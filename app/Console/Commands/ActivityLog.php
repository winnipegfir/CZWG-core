<?php

namespace App\Console\Commands;

use App\Classes\HttpHelper;
use App\Classes\VatsimHelper;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\SessionLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivityLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs activity logger';

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
        // Because OOMs
        DB::connection()->disableQueryLog();

        // Active lists
        $onlineControllers = [];

        // Getters
        $positions = MonitoredPosition::all();

        $response = HttpHelper::getClient()->get(VatsimHelper::getDatafeedUrl());
        $controllers = $response->object()->controllers;

        foreach ($controllers as $controller) {
            //Set our flag
            $identFound = false;

            foreach ($positions as $position) {
                if (($controller->callsign == $position->identifier)) {
                    $identFound = true; // set flag
                    array_push($onlineControllers, $controller); // Add if the callsign is the same as the position identifier
                }
            }

            if (! $identFound) {
                //Check to see if we need to make a new position, also check to make sure it isn't an ATIS, or an observer
                if (Str::contains($controller->callsign, ['ZWG', 'CZWG', 'CYWG', 'CYAV', 'CYPG', 'CYQR', 'CYXE', 'CYQT', 'CYMJ', 'WPG']) &&
                    ! Str::endsWith($controller->callsign, ['ATIS', 'OBS']) &&
                    $controller->facility != 0) {
                    // Add position to table if so
                    $monPos = new MonitoredPosition();
                    $monPos->identifier = $controller->callsign;
                    $monPos->save();

                    //They CLEARLY are on one of our positions, push them to the array please!
                    array_push($onlineControllers, $controller);
                }
            }
        }

        //Grab our open sessions
        $sessionLogs = SessionLog::where('session_end', null)->get();

        foreach ($onlineControllers as $oc) {
            //Set our flag like a fish, FISH ON!
            $logFound = false;

            //Let's see if they have an open session
            foreach ($sessionLogs as $log) {
                if ($log->cid == $oc->cid) {
                    $logFound = true;
                }
            }

            if (! $logFound) {
                //We have no log yet, let's create one!
                $roster = RosterMember::where('cid', $oc->cid)->first();

                //Creation time!
                $log = new SessionLog();
                $log->callsign = $oc->callsign;
                if ($roster) {
                    $log->roster_member_id = $roster->id;
                }
                $log->cid = $oc->cid;
                $log->session_start = Carbon::make($oc->logon_time);
                $log->monitored_position_id = MonitoredPosition::where('identifier', $oc->callsign)->first()->id;
                $log->emails_sent = 0;
                $log->save();

                Log::info('Session Log for '.$oc->cid.' on '.$oc->callsign.' has been created. Started at '.Carbon::now()->toDateTimeString());
            }
        }

        foreach ($sessionLogs as $log) {
            //We really like setting flags here at the Winnipeg FIR™
            $stillOnline = false;

            foreach ($onlineControllers as $oc) {
                if ($oc->cid == $log->cid &&
                    MonitoredPosition::where('id', $log->monitored_position_id)->first()->identifier == $oc->callsign &&
                    Carbon::make($oc->logon_time) == $log->session_start) {
                    $stillOnline = true;
                }
            }

            if (! $stillOnline) {
                // Start and end values parsed so Carbon can understand them
                $start = Carbon::create($log->session_start);
                $end = Carbon::now();

                // Calculate decimal difference (difference is the total hours gained) ie. 30 minutes = 0.5
                $difference = $start->floatDiffInMinutes($end) / 60;

                // Populate remaining columns
                $log->session_end = $end;
                $log->duration = $difference;

                // Save the log
                $log->save();

                Log::info('Session Log for '.$log->cid.' on '.$log->callsign.' has ended. Ended at '.$end);

                // Add hours
                $roster_member = RosterMember::where('cid', $log->cid)->first();

                // check it exists
                if ($roster_member &&
                    $roster_member->status == 'home' || $roster_member->status == 'instructor' || $roster_member->status == 'visit' || $roster_member->status == 'training' &&
                    $roster_member->active) {
                    // Add hours
                    $roster_member->currency += $difference;

                    if ($roster_member->rating == 'S1' || $roster_member->rating == 'S2' || $roster_member->rating == 'S3') {
                        $roster_member->rating_hours += $difference;
                    }
                    // Save roster member
                    $roster_member->save();
                }
            }
        }
    }
}
