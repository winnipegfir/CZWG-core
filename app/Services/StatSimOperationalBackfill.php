<?php

namespace App\Services;

use App\Models\Network\OperationalAirportSample;
use App\Models\Network\OperationalFlight;
use App\Models\Network\OperationalPositionSample;
use App\Models\Network\SessionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class StatSimOperationalBackfill
{
    public const AIRPORTS = OperationalOverviewCollector::AIRPORTS;

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        // getenv() deliberately bypasses Laravel's cached configuration. The
        // workflow supplies the secret only to the import process.
        $this->apiKey = (string) getenv('STATSIM_API_KEY');
        $this->baseUrl = rtrim((string) (getenv('STATSIM_API_URL') ?: 'https://api.statsim.net'), '/');
    }

    public function run(int $days, array $airports, bool $dryRun, callable $progress): array
    {
        $this->assertTablesExist();
        if (! $dryRun) {
            app(OperationalOverviewCollector::class)->purgeExpired();
        }
        $totals = ['returned' => 0, 'processed' => 0, 'skipped' => 0, 'positions' => 0, 'written' => 0];
        $end = now('UTC')->startOfDay();
        $start = $end->copy()->subDays($days);
        $sessions = $this->controllerSessions($start, $end);

        foreach ($airports as $airport) {
            for ($cursor = $start->copy(); $cursor->lt($end); $cursor->addDay()) {
                $from = $cursor->copy();
                $to = $cursor->copy()->addDay();
                $progress($airport.' '.$from->toDateString().': requesting flight list');
                $flights = $this->flightList($airport, $from, $to);
                $totals['returned'] += count($flights);
                $buckets = [];

                foreach ($flights as $index => $summary) {
                    $id = $this->value($summary, ['id', 'flightId']);
                    if ($id === null) {
                        $totals['skipped']++;
                        continue;
                    }

                    try {
                        $detail = $this->flightDetail($id);
                        $positions = $this->items($this->value($detail, ['positions', 'flightPositions'], []));
                        $metrics = $this->metrics($positions, $airport, (string) $id, $sessions, $buckets);
                        $totals['positions'] += count($positions);
                        if ($metrics['points'] < 2) {
                            $totals['skipped']++;
                            continue;
                        }

                        $totals['processed']++;
                        if (! $dryRun) {
                            $this->storeFlight($summary, $detail, $airport, $id, $metrics);
                            $totals['written']++;
                        }
                    } catch (\Throwable $exception) {
                        report($exception);
                        $totals['skipped']++;
                        $progress('  skipped StatSim flight '.$id.': '.$exception->getMessage());
                    }

                    if (($index + 1) % 25 === 0) {
                        $progress('  processed '.($index + 1).' of '.count($flights));
                    }
                    usleep(150000);
                }

                if (! $dryRun) {
                    $this->storeBuckets($airport, $buckets, $sessions);
                }
                $progress('  completed '.count($flights).' returned flight(s)');
            }
        }

        return $totals;
    }

    private function flightList(string $airport, Carbon $from, Carbon $to): array
    {
        $json = $this->request('/api/Flights/Icao', [
            'icao' => $airport,
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
        ], true);
        return $this->items($json);
    }

    private function flightDetail($id): array
    {
        $json = $this->request('/api/Flights/Id/'.rawurlencode((string) $id));
        $items = $this->items($json);
        return isset($json['positions']) || isset($json['flightPositions']) ? $json : ($items[0] ?? $json);
    }

    private function request(string $path, array $query = [], bool $notFoundIsEmpty = false): array
    {
        $response = Http::acceptJson()->withHeaders(['X-API-Key' => $this->apiKey])
            // Return the final response to us after retries so a StatSim 404
            // can be interpreted as an empty airport/day below. Other failed
            // responses are still thrown explicitly after that check.
            ->retry(3, 750, null, false)->timeout(30)->get($this->baseUrl.$path, $query);
        // StatSim uses 404 for a valid date/airport query with no matching
        // flights. That is an empty day, not a failed backfill.
        if ($notFoundIsEmpty && $response->status() === 404) return [];
        $response->throw();
        $json = $response->json();
        if (! is_array($json)) {
            throw new \RuntimeException('StatSim returned an unexpected response.');
        }
        return $json;
    }

    private function items(array $payload): array
    {
        foreach (['data', 'items', 'results', 'flights'] as $wrapper) {
            if (isset($payload[$wrapper]) && is_array($payload[$wrapper])) return array_values($payload[$wrapper]);
        }
        return array_is_list($payload) ? $payload : [];
    }

    private function metrics(array $positions, string $airport, string $flightId, $sessions, array &$buckets): array
    {
        $ground = $airborne = $controlledGround = $controlledAirborne = 0;
        $first = $last = null;
        $previous = null;
        $points = 0;

        usort($positions, fn ($a, $b) => strcmp((string) $this->value($a, ['time', 'timestamp'], ''), (string) $this->value($b, ['time', 'timestamp'], '')));
        foreach ($positions as $position) {
            $timeValue = $this->value($position, ['time', 'timestamp', 'observedAt']);
            $lat = $this->value($position, ['latitude', 'lat']);
            $lon = $this->value($position, ['longitude', 'lon', 'lng']);
            if ($timeValue === null || ! is_numeric($lat) || ! is_numeric($lon)) continue;
            $time = Carbon::parse($timeValue)->utc();
            $distance = $this->distanceNm((float) $lat, (float) $lon, self::AIRPORTS[$airport]['lat'], self::AIRPORTS[$airport]['lon']);
            if ($distance > 45) { $previous = null; continue; }
            $speed = (int) $this->value($position, ['speed', 'groundSpeed', 'groundspeed'], 0);
            $phase = $distance <= 5 && $speed <= 55 ? 'ground' : 'airborne';
            $controlled = $this->isControlled($sessions, $airport, $phase, $distance, $time);
            $bucket = $time->copy()->startOfMinute();
            $bucket->subMinutes($bucket->minute % OperationalOverviewCollector::SAMPLE_MINUTES);
            $key = $bucket->format('Y-m-d H:i:s');
            // A flight may contain several position reports in one five-minute
            // bucket. Count the aircraft once, not once per position report.
            // Keep one representative state per flight in each bucket. Later
            // reports replace earlier ones, which avoids counting the same
            // aircraft as both ground and airborne during a transition.
            $buckets[$key]['flights'][$flightId] = ['phase' => $phase, 'controlled' => $controlled];
            $first = $first ? $first->min($time) : $time->copy();
            $last = $last ? $last->max($time) : $time->copy();
            $points++;

            if ($previous) {
                $seconds = min(600, max(0, $previous['time']->diffInSeconds($time)));
                if ($previous['phase'] === 'ground') {
                    $ground += $seconds;
                    if ($previous['controlled']) $controlledGround += $seconds;
                } else {
                    $airborne += $seconds;
                    if ($previous['controlled']) $controlledAirborne += $seconds;
                }
            }
            $previous = ['time' => $time, 'phase' => $phase, 'controlled' => $controlled];
        }

        return compact('ground', 'airborne', 'controlledGround', 'controlledAirborne', 'first', 'last', 'points');
    }

    private function storeFlight(array $summary, array $detail, string $airport, $id, array $metrics): void
    {
        $source = array_merge($summary, $detail);
        OperationalFlight::updateOrCreate(['session_key' => 'statsim:'.$id.':'.$airport], [
            'pilot_cid' => $this->value($source, ['vatsimid', 'vatsimId', 'pilotCid']),
            'callsign' => strtoupper((string) $this->value($source, ['callsign'], 'UNKNOWN')),
            'departure' => strtoupper((string) $this->value($source, ['departure', 'origin'])) ?: null,
            'arrival' => strtoupper((string) $this->value($source, ['destination', 'arrival'])) ?: null,
            'airport' => $airport,
            'first_seen_at' => $metrics['first'], 'last_seen_at' => $metrics['last'],
            'completed_at' => $metrics['last'], 'last_sampled_at' => $metrics['last'],
            'last_phase' => 'completed', 'last_controlled' => false,
            'ground_seconds' => $metrics['ground'], 'airborne_seconds' => $metrics['airborne'],
            'controlled_ground_seconds' => $metrics['controlledGround'],
            'controlled_airborne_seconds' => $metrics['controlledAirborne'],
            'emergency_observed' => false,
        ]);
    }

    private function storeBuckets(string $airport, array $buckets, $sessions): void
    {
        DB::transaction(function () use ($airport, $buckets, $sessions) {
            foreach ($buckets as $sampledAt => $counts) {
                $time = Carbon::parse($sampledAt, 'UTC');
                $active = $this->activeSessions($sessions, $airport, $time);
                $flights = collect($counts['flights'] ?? []);
                $ground = $flights->where('phase', 'ground');
                $airborne = $flights->where('phase', 'airborne');
                OperationalAirportSample::updateOrCreate(['sampled_at' => $time, 'airport' => $airport], [
                    'ground_aircraft' => $ground->count(),
                    'airborne_aircraft' => $airborne->count(),
                    'controlled_ground_aircraft' => $ground->where('controlled', true)->count(),
                    'controlled_airborne_aircraft' => $airborne->where('controlled', true)->count(),
                    'online_positions' => $active->count(),
                    'active_positions' => $active->count(),
                ]);
                foreach ($active as $session) {
                    $type = $this->positionType($session->callsign);
                    $relevant = in_array($type, ['DEL', 'GND'], true)
                        ? $ground->count() : $airborne->count();
                    OperationalPositionSample::updateOrCreate(
                        ['sampled_at' => $time, 'airport' => $airport, 'callsign' => strtoupper((string) $session->callsign)],
                        ['controller_cid' => $session->cid, 'position_type' => $type, 'relevant_aircraft' => $relevant]
                    );
                }
            }
        });
    }

    private function controllerSessions(Carbon $start, Carbon $end)
    {
        if (! Schema::hasTable('session_logs')) return collect();
        return SessionLog::where('session_start', '<=', $end)
            ->where(fn ($q) => $q->whereNull('session_end')->orWhere('session_end', '>=', $start))->get();
    }

    private function activeSessions($sessions, string $airport, Carbon $time)
    {
        return $sessions->filter(function ($session) use ($airport, $time) {
            $callsign = strtoupper((string) $session->callsign);
            $relevant = str_starts_with($callsign, $airport.'_') || str_starts_with($callsign, 'CZWG_') || str_starts_with($callsign, 'WPG_') || str_starts_with($callsign, 'ZWG_');
            return $relevant && Carbon::parse($session->session_start)->lte($time)
                && Carbon::parse($session->session_end ?: now())->gte($time);
        })->unique(fn ($session) => strtoupper((string) $session->callsign))->values();
    }

    private function isControlled($sessions, string $airport, string $phase, float $distance, Carbon $time): bool
    {
        return $this->activeSessions($sessions, $airport, $time)->contains(function ($session) use ($phase, $distance) {
            $type = $this->positionType($session->callsign);
            if ($phase === 'ground') return in_array($type, ['DEL', 'GND', 'TWR', 'APP', 'DEP', 'TML', 'CTR', 'FSS'], true);
            return in_array($type, ['APP', 'DEP', 'TML', 'CTR', 'FSS'], true) || ($type === 'TWR' && $distance <= 10);
        });
    }

    private function positionType(?string $callsign): string
    {
        $callsign = strtoupper((string) $callsign);
        $type = str_contains($callsign, '_') ? (string) substr($callsign, strrpos($callsign, '_') + 1) : '';
        return in_array($type, ['DEL', 'GND', 'TWR', 'APP', 'DEP', 'TML', 'CTR', 'FSS'], true) ? $type : 'OTHER';
    }

    private function value(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) if (array_key_exists($key, $data)) return $data[$key];
        return $default;
    }

    private function assertTablesExist(): void
    {
        foreach (['network_operational_airport_samples', 'network_operational_position_samples', 'network_operational_flights'] as $table) {
            if (! Schema::hasTable($table)) throw new \RuntimeException('Required table '.$table.' is missing; deploy the Operational Overview migrations first.');
        }
    }

    private function distanceNm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $a = sin(deg2rad($lat2 - $lat1) / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($lon2 - $lon1) / 2) ** 2;
        return 3440.065 * 2 * atan2(sqrt($a), sqrt(max(0, 1 - $a)));
    }
}
