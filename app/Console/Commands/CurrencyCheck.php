<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Settings\CoreSettings;
use App\Notifications\Network\MonthlyInactivity;
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
    protected $description = 'Check if every roster member has completed their hours for this month';

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
        $badMembers = [];
        foreach (RosterMember::all()->sortBy('currency') as $rosterMember) {
            if ($rosterMember->currency > config(sprintf('currency.%s', $rosterMember->status)))
                continue;

            $memberName = $rosterMember->full_name.' '.$rosterMember->cid;
            $memberEmail = $rosterMember->user()->first()->email;
            $memberActivity = $rosterMember->currency;
            array_push($badMembers, [
                'name' => $memberName,
                'email' => $memberEmail,
                'activity' => decimal_to_hm($memberActivity),
                'requirement' => decimal_to_hm(config(sprintf('currency.%s', $rosterMember->status)))
            ]);
        }

        $settings = CoreSettings::find(1);
        Notification::route('mail', [
            $settings->emailfirchief,
            $settings->emaildepfirchief,
            $settings->emailcinstructor
        ])->notify(new MonthlyInactivity($badMembers));

        // Reset the hours for every member
        DB::table('roster')->update(['currency' => 0]);

        // Remove our session logs because we don't need them anymore
        SessionLog::query()->truncate();
    }
}
