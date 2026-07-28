<?php

namespace App\Classes;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class VatsimStatsApi
{
    // VATSIM's own authoritative ATC session history per CID. Unlike our own
    // activity bot (which only ever logs positions matching this FIR's callsign
    // prefixes), this includes every session a controller has worked anywhere
    // on the network -- which is what lets us detect out-of-FIR hours at all.
    const BASE_URL = 'https://api.vatsim.net/api/ratings/';

    // VATSIM hard-caps this endpoint at 10 requests/minute per IP (confirmed via its
    // 429 + retry-after response) -- this is a global ceiling, not per-CID, so a full
    // roster (which can easily be 50-150 people) can never be fetched live in one page
    // load. We stay one under it as a safety margin and let whatever doesn't fit in the
    // remaining budget stay uncached; it gets picked up by the cache-warm cron job
    // (see WarmVatsimActivityCache) or the next page load instead of tripping the limit
    // for the whole batch.
    const RATE_LIMIT_KEY = 'vatsim-atcsessions-api';

    const RATE_LIMIT_MAX_PER_MINUTE = 9;

    const CACHE_TTL_MINUTES = 30;

    /**
     * Fetch ATC sessions starting on/after $start for each cid.
     *
     * @param  \Illuminate\Support\Collection<int, int|string>  $cids
     * @return array<string, \Illuminate\Support\Collection|null> keyed by cid; null means not cached/fetched (yet)
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

        $budget = self::remainingRateLimitBudget();
        if ($budget > 0) {
            self::fetchBatch($toFetch->take($budget), $dateKey, $result);
        }

        return $result;
    }

    protected static function fetchBatch(Collection $cids, string $dateKey, array &$result): void
    {
        if ($cids->isEmpty()) {
            return;
        }

        foreach ($cids as $cid) {
            RateLimiter::hit(self::RATE_LIMIT_KEY, 60);
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
                Cache::put("vatsim.atcsessions.{$cid}.{$dateKey}", $result[$cid], now()->addMinutes(self::CACHE_TTL_MINUTES));

                continue;
            }

            if (! $response instanceof \Illuminate\Http\Client\Response || ! $response->ok()) {
                // Network error, timeout, or VATSIM's known 500-while-online bug.
                // Leave uncached so the next load retries instead of sticking.
                $result[$cid] = null;

                if ($response instanceof \Illuminate\Http\Client\Response) {
                    Log::warning('VatsimStatsApi: non-OK response fetching ATC sessions', [
                        'cid' => $cid,
                        'status' => $response->status(),
                        'body' => Str::limit($response->body(), 500),
                    ]);
                } elseif ($response instanceof \Illuminate\Http\Client\ConnectionException) {
                    Log::warning('VatsimStatsApi: connection error fetching ATC sessions', [
                        'cid' => $cid,
                        'message' => $response->getMessage(),
                    ]);
                } else {
                    Log::warning('VatsimStatsApi: unexpected pool result fetching ATC sessions', [
                        'cid' => $cid,
                        'type' => is_object($response) ? get_class($response) : gettype($response),
                    ]);
                }

                continue;
            }

            $sessions = collect($response->json('results') ?? []);
            $result[$cid] = $sessions;
            Cache::put("vatsim.atcsessions.{$cid}.{$dateKey}", $sessions, now()->addMinutes(self::CACHE_TTL_MINUTES));
        }
    }

    /**
     * How many more requests this endpoint can take this minute before hitting VATSIM's
     * cap. Used by the cache-warm cron job to know how much budget live page-load
     * fetches have already spent.
     */
    public static function remainingRateLimitBudget(): int
    {
        return max(0, self::RATE_LIMIT_MAX_PER_MINUTE - RateLimiter::attempts(self::RATE_LIMIT_KEY));
    }
}
