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
use App\Services\ControllerActivityService;
use App\Services\OperationalOverviewCollector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $positionTypes = OperationalPositionSample::where('sampled_at', '>=', $start)
            ->selectRaw('airport, position_type, COUNT(*) AS staffed_samples, SUM(CASE WHEN relevant_aircraft > 0 THEN 1 ELSE 0 END) AS active_samples, SUM(relevant_aircraft) AS traffic_observations')
            ->groupBy('airport', 'position_type')->orderBy('airport')->orderBy('position_type')->get()
            ->map(function ($row) {
                $staffed = (int) $row->staffed_samples;
                $active = (int) $row->active_samples;
                $row->staffed_minutes = $staffed * OperationalOverviewCollector::SAMPLE_MINUTES;
                $row->active_minutes = $active * OperationalOverviewCollector::SAMPLE_MINUTES;
                $row->utilization = $staffed > 0 ? round(($active / $staffed) * 100, 1) : null;
                return $row;
            });

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

        $recentFlights = $flightRows->sortByDesc('last_seen_at')->take(15)->map(function ($flight) {
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

        return view('dashboard.network.operations.index', compact(
            'airports', 'positionTypes', 'days', 'latestSample', 'flightTotals', 'recentFlights',
            'emergencies', 'rosterEfficiency', 'trainingOpportunities'
        ));
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
