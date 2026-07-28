<?php

namespace App\Console\Commands;

use App\Classes\VatsimStatsApi;
use App\Models\AtcTraining\RosterMember;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class WarmVatsimActivityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:warm-activity-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Pre-fetches this quarter's VATSIM ATC session history for active roster "
        ."members a few at a time, so the network activity page and dashboard widget read from a warm "
        ."cache instead of racing VATSIM's 10-requests-per-minute cap on every page load.";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $cids = RosterMember::where('active', '1')
            ->whereIn('status', ['home', 'visit', 'instructor', 'training'])
            ->pluck('cid')
            ->unique();

        // Same default range NetworkController and DashboardController use when no
        // custom range is picked -- the range almost everyone actually views.
        VatsimStatsApi::getAtcSessionsForMembers($cids, Carbon::now()->startOfQuarter());

        // Any custom ranges staff have actually looked at recently (e.g. a quarterly
        // review pulling last quarter's dates) get backed too, a few CIDs per run,
        // instead of only ever fighting for live-load rate-limit budget on reload.
        foreach (VatsimStatsApi::recentlyViewedRangeStarts() as $dateKey) {
            VatsimStatsApi::getAtcSessionsForMembers($cids, Carbon::parse($dateKey));
        }
    }
}
