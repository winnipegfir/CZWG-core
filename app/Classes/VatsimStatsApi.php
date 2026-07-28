<?php

namespace App\Classes;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VatsimStatsApi
{
    // VATSIM's own authoritative ATC session history per CID. Unlike our own
    // activity bot (which only ever logs positions matching this FIR's callsign
    // prefixes), this includes every session a controller has worked anywhere
    // on the network -- which is what lets us detect out-of-FIR hours at all.
    const BASE_URL = 'https://api.vatsim.net/api/ratings/';

    /**
     * Fetch ATC sessions starting on/after $start for each cid.
     *
     * @param  \Illuminate\Support\Collection<int, int|string>  $cids
     * @return array<string, \Illuminate\Support\Collection|null> keyed by cid; null means the fetch failed
     */
    // A never-before-cached date range (e.g. a custom "last quarter" pick) means every
    // roster member misses cache at once. Firing all of them at VATSIM in a single burst
    // trips their rate limiting / 500-while-online bug for the whole batch, so we fetch
    // in smaller waves instead and give the ones that failed one retry pass at the end.
    const BATCH_SIZE = 20;

    /**
     * Fetch ATC sessions starting on/after $start for each cid.
     *
     * @param  \Illuminate\Support\Collection<int, int|string>  $cids
     * @return array<string, \Illuminate\Support\Collection|null> keyed by cid; null means the fetch failed
     */
    public static function getAtcSessionsForMembers($cids, Carbon $start): array
    {
        $dateKey = $start->format('Y-m-d');
        $result = [];
        $toFetch = collect();

        foreach ($cids as $cid) {
            $cid = (string) $cid;
            $cacheKey = "vatsim.atcsessions.{$cid}.{$dateKey}";
            if (Cache::has($cacheKey)) {
                $result[$cid] = Cache::get($cacheKey);
            } else {
                $toFetch->push($cid);
            }
        }

        foreach ($toFetch->chunk(self::BATCH_SIZE) as $batch) {
            self::fetchBatch($batch, $dateKey, $result);
        }

        // One retry pass for anything that failed, in case it was a transient
        // burst/rate-limit failure rather than a real per-CID problem.
        $failedCids = collect($result)->filter(fn ($sessions) => $sessions === null)->keys();
        foreach ($failedCids->chunk(self::BATCH_SIZE) as $batch) {
            self::fetchBatch($batch, $dateKey, $result);
        }

        return $result;
    }

    protected static function fetchBatch(Collection $cids, string $dateKey, array &$result): void
    {
        if ($cids->isEmpty()) {
            return;
        }

        $responses = Http::pool(fn (Pool $pool) => $cids->map(
            fn ($cid) => $pool->as($cid)
                ->withHeaders(['User-Agent' => 'winnipegfir.ca'])
                ->connectTimeout(5)
                ->timeout(15)
                ->get(self::BASE_URL.$cid.'/atcsessions/', ['start' => $dateKey])
        )->all());

        foreach ($cids as $cid) {
            $response = $responses[$cid] ?? null;

            // A 404 just means this CID has no ATC history -- that's a valid empty result.
            if ($response instanceof \Illuminate\Http\Client\Response && $response->status() === 404) {
                $result[$cid] = collect();
                Cache::put("vatsim.atcsessions.{$cid}.{$dateKey}", $result[$cid], now()->addMinutes(10));

                continue;
            }

            if (! $response instanceof \Illuminate\Http\Client\Response || ! $response->ok()) {
                // Network error, timeout, or VATSIM's known 500-while-online bug.
                // Leave uncached so the next load retries instead of sticking.
                $result[$cid] = null;

                continue;
            }

            $sessions = collect($response->json('results') ?? []);
            $result[$cid] = $sessions;
            Cache::put("vatsim.atcsessions.{$cid}.{$dateKey}", $sessions, now()->addMinutes(10));
        }
    }
}
