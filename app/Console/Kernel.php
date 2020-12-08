<?php

namespace App\Console;

use App\AuditLogEntry;
use App\Models\AtcTraining\RosterMember;
use App\Models\Events\Event;
use App\Models\Events\EventConfirm;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\SessionLog;
use App\Models\Users\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\CoreSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Network\OneMonthInactivityReminder;
use App\Notifications\events\EventReminder;
use Illuminate\Support\Str;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
     protected function schedule (Schedule $schedule)
    {
        //Re-doing ActivityBot because it's keking itself lol
        $schedule->call(function () {

            // Because OOMs
            DB::connection()->disableQueryLog();

            // Active lists
            $onlineControllers = array();

            // Getters
            $positions = MonitoredPosition::all();

            $client = new Client();
            $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
            $controllers = json_decode($response->getBody()->getContents())->controllers;

            foreach ($controllers as $controller) {
                //Set our flag
                $identFound = false;

                foreach ($positions as $position) {
                    if (($controller->callsign == $position->identifier)) {
                        $identFound = true; // set flag
                        array_push($onlineControllers, $controller); // Add if the callsign is the same as the position identifier
                    }
                }

                if (!$identFound) {
                    //Check to see if we need to make a new position, also check to make sure it isn't an ATIS, or an observer
                    if (Str::contains($controller->callsign, ['ZWG', 'CZWG', 'CYWG', 'CYAV', 'CYPG', 'CYQR', 'CYXE', 'CYQT', 'CYMJ']) &&
                        !Str::endsWith($controller->callsign, 'ATIS') &&
                        !Str::endsWith($controller->callsign, 'OBS') &&
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
            $sessionLogs =  SessionLog::where("session_end", null)->get();


            foreach ($onlineControllers as $oc) {
                //Set our flag like a fish, FISH ON!
                $logFound = false;

                //Let's see if they have an open session
                foreach($sessionLogs as $log) {
                    if($log->cid == $oc->cid) {
                        $logFound = true;
                    }
                }

                if(!$logFound) {
                    //We have no log yet, let's create one!

                    $roster = RosterMember::where('cid', $oc->cid)->first();

                    //Creation time!
                    $log = new SessionLog();
                    $log->callsign = $oc->callsign;
                    if($roster) {
                        $log->roster_member_id = $roster->id;
                    }
                    $log->cid = $oc->cid;
                    $log->session_start = Carbon::make($oc->logon_time);
                    $log->monitored_position_id = MonitoredPosition::where('identifier', $oc->callsign)->first()->id;
                    $log->emails_sent = 0;
                    $log->save();

                    Log::info('Session Log for ' . $oc->cid . ' on ' . $oc->callsign . ' has been created. Started at ' . Carbon::now()->toDateTimeString());
                }
            }

            foreach($sessionLogs as $log) {
                //We really like setting flags here at the Winnipeg FIR™
                $stillOnline = false;

                foreach ($onlineControllers as $oc) {
                    if ($oc->cid == $log->cid) { // If CID matches
                        // If callsign matches
                        if (MonitoredPosition::where('id', $log->monitored_position_id)->first()->identifier == $oc->callsign) {
                            if (Carbon::make($oc->logon_time) == $log->session_start) {
                                $stillOnline = true;
                            }
                        }
                    }
                }

                if (!$stillOnline) {

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

                    Log::info('Session Log for ' . $log->cid . ' on ' . $log->callsign . ' has ended. Ended at ' . $end);

                    // Add hours
                    $roster_member = RosterMember::where('cid', $log->cid)->first();

                    // check it exists
                    if ($roster_member) {
                        if ($roster_member->status == 'home' || $roster_member->status == 'instructor' || $roster_member->status == 'visit' || $roster_member->status == 'training') {
                            if ($roster_member->active) {
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

            //Event Reminders

            $events = Event::all();
            foreach($events as $e) {
                //Check if there is less than 24 hours before the event
                if (Carbon::now()->diffInHours($e->start_timestamp, false) < 24 && Carbon::now()->diffInHours($e->start_timestamp, false) > 0) {
                    $rosterMembers = RosterMember::all();
                    foreach ($rosterMembers as $r) {
                        //Check if they are in our event
                        $inEvent = EventConfirm::where(['user_id' => $r->cid, 'event_id' => $e->id])->first();
                        if($inEvent) {
                            //Do they need a reminder email?
                            if($inEvent->email_sent == 0) {
                                //Do they want emails?
                                if($r->user->gdpr_subscribed_emails == 1) {
                                    //They want/need an email, send er bud!
                                    $positions = EventConfirm::where(['user_id' => $r->cid, 'event_id' => $e->id])->get();
                                    $r->user->notify(new EventReminder($e, $positions));

                                    //Set our DB flag so they don't get another
                                    foreach($positions as $p) {
                                        $p->email_sent = 1;
                                        $p->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            file_get_contents(config('cronurls.minute'));
        })->everyMinute()->evenInMaintenanceMode();

        //VATSIM Rating Update because Nate is making me
        $schedule->call(function () {
            $ratings = json_decode(file_get_contents('http://cluster.data.vatsim.net/v3/vatsim-data.json'))->ratings;
            $users = User::all();

            // Check each user to see if their rating has changed
            foreach($users as $u) {
                //Because of the vacant user ugh
                if($u->id != 1 && $u->id != 2) {
                    $rosterMember = RosterMember::where('cid', $u->id)->first();
                    $getRating = json_decode(file_get_contents('https://api.vatsim.net/api/ratings/' . $u->id . '/'));
                    $ratingID = $getRating->rating;
                    if ($u->rating_id != $ratingID) {
                        //Log it for when it breaks
                        Log::info('User: ' . $u->fname . ' ' . $u->lname . ' updated from ' . $u->rating_short . ' to ' . $ratings[$ratingID]['short'] . '.');

                        User::where('id', $u->id)
                            ->update(['rating_id' => $ratingID, 'rating_short' => $ratings[$ratingID]['short'], 'rating_long' => $ratings[$ratingID]['long'], 'rating_GRP' => $ratings[$ratingID]['long']]);

                        if($rosterMember) {
                            Log::info("--> Setting rating hours to 0");

                            RosterMember::where('cid', $u->id)
                                ->update(['rating_hours' => 0]);
                        }

                        Log::info('--> Completed!');
                    } else {
                        //Log it for when it breaks
                        Log::info('User: ' . $u->fname . ' ' . $u->lname . ' rating is unchanged. Skipping.');
                    }
                }
            }
            file_get_contents(config('cronurls.daily'));
        })->daily()->timezone('America/New_York');

        // Monthly leaderboard wipe + currency wipe + notify staff + session log wipe
        $schedule->call(function () {

            //Send Email to FIR Chief, Deputy Chief, and Chief Instructor alerting of inactivity
            $badMembers = [];
            foreach (RosterMember::all()->sortBy('currency') as $rosterMember) {
                if ($rosterMember->status == 'visit') {
                    if ($rosterMember->currency < 1) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => decimal_to_hm($memberActivity), 'requirement' => '01:00']);
                    }
                } elseif ($rosterMember->status == 'home') {
                    if ($rosterMember->currency < 2) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => decimal_to_hm($memberActivity), 'requirement' => '02:00']);
                    }
                } elseif ($rosterMember->status == 'instructor') {
                    if ($rosterMember->currency < 3) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => decimal_to_hm($memberActivity), 'requirement' => '03:00']);
                    }
                }
            }
            Notification::route('mail', [CoreSettings::find(1)->emailfirchief, CoreSettings::find(1)->emaildepfirchief, CoreSettings::find(1)->emailcinstructor])->notify(new OneMonthInactivityReminder($badMembers));

            // Loop through all roster members
            foreach (RosterMember::all() as $rosterMember) {
                // Reset the hours for every member
                $rosterMember->currency = 0.0;
                $rosterMember->save();
            }

            //Remove our session logs because we don't need them anymore
            SessionLog::query()->truncate();

            file_get_contents(config('cronurls.monthly'));
        })->monthlyOn(1, '00:00');
}
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
