<?php

namespace App\Console\Commands;

use App\Classes\VatsimRating;
use App\Classes\VatsimStatsApi;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class RatingUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:rating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs VATSIM rating update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('id', '!=', 1)->where('id', '!=', 2)->get()->keyBy('id');

        // This hits the same api.vatsim.net/api/ratings/ endpoint family VatsimStatsApi
        // does (hard-capped at ~10 requests/minute, per-IP, not per-CID) -- sharing its
        // rate limiter key keeps the two commands from stacking over that cap if they
        // ever land in the same minute. Whatever doesn't fit in a minute's budget waits
        // for the next one instead of firing unthrottled and getting rate-limited.
        $pending = $users->keys()->values();

        while ($pending->isNotEmpty()) {
            $budget = VatsimStatsApi::remainingRateLimitBudget();

            if ($budget <= 0) {
                sleep(60);

                continue;
            }

            $batch = $pending->take($budget);
            $pending = $pending->skip($budget)->values();

            foreach ($batch as $cid) {
                RateLimiter::hit(VatsimStatsApi::RATE_LIMIT_KEY, 60);
            }

            $responses = Http::pool(fn (Pool $pool) => $batch->map(
                fn ($cid) => $pool->as($cid)
                    ->withHeaders(['User-Agent' => 'winnipegfir.ca'])
                    ->connectTimeout(5)
                    ->timeout(15)
                    ->get('https://api.vatsim.net/api/ratings/'.$cid.'/')
            )->all());

            foreach ($batch as $cid) {
                $this->applyRating($users[$cid], $responses[$cid] ?? null);
            }
        }
    }

    protected function applyRating(User $user, $response): void
    {
        if (! $response instanceof Response || ! $response->ok()) {
            // Network error, timeout, or a rate-limited/bad response -- leave the
            // user's existing rating alone and try again on tomorrow's run rather
            // than overwriting good data with a failed lookup.
            Log::warning('RatingUpdate: could not fetch rating', ['cid' => $user->id]);

            return;
        }

        $ratingId = $response->json('rating');
        $rating = VatsimRating::tryFrom((int) $ratingId);

        if ($ratingId === null || ! $rating) {
            Log::warning('RatingUpdate: unrecognized rating in response', ['cid' => $user->id, 'rating' => $ratingId]);

            return;
        }

        if ((int) $user->rating_id === $rating->value) {
            return;
        }

        // rating_short/rating_long/rating_grp were dropped from the users table
        // (see 2024_01_11_232113_remove_old_fields.php) -- rating_id is the only
        // column left to write; short/long names are derived from it on read via
        // the VatsimRating-backed rating() accessor.
        $previousRating = VatsimRating::tryFrom((int) $user->rating_id);
        Log::info('User: '.$user->fname.' '.$user->lname.' updated from '.($previousRating?->getShortName() ?? 'unknown').' to '.$rating->getShortName().'.');

        $user->rating_id = $rating->value;
        $user->save();

        $rosterMember = $user->rosterProfile()->first();
        if ($rosterMember) {
            $rosterMember->rating_hours = 0;
            $rosterMember->save();
        }
    }
}
