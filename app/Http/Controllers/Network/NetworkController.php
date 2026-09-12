<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Classes\VatsimStatsApi;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\OperationalAirportSample;
use App\Models\Network\OperationalEmergency;
use App\Models\Network\OperationalFlight;
use App\Models\Network\OperationalPositionSample;
use App\Models\Network\SessionLog;
use App\Services\ControllerActivityService;
use App\Services\OperationalOverviewCollector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NetworkController extends Controller
{
    public function index()
    {
        return view('dashboard.network.index');
    }

    public function operationalOverview(Request $request)
    {
        $days = in_array((int) $request->get('days', 30), [7, 30, 90], true)
            ? (int) $request->get('days', 30)
            : 30;
        $start = Carbon::now()->subDays($days);

        $requiredTables = [
            'network_operational_airport_samples', 'network_operational_position_samples',
            'network_operational_flights', 'network_operational_emergencies',
        ];
        if (collect($requiredTables)->contains(fn ($table) => ! Schema::hasTable($table))) {
            $latestSample = null;
            $setupIncomplete = true;
            return view('dashboard.network.operations.index', compact('days', 'latestSample', 'setupIncomplete'));
        }

        $setupIncomplete = false;

        // Production does not currently run Laravel's scheduler. Let the first
        // staff visit in each five-minute window collect one real VATSIM sample.
        // Cache::add is atomic on Laravel's supported cache stores, preventing a
        // burst of staff page loads from making duplicate feed requests.
        if (Cache::add('network.operational-overview.collect', true, now()->addMinutes(5))) {
            try {
                app(OperationalOverviewCollector::class)->collect();
            } catch (\Throwable $exception) {
                report($exception);
                Cache::forget('network.operational-overview.collect');
            }
        }

        $airportRows = OperationalAirportSample::where('sampled_at', '>=', $start)
            ->selectRaw('airport, COUNT(*) AS sample_count, SUM(ground_aircraft) AS ground_observations, SUM(airborne_aircraft) AS airborne_observations, SUM(controlled_ground_aircraft) AS controlled_ground, SUM(controlled_airborne_aircraft) AS controlled_airborne, SUM(CASE WHEN online_positions > 0 THEN 1 ELSE 0 END) AS staffed_samples, SUM(CASE WHEN ground_aircraft + airborne_aircraft > 0 THEN 1 ELSE 0 END) AS traffic_samples, SUM(CASE WHEN ground_aircraft + airborne_aircraft > 0 AND online_positions = 0 THEN 1 ELSE 0 END) AS uncovered_samples, MAX(sampled_at) AS latest_sample')
            ->groupBy('airport')
            ->get()->keyBy('airport');

        $airports = collect(OperationalOverviewCollector::AIRPORTS)->map(function ($details, $icao) use ($airportRows) {
            $row = $airportRows->get($icao);
            $ground = (int) ($row->ground_observations ?? 0);
            $airborne = (int) ($row->airborne_observations ?? 0);
            $controlled = (int) ($row->controlled_ground ?? 0) + (int) ($row->controlled_airborne ?? 0);
            $observations = $ground + $airborne;

            return (object) [
                'icao' => $icao,
                'name' => $details['name'],
                'photo' => $details['photo'],
                'photo_credit' => $details['photo_credit'],
                'photo_license' => $details['photo_license'],
                'photo_source' => $details['photo_source'],
                'sample_count' => (int) ($row->sample_count ?? 0),
                'ground_minutes' => $ground * OperationalOverviewCollector::SAMPLE_MINUTES,
                'airborne_minutes' => $airborne * OperationalOverviewCollector::SAMPLE_MINUTES,
                'coverage' => $observations > 0 ? round(($controlled / $observations) * 100, 1) : null,
                'staffed_time' => (int) ($row->staffed_samples ?? 0) * OperationalOverviewCollector::SAMPLE_MINUTES,
                'traffic_time' => (int) ($row->traffic_samples ?? 0) * OperationalOverviewCollector::SAMPLE_MINUTES,
                'uncovered_time' => (int) ($row->uncovered_samples ?? 0) * OperationalOverviewCollector::SAMPLE_MINUTES,
                'latest_sample' => $row?->latest_sample,
            ];
        })->values();

        // Present operational roles rather than raw callsign suffixes. APP,
        // DEP, and TML are one Terminal function for planning purposes. Broad
        // Winnipeg positions are stored once per airport for top-down coverage,
        // so de-duplicate those copies before calculating FIR utilization.
        $validPositionTypes = ['DEL', 'GND', 'TWR', 'APP', 'DEP', 'TML', 'CTR'];
        $positionSamples = OperationalPositionSample::where('sampled_at', '>=', $start)
            ->whereIn('position_type', $validPositionTypes)->get()
            ->groupBy(function ($sample) {
                $callsign = strtoupper((string) $sample->callsign);
                $scope = $this->isFirCallsign($callsign) ? 'FIR' : strtoupper((string) $sample->airport);
                return $scope.'|'.$this->positionGroup($sample->position_type).'|'.Carbon::parse($sample->sampled_at)->format('Y-m-d H:i:s').'|'.$callsign;
            })->map(function ($copies) {
                $sample = $copies->first();
                $callsign = strtoupper((string) $sample->callsign);
                return (object) [
                    'airport' => $this->isFirCallsign($callsign) ? 'FIR' : strtoupper((string) $sample->airport),
                    'position_type' => $this->positionGroup($sample->position_type),
                    'relevant_aircraft' => $copies->sum('relevant_aircraft'),
                ];
            });
        $positionTypes = $positionSamples
            ->groupBy(fn ($sample) => $sample->airport.'|'.$sample->position_type)
            ->map(function ($samples) {
                $staffed = $samples->count();
                $active = $samples->where('relevant_aircraft', '>', 0)->count();
                return (object) [
                    'airport' => $samples->first()->airport,
                    'position_type' => $samples->first()->position_type,
                    'staffed_samples' => $staffed,
                    'active_samples' => $active,
                    'traffic_observations' => $samples->sum('relevant_aircraft'),
                    'staffed_minutes' => $staffed * OperationalOverviewCollector::SAMPLE_MINUTES,
                    'active_minutes' => $active * OperationalOverviewCollector::SAMPLE_MINUTES,
                    'utilization' => $staffed > 0 ? round(($active / $staffed) * 100, 1) : null,
                ];
            })->sortBy(fn ($position) => $this->positionSortKey($position->airport, $position->position_type))->values();

        // Compare sampled workload with each controller's current roster rating.
        // Broad top-down positions have one database row per tracked airport;
        // collapse those copies before calculating rating totals. Center is kept
        // separate so frequent CTR coverage cannot hide local-position gaps.
        $ratingSourceSamples = OperationalPositionSample::where('sampled_at', '>=', $start)
            ->whereNotNull('controller_cid')->whereIn('position_type', $validPositionTypes)->get()
            ->groupBy(function ($sample) {
                $callsign = strtoupper((string) $sample->callsign);
                $scope = $this->isFirCallsign($callsign) ? 'FIR' : strtoupper((string) $sample->airport);
                return $scope.'|'.Carbon::parse($sample->sampled_at)->format('Y-m-d H:i:s').'|'.$callsign.'|'.$sample->controller_cid;
            })->map(function ($copies) {
                $sample = $copies->first();
                return (object) [
                    'controller_cid' => (string) $sample->controller_cid,
                    'scope' => strtoupper((string) $sample->position_type) === 'CTR' ? 'Center' : 'Local & Terminal',
                    'relevant_aircraft' => $copies->sum('relevant_aircraft'),
                ];
            })->values();
        $ratingRoster = RosterMember::whereIn('cid', $ratingSourceSamples->pluck('controller_cid')->unique())
            ->get()->keyBy(fn ($member) => (string) $member->cid);
        $ratingSamples = $ratingSourceSamples->map(function ($sample) use ($ratingRoster) {
            $member = $ratingRoster->get($sample->controller_cid);
            $sample->rating_group = $this->ratingGroup($member?->rating);
            return $sample;
        });
        $ratingGroups = ['S1', 'S2', 'S3', 'C1+'];
        if ($ratingSamples->contains('rating_group', 'Unmatched')) $ratingGroups[] = 'Unmatched';
        $ratingContribution = collect(['Local & Terminal', 'Center'])->flatMap(function ($scope) use ($ratingGroups, $ratingSamples) {
            return collect($ratingGroups)->map(function ($rating) use ($scope, $ratingSamples) {
                $samples = $ratingSamples->where('scope', $scope)->where('rating_group', $rating);
                $staffed = $samples->count();
                $active = $samples->where('relevant_aircraft', '>', 0)->count();
                return (object) [
                    'scope' => $scope,
                    'rating' => $rating,
                    'controllers' => $samples->pluck('controller_cid')->unique()->count(),
                    'staffed_minutes' => $staffed * OperationalOverviewCollector::SAMPLE_MINUTES,
                    'active_minutes' => $active * OperationalOverviewCollector::SAMPLE_MINUTES,
                    'traffic_observations' => $samples->sum('relevant_aircraft'),
                    'utilization' => $staffed > 0 ? round(($active / $staffed) * 100, 1) : null,
                ];
            });
        })->values();

        $latestSample = OperationalAirportSample::max('sampled_at');

        $flightRows = OperationalFlight::where('last_seen_at', '>=', $start)->get();
        $flightTotals = (object) [
            'count' => $flightRows->count(),
            'ground_minutes' => (int) round($flightRows->sum('ground_seconds') / 60),
            'airborne_minutes' => (int) round($flightRows->sum('airborne_seconds') / 60),
            'controlled_minutes' => (int) round(($flightRows->sum('controlled_ground_seconds') + $flightRows->sum('controlled_airborne_seconds')) / 60),
        ];
        $allFlightSeconds = $flightRows->sum('ground_seconds') + $flightRows->sum('airborne_seconds');
        $flightTotals->coverage = $allFlightSeconds > 0
            ? round((($flightRows->sum('controlled_ground_seconds') + $flightRows->sum('controlled_airborne_seconds')) / $allFlightSeconds) * 100, 1)
            : null;
        $flightTotals->average_ground_minutes = $flightRows->count() > 0 ? round($flightTotals->ground_minutes / $flightRows->count(), 1) : null;
        $flightTotals->average_airborne_minutes = $flightRows->count() > 0 ? round($flightTotals->airborne_minutes / $flightRows->count(), 1) : null;

        $recentFlights = $flightRows->sortByDesc('last_seen_at')->map(function ($flight) {
            $total = $flight->ground_seconds + $flight->airborne_seconds;
            $controlled = $flight->controlled_ground_seconds + $flight->controlled_airborne_seconds;
            $flight->coverage = $total > 0 ? round(($controlled / $total) * 100, 1) : null;
            return $flight;
        });

        $emergencies = OperationalEmergency::where('last_seen_at', '>=', $start)
            ->orderByDesc('last_seen_at')->limit(25)->get();

        $controllerSamples = OperationalPositionSample::where('sampled_at', '>=', $start)
            ->whereNotNull('controller_cid')
            ->selectRaw('controller_cid, COUNT(*) AS staffed_samples, SUM(CASE WHEN relevant_aircraft > 0 THEN 1 ELSE 0 END) AS active_samples')
            ->groupBy('controller_cid')->get();
        $rosterByCid = RosterMember::whereIn('cid', $controllerSamples->pluck('controller_cid'))->get()->keyBy('cid');
        $requiredHours = 0.0;
        foreach ($controllerSamples as $sample) {
            $member = $rosterByCid->get($sample->controller_cid);
            $quarterRequirement = $member ? config('currency.'.$member->status) : null;
            if (is_numeric($quarterRequirement)) {
                $requiredHours += ((float) $quarterRequirement) * ($days / 90);
            }
        }
        $staffedHours = $controllerSamples->sum('staffed_samples') * OperationalOverviewCollector::SAMPLE_MINUTES / 60;
        $activeHours = $controllerSamples->sum('active_samples') * OperationalOverviewCollector::SAMPLE_MINUTES / 60;
        $rosterEfficiency = (object) [
            'controllers_observed' => $controllerSamples->count(),
            'staffed_hours' => round($staffedHours, 1),
            'active_hours' => round($activeHours, 1),
            'traffic_utilization' => $staffedHours > 0 ? round(($activeHours / $staffedHours) * 100, 1) : null,
            'prorated_requirement_hours' => round($requiredHours, 1),
            'requirement_delivery' => $requiredHours > 0 ? round(($staffedHours / $requiredHours) * 100, 1) : null,
        ];

        $trainingOpportunities = $airports->map(function ($airport) {
            $gap = $airport->uncovered_time;
            return (object) ['airport' => $airport->icao, 'name' => $airport->name, 'uncovered_minutes' => $gap, 'coverage' => $airport->coverage];
        })->sortByDesc('uncovered_minutes')->values();

        $hourlyTraffic = OperationalAirportSample::where('sampled_at', '>=', $start)
            ->selectRaw('airport, HOUR(sampled_at) AS hour_utc, SUM(ground_aircraft + airborne_aircraft) AS traffic_observations')
            ->groupBy('airport', DB::raw('HOUR(sampled_at)'))->get();
        $peakTimes = collect(OperationalOverviewCollector::AIRPORTS)->map(function ($details, $icao) use ($hourlyTraffic) {
            $hours = array_fill(0, 24, 0);
            foreach ($hourlyTraffic->where('airport', $icao) as $row) {
                $hours[(int) $row->hour_utc] = (int) $row->traffic_observations;
            }

            $bestStart = 0;
            $bestTraffic = 0;
            for ($hour = 0; $hour < 24; $hour++) {
                $traffic = $hours[$hour] + $hours[($hour + 1) % 24] + $hours[($hour + 2) % 24];
                if ($traffic > $bestTraffic) {
                    $bestTraffic = $traffic;
                    $bestStart = $hour;
                }
            }

            return (object) [
                'airport' => $icao,
                'name' => $details['name'],
                'window' => $bestTraffic > 0
                    ? sprintf('%02d:00–%02d:00Z', $bestStart, ($bestStart + 3) % 24)
                    : null,
                'traffic_observations' => $bestTraffic,
            ];
        })->values();

        // The session archive is an independent staffing view, not an addition
        // to sampled utilization. Show its full selected range; the former
        // pre-sample cutoff made a populated archive appear almost empty.
        $historicalStaffing = SessionLog::where('session_start', '<=', now())
            ->where(function ($query) use ($start) {
                $query->whereNull('session_end')->orWhere('session_end', '>=', $start);
            })->get()->map(function ($session) use ($start) {
                $from = Carbon::parse($session->session_start)->max($start);
                $to = Carbon::parse($session->session_end ?: now())->min(now());
                $callsign = strtoupper((string) $session->callsign);
                $rawType = str_contains($callsign, '_') ? substr($callsign, strrpos($callsign, '_') + 1) : '';
                $position = $this->positionGroup($rawType);
                $airport = $this->positionAirport($callsign);

                return (object) [
                    'airport' => $airport,
                    'position_type' => $position,
                    'callsign' => $callsign,
                    'started_at' => Carbon::parse($session->session_start),
                    'minutes' => $to->gt($from) ? $from->diffInMinutes($to) : 0,
                ];
            })->filter(fn ($row) => $row->minutes > 0 && $row->airport !== null && $row->position_type !== null)
            ->sortByDesc('started_at')->values();

        return view('dashboard.network.operations.index', compact(
            'airports', 'positionTypes', 'days', 'latestSample', 'flightTotals', 'recentFlights',
            'emergencies', 'rosterEfficiency', 'trainingOpportunities', 'peakTimes', 'historicalStaffing',
            'ratingContribution', 'setupIncomplete'
        ));
    }

    private function positionGroup(?string $positionType): ?string
    {
        return match (strtoupper((string) $positionType)) {
            'DEL' => 'Delivery',
            'GND' => 'Ground',
            'TWR' => 'Tower',
            'APP', 'DEP', 'TML' => 'Terminal',
            'CTR' => 'Center',
            default => null,
        };
    }

    private function isFirCallsign(string $callsign): bool
    {
        return str_starts_with($callsign, 'CZWG_') || str_starts_with($callsign, 'WPG_') || str_starts_with($callsign, 'ZWG_');
    }

    private function positionAirport(string $callsign): ?string
    {
        foreach (array_keys(OperationalOverviewCollector::AIRPORTS) as $icao) {
            if (str_starts_with($callsign, $icao.'_')) return $icao;
        }
        return $this->isFirCallsign($callsign) ? 'FIR' : null;
    }

    private function positionSortKey(string $airport, string $position): string
    {
        $airportOrder = array_flip(array_merge(array_keys(OperationalOverviewCollector::AIRPORTS), ['FIR']));
        $positionOrder = array_flip(['Delivery', 'Ground', 'Tower', 'Terminal', 'Center']);
        return sprintf('%02d|%02d', $airportOrder[$airport] ?? 99, $positionOrder[$position] ?? 99);
    }

    private function ratingGroup(?string $rating): string
    {
        $rating = strtoupper(trim((string) $rating));
        if (in_array($rating, ['S1', 'S2', 'S3'], true)) return $rating;
        if (in_array($rating, ['C1', 'C2', 'C3', 'I1', 'I2', 'I3', 'SUP', 'ADM'], true)) return 'C1+';
        return 'Unmatched';
    }

    public function activityIndex(Request $request)
    {
        $now = Carbon::now();
        $quarterLabel = 'Q'.ceil($now->month / 3).' '.$now->year;
        $defaultStart = $now->copy()->startOfQuarter();
        $defaultEnd = $now->copy();

        $rangeStart = $request->filled('start') ? Carbon::parse($request->get('start'))->startOfDay() : $defaultStart;
        $rangeEnd = $request->filled('end') ? Carbon::parse($request->get('end'))->endOfDay() : $defaultEnd;
        $isCustomRange = $request->filled('start') || $request->filled('end');

        // Let the cache-warm cron know this range is actually being looked at, so it
        // backs it in the background instead of leaving it to fight for live-load
        // rate-limit budget on every reload.
        if ($isCustomRange) {
            VatsimStatsApi::rememberRangeStart($rangeStart);
        }

        // Not filtered by active/inactive -- this should match everyone the Roster
        // admin page shows, so a pending/inactive member isn't invisible here just
        // because they haven't been flipped active yet.
        $roster = RosterMember::whereIn('status', ['home', 'visit', 'instructor', 'training'])
            ->with('user')
            ->get();

        $members = ControllerActivityService::compute($roster, $rangeStart, $rangeEnd)
            ->sortBy('total_logged_hours')
            ->values();

        $totalMembers = $members->count();
        $meetingRequirement = $members->where('meets_requirement', true)->count();
        $belowRequirement = $members->where('meets_requirement', false)->count();
        $dataUnavailable = $members->where('vatsim_data_unavailable', true)->count();
        $notOnVatcan = $members->where('vatcan_status', 'none')->count();

        // Home controllers, instructors, and trainees all belong to the FIR itself;
        // visitors are the only status held to the flat hour minimum with no FIR-share rule.
        $homeMembers = $members->whereIn('status', ['home', 'instructor', 'training'])->values();
        $visitingMembers = $members->where('status', 'visit')->values();

        return view('dashboard.network.activity.index', compact(
            'homeMembers', 'visitingMembers', 'quarterLabel', 'totalMembers', 'meetingRequirement', 'belowRequirement', 'dataUnavailable', 'notOnVatcan',
            'rangeStart', 'rangeEnd', 'isCustomRange'
        ));
    }

    public function monitoredPositionsIndex()
    {
        $positions = MonitoredPosition::all()->sortByDesc('identifier');

        return view('dashboard.network.monitoredpositions.index', compact('positions'));
    }

    public function viewMonitoredPosition($position)
    {
        $position = MonitoredPosition::where(strtolower('identifier'), strtolower($position))->firstOrFail();

        return view('dashboard.network.monitoredpositions.view', compact('position'));
    }

    public function createMonitoredPosition(Request $request)
    {
        $messages = [
            'identifier.required' => 'Please type an identifier prefix/callsign.',
        ];

        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createMonitoredPosition');
        }

        $position = new MonitoredPosition();
        $position->identifier = $request->get('identifier');
        $position->save();

        return redirect()->route('network.monitoredpositions.view', strtolower($position->identifier));
    }
}
