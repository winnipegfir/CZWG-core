<?php

namespace App\Http\Controllers;

use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\Events\ControllerApplication;
use App\Models\Events\Event;
use App\Models\Events\EventConfirm;
use App\Models\Publications\AtcResource;
use App\Models\Tickets\Ticket;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        //  $allusers = User::all();
        $yourinstructor = Student::where('user_id', $user->id)->first();
        $certification = null;
        $active = null;
        $confirmedevent = [];
        $potentialRosterMember = RosterMember::where('user_id', $user->id)->first();
        if ($potentialRosterMember !== null) {
            $certification = $potentialRosterMember->status;
            $active = $potentialRosterMember->active;
        }
        $openTickets = Ticket::where('user_id', $user->id)->where('status', 0)->get();
        $staffTickets = Ticket::where('staff_member_cid', $user->id)->where('status', 0)->get();

        $unconfirmedapp = ControllerApplication::where('user_id', $user->id)->get();
        $confirmedapp = EventConfirm::where('user_id', $user->id)->get()->sortBy('start_timestamp');

        $event = Event::all()->sortBy('start_timestamp');

        foreach ($event as $e) {
            if (Carbon::now() > $e->end_timestamp) {
            } else {
                array_push($confirmedevent, $e);
            }
        }

        $atcResources = AtcResource::all()->sortBy('title');

        if ($user->permissions == 0) {
            return view('dashboard.index2', compact('openTickets', 'confirmedevent'));
        } else {
            return view('dashboard.index', compact('event', 'potentialRosterMember', 'yourinstructor', 'openTickets', 'staffTickets', 'certification', 'active', 'atcResources', 'unconfirmedapp', 'confirmedapp', 'confirmedevent'));
        }
    }

    public function postTweet()
    {
        return 'nothing';
    }
}
