<?php

namespace App\Classes;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Carbon;
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

        if ($toFetch->isEmpty()) {
            return $result;
        }

        $responses = Http::pool(fn (Pool $pool) => $toFetch->map(
            fn ($cid) => $pool->as($cid)
                ->withHeaders(['User-Agent' => 'winnipegfir.ca'])
                ->connectTimeout(5)
                ->timeout(15)
                ->get(self::BASE_URL.$cid.'/atcsessions/', ['start' => $dateKey])
        )->all());

        foreach ($toFetch as $cid) {
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

        return $result;
    }
}
