<?php

namespace App\Console;

use App\AuditLogEntry;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\SessionLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\CoreSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Network\OneMonthInactivityReminder;
use RestCord\DiscordClient;
use GuzzleHttp\Client;
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

            // Load VATSIM data
            $vatsim = new \Vatsimphp\VatsimData();
            $vatsim->loadData();

            // Active lists
            $onlineControllers = array();

            // Getters
            $positions = MonitoredPosition::all();
            $controllers = $vatsim->getControllers();

            //Set our flag
            $identFound = false;

            foreach ($controllers as $controller) {
                foreach ($positions as $position) {
                    if (($controller['callsign'] == $position->identifier)) {
                        $identFound = true; // set flag
                        array_push($onlineControllers, $controller); // Add if the callsign is the same as the position identifier
                    }
                }

                if (!$identFound) {
                    //Check to see if we need to make a new position, also check to make sure it isn't an ATIS, or an observer
                    if (Str::contains($controller['callsign'], ['ZWG', 'CZWG', 'CYWG', 'CYAV', 'CYPG', 'CYQR', 'CYXE', 'CYQT', 'CYMJ']) && !Str::endsWith($controller['callsign'], 'ATIS') && !Str::endsWith($controller['callsign'], 'OBS') && $controller['facilitytype'] != 0) {
                        // Add position to table if so
                        $monPos = new MonitoredPosition();
                        $monPos->identifier = $controller["callsign"];
                        $monPos->save();

                        //They CLEARLY are on one of our positions, push them to the array please!
                        array_push($onlineControllers, $controller);
                    }
                }
            }

            //Grab our open sessions
            $sessionLogs =  SessionLog::where("session_end", null)->get();

            //Set our flag like a fish, FISH ON!
            $logFound = false;

            foreach ($onlineControllers as $oc) {
                //Let's see if they have an open session
                foreach($sessionLogs as $log) {
                    if($log->cid == $oc['cid']) {
                        $logFound = true;
                    }
                }

                if(!$logFound) {
                    //We have no log yet, let's create one!

                    //Make a pretty time
                    $ocLogon = substr($oc['time_logon'], 0, 4).'-'
                        .substr($oc['time_logon'], 4, 2).'-'
                        .substr($oc['time_logon'], 6, 2).' '
                        .substr($oc['time_logon'], 8, 2).':'
                        .substr($oc['time_logon'], 10, 2).':'
                        .substr($oc['time_logon'], 12, 2);

                    $roster = RosterMember::where('cid', $oc['cid'])->first();

                    //Creation time!
                    $log = new SessionLog();
                    $log->callsign = $oc['callsign'];
                    if($roster) {
                        $log->roster_member_id = $roster->id;
                    }
                    $log->cid = $oc['cid'];
                    $log->session_start = $ocLogon;
                    $log->monitored_position_id = MonitoredPosition::where('identifier', $oc['callsign'])->first()->id;
                    $log->emails_sent = 0;
                    $log->save();

                    Log::info('Session Log for ' . $oc['cid'] . ' on ' . $oc['callsign'] . ' has been created. Started at ' . $ocLogon);
                }
            }

            foreach($sessionLogs as $log) {
                //We really like setting flags here at the Winnipeg FIR™
                $stillOnline = false;

                foreach ($onlineControllers as $oc) {
                    $ocLogon = substr($oc['time_logon'], 0, 4).'-'
                        .substr($oc['time_logon'], 4, 2).'-'
                        .substr($oc['time_logon'], 6, 2).' '
                        .substr($oc['time_logon'], 8, 2).':'
                        .substr($oc['time_logon'], 10, 2).':'
                        .substr($oc['time_logon'], 12, 2);

                    if ($oc['cid'] == $log->cid) { // If CID matches
                        // If callsign matches
                        if (MonitoredPosition::where('id', $log->monitored_position_id)->first()->identifier == $oc['callsign']) {
                            if ($ocLogon == $log->session_start) {
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

                    error_log($difference);

                    // Save the log
                    $log->save();

                    Log::info('Session Log for ' . $log->cid . ' on ' . $log->callsign . ' has ended. Ended at ' . $end);

                    // Add hours
                    $roster_member = RosterMember::where('cid', $log->cid)->first();

                    // check it exists
                    if ($roster_member) {
                        if ($roster_member->status == 'home' || $roster_member->status == 'instructor' || $roster_member->status == 'visit') {
                            if ($roster_member->active) {
                                // Add hours
                                $roster_member->currency += $difference;

                                // Add hours to leaderboard
                                $roster_member->monthly_hours += $difference;
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
            file_get_contents(env('CRON_MINUTE_URL'));
        })->everyMinute();

        /* ActivityBot logging
        $schedule->call(function () {
            // Because OOMs
            DB::connection()->disableQueryLog();

            // Load VATSIM data
            $vatsim = new \Vatsimphp\VatsimData();
            $vatsim->loadData();

            // Active lists
            $onlineControllers = array();

            // Getters
            $positions = MonitoredPosition::all();
            $controllers = $vatsim->getControllers();
            $staffOnly = false;

            // Scan controller list for callsign relationships
            foreach ($controllers as $controller) {

                // Flag to set to true if position was in the monitored table
                $identFound = false;

                // Loop through position table
                foreach ($positions as $position) {
                    if (($controller['callsign'] == $position->identifier)) {
                        if ($position->staff_only) {
                            $staffOnly = true;
                        }
                        $identFound = true; // set flag
                        array_push($onlineControllers, $controller); // Add if the callsign is the same as the position identifier
                    }
                }

                // If it wasn't found, check if it has the correct callsign prefix
                if (!$identFound) {

                  if (substr($controller['callsign'], 0, 4) == "CZWG" || substr($controller['callsign'], 0, 4) == "CYWG" || substr($controller['callsign'], 0, 3) == "ZWG" || substr($controller['callsign'], 0, 4) == "CYAV" || substr($controller['callsign'], 0, 4) == "CYQT" || substr($controller['callsign'], 0, 4) == "CYQR" || substr($controller['callsign'], 0, 4) == "CYXE" || substr($controller['callsign'], 0, 4) == "CYPG" || substr($controller['callsign'], 0, 4) == "CYMJ") {
                  if (substr($controller['callsign'], 5, 4) != "ATIS" && $controller['facilitytype'] != 0  && !Str::contains($controller['callsign'], '_OBS')) {
                          // Add position to table if so, and email
                          $monPos = new MonitoredPosition();
                          $monPos->identifier = $controller["callsign"];
                          $monPos->save();
                          // todo: send email so we know that a new position was created

                  array_push($onlineControllers, $controller); // Add controller because they have logged on an oceanic position
                                    }
                                }
                            }
                        }

            // List of session logs
            $sessionLogs =  SessionLog::where("session_end", null)->get();

            // Check logs against currently online controllers
            foreach ($onlineControllers as $oc) {
                $matchFound = false;
                $ocLogon = null;
                foreach ($sessionLogs as $log) {
                    // Parse logon time lol
                    // Change this to the Y-m-d H:i:s format, as I changed the column type to 'dateTime'
                    $ocLogon = substr($oc['time_logon'], 0, 4).'-'
                        .substr($oc['time_logon'], 4, 2).'-'
                        .substr($oc['time_logon'], 6, 2).' '
                        .substr($oc['time_logon'], 8, 2).':'
                        .substr($oc['time_logon'], 10, 2).':'
                        .substr($oc['time_logon'], 12, 2);

                    // If a match is found
                    if ($ocLogon == $log->session_start) {
                        if (!$log->roster_member_id || RosterMember::where('cid', $log->cid)->first()->status == 'not_certified') { // Check if they're naughty
                            if ($log->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerNotCertified($log));
                                $log->emails_sent++;
                                $log->save();
                            }
                        } else if (!RosterMember::where('cid', $log->cid)->first()->active) { // inactive
                            if ($log->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerInactive($log));
                                $log->emails_sent++;
                                $log->save();
                            }
                        } else if (RosterMember::where('cid', $log->cid)->first()->status == 'training') {
                            if ($log->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerIsStudent($log));
                                error_log('user in training');
                                $log->emails_sent++;
                                $log->save();
                            }
                        } else if ($staffOnly && (RosterMember::where('cid', $log->cid)->first()->status != 'instructor')) { // instructor
                            if ($log->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerNotStaff($log));
                                $log->emails_sent++;
                                $log->save();
                            }
                        }

                        $matchFound = true;
                    } else {
                        continue; // No match was found
                    }
                }

                // Create log variable here so it's within appropriate scope
                $sessionLog = null;

                // If no match was found
                if (!$matchFound) {

                    // Parse logon time again lol
                    // Change this to the Y-m-d H:i:s format, as I changed the column type to 'dateTime'
                    $ocLogon = substr($oc['time_logon'], 0, 4).'-'
                        .substr($oc['time_logon'], 4, 2).'-'
                        .substr($oc['time_logon'], 6, 2).' '
                        .substr($oc['time_logon'], 8, 2).':'
                        .substr($oc['time_logon'], 10, 2).':'
                        .substr($oc['time_logon'], 12, 2);

                    // Build new session log
                    $sessionLog = new SessionLog();
                    $sessionLog->cid = $oc['cid'];
                    $sessionLog->callsign = $oc['callsign'];
                    $sessionLog->session_start = $ocLogon;
                    $sessionLog->monitored_position_id = MonitoredPosition::where('identifier', $oc['callsign'])->first()->id;
                    $sessionLog->emails_sent = 0;

                    // Check the user's CID against the roster
                    $user = RosterMember::where('cid', $oc['cid'])->first();
                    if ($user && $user->status != 'training' && $user->status != 'not_certified') { // Add if on roster, don't if not (big problem lmao)
                        $sessionLog->roster_member_id = $user->id;
                        if ($staffOnly && ($user->status != 'instructor')) {
                            if ($sessionLog->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerNotStaff($sessionLog));
                                $sessionLog->emails_sent++;
                                $sessionLog->save();
                            }
                        } else if (!$user->active) { // inactive
                            if ($sessionLog->emails_sent < 1) {
                              //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerInactive($sessionLog));
                                $sessionLog->emails_sent++;
                                $sessionLog->save();
                            }
                        }
                      } else { // Send unauthorised notification to FIR Chief
                          if ($user) {
                              $sessionLog->roster_member_id = $user->id;
                              if (!$user->active) { // inactive
                                  if ($sessionLog->emails_sent < 1) {
                                    //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerInactive($sessionLog));
                                      $sessionLog->emails_sent++;
                                      $sessionLog->save();
                                  }
                              } else if ($user->status == 'training') {
                                  if ($sessionLog->emails_sent < 1) {
                                    //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerIsStudent($sessionLog));
                                      $sessionLog->emails_sent++;
                                      $sessionLog->save();
                                  }
                              } else if ($sessionLog->emails_sent < 1) {
                                //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerNotCertified($sessionLog));
                                  $sessionLog->emails_sent++;
                                  $sessionLog->save();
                              }
                          } else {
                            //  Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new ControllerNotCertified($sessionLog));
                              $sessionLog->emails_sent++;
                              $sessionLog->save();
                          }
                      }

                    // Add session
                    $sessionLog->save();
                }
            }

            // Now check to see if any sessions should be marked as finished
            foreach ($sessionLogs as $log) {
                // Are they still online?
                $stillOnline = false;

                                // Loop through online controller list to find a match
                                foreach ($onlineControllers as $oc) {
                                    if ($oc['cid'] == $log->cid) { // If CID matches
                                        // If callsign matches
                                        if (MonitoredPosition::where('id', $log->monitored_position_id)->first()->identifier == $oc['callsign']) {
                                        if ($ocLogon == $log->session_start) {
                                          $stillOnline = true;
                            }
                          }
                      }
                  }

                // Check if the controller has indeed logged off
                if (!$stillOnline) {

                    // Start and end values parsed so Carbon can understand them
                    $start = Carbon::create($log->session_start);
                    $end = Carbon::now();

                    // Calculate decimal difference (difference is the total hours gained) ie. 30 minutes = 0.5
                    $difference = $start->floatDiffInMinutes($end) / 60;

                    // Populate remaining columns
                    $log->session_end = $end;
                    $log->duration = $difference;

                    error_log($difference);

                    // Save the log
                    $log->save();

                    // Add hours
                    $roster_member = RosterMember::where('cid', $log->cid)->first();

                    // check it exists
                    if ($roster_member) {
                        if ($roster_member->status == 'home' || $roster_member->status == 'instructor' || $roster_member->status == 'visit') {
                            if ($roster_member->active) {
                                // Add hours
                                $roster_member->currency = $roster_member->currency + $difference;

                                // Add hours to leaderboard
                                $roster_member->monthly_hours = $roster_member->monthly_hours + $difference;
                                if ($roster_member->rating == 'S1' || $roster_member->rating == 'S2' || $roster_member->rating == 'S3') {
                                  $roster_member->rating_hours = $roster_member->rating_hours + $difference;
                                }
                                // Save roster member
                                $roster_member->save();
                            }
                        }
                    }
                }
            }
            file_get_contents(env('CRON_MINUTE_URL'));
        })->everyMinute(); */

        //VATSIM Rating Update because Nate is making me
        $schedule->call(function () {
            //Define our ratings, because how else are we supposed to?
            $ratings = [
                -1 => ["IACT", "Inactive"],
                0 => ["SUSP", "Suspended"],
                1 => ["OBS", "Observer"],
                2 => ["S1", "Tower Trainee"],
                3 => ["S2", "Tower Controller"],
                4 => ["S3", "TMA Controller"],
                5 => ["C1", "Enroute Controller"],
                7 => ["C3", "Senior Controller"],
                8 => ["I1", "Instructor"],
                10 => ["I3", "Senior Instructor"],
                11 => ["SUP", "Supervisor"],
                12 => ["ADM", "Administrator"]
            ];
            $users = User::all();

            // Check each user to see if their rating has changed
            foreach($users as $u) {
                //Because of the vacant user ugh
                if($u->id != 1) {
                    $rosterMember = RosterMember::where('cid', $u->id)->first();
                    $getRating = json_decode(file_get_contents('https://api.vatsim.net/api/ratings/' . $u->id . '/'));
                    $ratingID = $getRating->rating;
                    if ($u->rating_id != $ratingID) {
                        //Log it for when it breaks
                        Log::info('User: ' . $u->fname . ' ' . $u->lname . ' updated from ' . $u->rating_short . ' to ' . $ratings[$ratingID][0] . '.');

                        User::where('id', $u->id)
                            ->update(['rating_id' => $ratingID, 'rating_short' => $ratings[$ratingID][0], 'rating_long' => $ratings[$ratingID][1], 'rating_GRP' => $ratings[$ratingID][1]]);

                        if($rosterMember) {
                            Log::info("--> Updating rating, setting rating hours to 0");

                            RosterMember::where('cid', $u->id)
                                ->update(['rating' => $ratings[$ratingID][0], 'rating_hours' => 0]);
                        }

                        Log::info('--> Completed!');
                    } else {
                        //Log it for when it breaks
                        Log::info('User: ' . $u->fname . ' ' . $u->lname . ' rating is unchanged. Skipping.');
                    }
                }
            }
            file_get_contents(env('CRON_DAILY_URL'));
        })->daily()->timezone('America/New_York');

        // Monthly leaderboard wipe + currency wipe + notify staff + session log wipe
        $schedule->call(function () {

            //How to make your time's look pretty, with Winnipeg!
            function convertTime($dec) {
                // start by converting to seconds
                $seconds = ($dec * 3600);
                // we're given hours, so let's get those the easy way
                $hours = floor($dec);
                // since we've "calculated" hours, let's remove them from the seconds variable
                $seconds -= $hours * 3600;
                // calculate minutes left
                $minutes = floor($seconds / 60);
                // return the time formatted HH:MM:SS
                return lz($hours).":".lz($minutes);
            }

            function lz($num) {
                return (strlen($num) < 2) ? "0{$num}" : $num;
            }

            //Send Email to FIR Chief, Deputy Chief, and Chief Instructor alerting of inactivity
            $badMembers = [];
            foreach (RosterMember::all()->sortBy('currency') as $rosterMember) {
                if ($rosterMember->status == 'visit') {
                    if ($rosterMember->currency < 1) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => convertTime($memberActivity), 'requirement' => '01:00']);
                    }
                } elseif ($rosterMember->status == 'home') {
                    if ($rosterMember->currency < 2) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => convertTime($memberActivity), 'requirement' => '02:00']);
                    }
                } elseif ($rosterMember->status == 'instructor') {
                    if ($rosterMember->currency < 3) {
                        $memberName = $rosterMember->full_name . ' ' . $rosterMember->cid;
                        $memberEmail = User::where('id', $rosterMember->cid)->first()->email;
                        $memberActivity = $rosterMember->currency;
                        array_push($badMembers, ['name' => $memberName, 'email' => $memberEmail, 'activity' => convertTime($memberActivity), 'requirement' => '03:00']);
                    }
                }
            }
            Notification::route('mail', [CoreSettings::find(1)->emailfirchief, CoreSettings::find(1)->emaildepfirchief, CoreSettings::find(1)->emailcinstructor])->notify(new OneMonthInactivityReminder($badMembers));

            // Loop through all roster members
            foreach (RosterMember::all() as $rosterMember) {
                // Reset the hours for every member
                $rosterMember->monthly_hours = 0.0;
                $rosterMember->currency = 0.0;
                $rosterMember->save();
            }

            //Remove our session logs because we don't need them anymore
            SessionLog::query()->truncate();

            file_get_contents(env('CRON_MONTHLY_URL'));
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
