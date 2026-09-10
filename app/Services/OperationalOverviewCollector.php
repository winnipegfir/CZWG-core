<?php

namespace App\Services;

use App\Classes\HttpHelper;
use App\Classes\VatsimHelper;
use App\Models\Network\OperationalAirportSample;
use App\Models\Network\OperationalEmergency;
use App\Models\Network\OperationalFlight;
use App\Models\Network\OperationalPositionSample;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationalOverviewCollector
{
    public const SAMPLE_MINUTES = 5;

    public const AIRPORTS = [
        'CYWG' => ['name' => 'Winnipeg', 'lat' => 49.9100, 'lon' => -97.2399],
        'CYXE' => ['name' => 'Saskatoon', 'lat' => 52.1708, 'lon' => -106.6997],
        'CYQR' => ['name' => 'Regina', 'lat' => 50.4319, 'lon' => -104.6658],
        'CYQT' => ['name' => 'Thunder Bay', 'lat' => 48.3719, 'lon' => -89.3239],
    ];

    public function collect(): array
    {
        $feed = HttpHelper::getClient()->get(VatsimHelper::getDatafeedUrl())->object();
        $observedAt = now()->startOfSecond();
        $sampledAt = $observedAt->copy()->startOfMinute();
        $sampledAt->subMinutes($sampledAt->minute % self::SAMPLE_MINUTES);

        $controllers = collect($feed->controllers ?? [])->map(fn ($controller) => $this->controllerContext($controller))
            ->filter()->values();
        $traffic = collect(array_keys(self::AIRPORTS))->mapWithKeys(fn ($airport) => [$airport => ['ground' => [], 'airborne' => []]])->all();

        foreach (($feed->pilots ?? []) as $pilot) {
            $airport = $this->airportForPilot($pilot);
            if (! $airport) {
                continue;
            }

            $distance = $this->distanceNm((float) $pilot->latitude, (float) $pilot->longitude, self::AIRPORTS[$airport]['lat'], self::AIRPORTS[$airport]['lon']);
            $phase = $distance <= 5 && (int) ($pilot->groundspeed ?? 0) <= 55 ? 'ground' : 'airborne';
            $traffic[$airport][$phase][] = ['pilot' => $pilot, 'distance' => $distance];
        }

        $seenFlightKeys = [];
        DB::transaction(function () use ($sampledAt, $observedAt, $controllers, $traffic, &$seenFlightKeys) {
            foreach (self::AIRPORTS as $airport => $details) {
                $applicable = $controllers->filter(fn ($controller) => $controller['airport'] === null || $controller['airport'] === $airport)->values();
                $ground = count($traffic[$airport]['ground']);
                $airborne = count($traffic[$airport]['airborne']);
                $groundCovered = $this->controlledTrafficCount($applicable, 'ground', $traffic[$airport]['ground']);
                $airborneCovered = $this->controlledTrafficCount($applicable, 'airborne', $traffic[$airport]['airborne']);

                $positionRows = $applicable->map(function ($controller) use ($sampledAt, $airport, $traffic) {
                    $relevant = $this->relevantTraffic($controller['type'], $traffic[$airport]['ground'], $traffic[$airport]['airborne']);
                    return [
                        'sampled_at' => $sampledAt,
                        'airport' => $airport,
                        'callsign' => $controller['callsign'],
                        'controller_cid' => $controller['cid'],
                        'position_type' => $controller['type'],
                        'relevant_aircraft' => $relevant,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                OperationalAirportSample::updateOrCreate(
                    ['sampled_at' => $sampledAt, 'airport' => $airport],
                    [
                        'ground_aircraft' => $ground,
                        'airborne_aircraft' => $airborne,
                        'controlled_ground_aircraft' => $groundCovered,
                        'controlled_airborne_aircraft' => $airborneCovered,
                        'online_positions' => $applicable->count(),
                        'active_positions' => $positionRows->where('relevant_aircraft', '>', 0)->count(),
                    ]
                );

                foreach ($positionRows as $row) {
                    OperationalPositionSample::updateOrCreate(
                        ['sampled_at' => $sampledAt, 'airport' => $airport, 'callsign' => $row['callsign']],
                        $row
                    );
                }

                foreach (array_merge($traffic[$airport]['ground'], $traffic[$airport]['airborne']) as $aircraft) {
                    $phase = $aircraft['distance'] <= 5 && (int) ($aircraft['pilot']->groundspeed ?? 0) <= 55 ? 'ground' : 'airborne';
                    $controlled = $this->controlledTrafficCount($applicable, $phase, [$aircraft]) > 0;
                    $seenFlightKeys[] = $this->trackFlight($aircraft['pilot'], $airport, $phase, $controlled, $observedAt);
                }
            }


            OperationalFlight::whereNull('completed_at')
                ->where('last_seen_at', '<', $observedAt->copy()->subMinutes(10))
                ->update(['completed_at' => $observedAt, 'updated_at' => now()]);

            OperationalAirportSample::where('sampled_at', '<', now()->subDays(180))->delete();
            OperationalPositionSample::where('sampled_at', '<', now()->subDays(180))->delete();
            OperationalFlight::where('last_seen_at', '<', now()->subDays(180))->delete();
            OperationalEmergency::where('last_seen_at', '<', now()->subDays(180))->delete();
        });

        return [
            'sampled_at' => $observedAt,
            'controllers' => $controllers->count(),
            'tracked_aircraft' => collect($traffic)->sum(fn ($phases) => count($phases['ground']) + count($phases['airborne'])),
            'tracked_flights' => count(array_unique($seenFlightKeys)),
        ];
    }

    private function trackFlight(object $pilot, string $airport, string $phase, bool $controlled, Carbon $sampledAt): string
    {
        $cid = isset($pilot->cid) ? (int) $pilot->cid : null;
        $callsign = strtoupper((string) ($pilot->callsign ?? 'UNKNOWN'));
        $connectedAt = (string) ($pilot->logon_time ?? $pilot->last_updated ?? $sampledAt->toIso8601String());
        $sessionKey = hash('sha256', ($cid ?: $callsign).'|'.$callsign.'|'.$connectedAt.'|'.$airport);
        $flight = OperationalFlight::firstOrNew(['session_key' => $sessionKey]);
        $elapsed = 0;

        if ($flight->exists && $flight->last_sampled_at) {
            $elapsed = max(0, min(600, $flight->last_sampled_at->diffInSeconds($sampledAt)));
        }

        if ($elapsed > 0) {
            $durationField = $phase === 'ground' ? 'ground_seconds' : 'airborne_seconds';
            $flight->{$durationField} = (int) $flight->{$durationField} + $elapsed;
            if ($controlled) {
                $controlledField = $phase === 'ground' ? 'controlled_ground_seconds' : 'controlled_airborne_seconds';
                $flight->{$controlledField} = (int) $flight->{$controlledField} + $elapsed;
            }
        }

        $squawk = str_pad((string) ($pilot->transponder ?? ''), 4, '0', STR_PAD_LEFT);
        $isEmergency = in_array($squawk, ['7500', '7600', '7700'], true);
        $flight->fill([
            'pilot_cid' => $cid,
            'callsign' => $callsign,
            'departure' => strtoupper((string) ($pilot->flight_plan->departure ?? '')) ?: null,
            'arrival' => strtoupper((string) ($pilot->flight_plan->arrival ?? '')) ?: null,
            'airport' => $airport,
            'first_seen_at' => $flight->first_seen_at ?: $sampledAt,
            'last_seen_at' => $sampledAt,
            'completed_at' => null,
            'last_sampled_at' => $sampledAt,
            'last_phase' => $phase,
            'last_controlled' => $controlled,
            'emergency_observed' => (bool) $flight->emergency_observed || $isEmergency,
        ])->save();

        if ($isEmergency) {
            OperationalEmergency::updateOrCreate(
                ['session_key' => $sessionKey, 'squawk' => $squawk],
                ['pilot_cid' => $cid, 'callsign' => $callsign, 'airport' => $airport,
                    'first_seen_at' => OperationalEmergency::where('session_key', $sessionKey)->where('squawk', $squawk)->value('first_seen_at') ?: $sampledAt,
                    'last_seen_at' => $sampledAt]
            );
        }

        return $sessionKey;
    }

    private function airportForPilot(object $pilot): ?string
    {
        if (! isset($pilot->latitude, $pilot->longitude)) {
            return null;
        }

        $departure = strtoupper((string) ($pilot->flight_plan->departure ?? ''));
        $arrival = strtoupper((string) ($pilot->flight_plan->arrival ?? ''));
        $best = null;
        $bestDistance = INF;

        foreach (self::AIRPORTS as $airport => $details) {
            $distance = $this->distanceNm((float) $pilot->latitude, (float) $pilot->longitude, $details['lat'], $details['lon']);
            $isLocalPlan = $departure === $airport || $arrival === $airport;
            if (($distance <= 5 || ($isLocalPlan && $distance <= 45)) && $distance < $bestDistance) {
                $best = $airport;
                $bestDistance = $distance;
            }
        }

        return $best;
    }

    private function controllerContext(object $controller): ?array
    {
        $callsign = strtoupper((string) ($controller->callsign ?? ''));
        if ($callsign === '' || str_ends_with($callsign, '_ATIS') || str_ends_with($callsign, '_OBS') || (int) ($controller->facility ?? 0) === 0) {
            return null;
        }

        $type = strtoupper((string) substr($callsign, strrpos($callsign, '_') + 1));
        $type = in_array($type, ['DEL', 'GND', 'TWR', 'DEP', 'APP', 'TML', 'CTR', 'FSS'], true) ? $type : 'OTHER';
        $airport = collect(array_keys(self::AIRPORTS))->first(fn ($icao) => str_starts_with($callsign, $icao.'_'));
        $isBroadWinnipeg = str_starts_with($callsign, 'CZWG_') || str_starts_with($callsign, 'WPG_') || str_starts_with($callsign, 'ZWG_');

        if (! $airport && ! $isBroadWinnipeg) {
            return null;
        }

        return ['callsign' => $callsign, 'cid' => isset($controller->cid) ? (int) $controller->cid : null, 'type' => $type, 'airport' => $airport];
    }

    private function controlledTrafficCount($controllers, string $phase, array $traffic): int
    {
        if ($phase === 'ground') {
            return $controllers->contains(fn ($controller) => in_array($controller['type'], ['DEL', 'GND', 'TWR', 'APP', 'DEP', 'TML', 'CTR', 'FSS'], true))
                ? count($traffic) : 0;
        }

        $broadCoverage = $controllers->contains(fn ($controller) => in_array($controller['type'], ['APP', 'DEP', 'TML', 'CTR', 'FSS'], true));
        $towerCoverage = $controllers->contains(fn ($controller) => $controller['type'] === 'TWR');

        return collect($traffic)->filter(fn ($aircraft) => $broadCoverage || ($towerCoverage && $aircraft['distance'] <= 10))->count();
    }

    private function relevantTraffic(string $type, array $ground, array $airborne): int
    {
        if (in_array($type, ['DEL', 'GND'], true)) return count($ground);
        if ($type === 'TWR') return count($ground) + collect($airborne)->where('distance', '<=', 10)->count();
        if (in_array($type, ['APP', 'DEP', 'TML', 'CTR', 'FSS'], true)) return count($airborne);
        return count($ground) + count($airborne);
    }

    private function distanceNm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusNm = 3440.065;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) ** 2;
        return $earthRadiusNm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
