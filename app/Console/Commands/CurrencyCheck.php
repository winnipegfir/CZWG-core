<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Network\ActivityWarning;
use App\Models\Network\SessionLog;
use App\Models\Settings\CoreSettings;
use Carbon\Carbon;
use App\Notifications\Network\QuarterlyInactivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CurrencyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if every roster member has completed their hours for this quarter.';

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
        // This runs at 00:00 on the 1st of the new quarter, checking hours the
        // roster just finished accumulating over the quarter that's ending --
        // so "yesterday" is the quarter this check is actually about.
        $quarterStart = Carbon::now()->subDay()->startOfQuarter();
        $quarterLabel = 'Q'.ceil($quarterStart->month / 3).' '.$quarterStart->year;

        $badMembers = [];
        foreach (RosterMember::all()->sortBy('currency') as $rosterMember) {
            // Not endorsed for a single position yet -- they can't control anything,
            // so there's no activity to be current on in the first place.
            if (! $rosterMember->hasAnyEndorsement()) {
                continue;
            }

            if ($rosterMember->currency >= config(sprintf('currency.%s', $rosterMember->status))) {
                continue;
            }

            $memberName = $rosterMember->full_name.' '.$rosterMember->cid;
            $memberEmail = $rosterMember->user()->first()->email;
            $memberActivity = $rosterMember->currency;
            $memberRequirement = config(sprintf('currency.%s', $rosterMember->status));
            $badMembers[] = [
                'name' => $memberName,
                'email' => $memberEmail,
                'activity' => decimal_to_hm($memberActivity),
                'requirement' => decimal_to_hm($memberRequirement),
            ];

            // One row per flagged member per quarter, so staff have somewhere to
            // track whether the 14-day warning actually went out and how it
            // resolved -- this check only ever flags, it doesn't notify the
            // member directly.
            ActivityWarning::firstOrCreate(
                ['roster_member_id' => $rosterMember->id, 'quarter_label' => $quarterLabel],
                [
                    'cid' => $rosterMember->cid,
                    'member_name' => $memberName,
                    'quarter_start' => $quarterStart,
                    'hours_logged' => $memberActivity,
                    'hours_required' => $memberRequirement,
                ]
            );
        }

        $settings = CoreSettings::find(1);
        Notification::route('mail', [
            $settings->emailfirchief,
            $settings->emaildepfirchief,
            $settings->emailcinstructor,
        ])->notify(new QuarterlyInactivity($badMembers));

        // Reset the hours for every member
        DB::table('roster')->update(['currency' => 0]);

        // Keep the last 6 months for profile heatmaps; drop anything older
        SessionLog::where('session_end', '<', Carbon::now()->subMonths(6))->delete();

        return 0;
    }

}
