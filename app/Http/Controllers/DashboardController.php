<?php

namespace App\Http\Controllers;

use App\Models\AtcTraining\RosterMember;
use App\Services\VatsimBookingService;
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
        $waitlistPosition = null;
        $waitlistTypeTotal = null;
        if ($yourinstructor && $yourinstructor->status == 0 && $yourinstructor->waitlist_added_at) {
            $waitlistPosition = Student::where('status', 0)
                ->where('entry_type', $yourinstructor->entry_type)
                ->where('waitlist_added_at', '<=', $yourinstructor->waitlist_added_at)
                ->whereNotNull('waitlist_added_at')
                ->count();
            $waitlistTypeTotal = Student::where('status', 0)
                ->where('entry_type', $yourinstructor->entry_type)
                ->whereNotNull('waitlist_added_at')
                ->count();
        }
        $certification = null;
        $active = null;
        $cbtnotifications = [];
        $student = Student::where('user_id', $user->id)->first();
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

        $myBookings = collect();
        if ($user->permissions >= 1) {
            $bookingResult = (new VatsimBookingService)->getBookings(['cid' => $user->id, 'sort' => 'start', 'sort_dir' => 'asc']);
            $myBookings = collect($bookingResult['data'] ?? [])
                ->filter(fn($b) => Carbon::parse($b['end'])->isFuture())
                ->values();
        }

        if ($user->permissions == 0) {
            return view('dashboard.index2', compact('openTickets', 'confirmedevent', 'cbtnotifications', 'yourinstructor', 'waitlistPosition', 'waitlistTypeTotal'));
        } else {
            return view('dashboard.index', compact('event', 'potentialRosterMember', 'yourinstructor', 'waitlistPosition', 'waitlistTypeTotal', 'openTickets', 'staffTickets', 'certification', 'active', 'atcResources', 'unconfirmedapp', 'confirmedapp', 'confirmedevent', 'cbtnotifications', 'myBookings'));
        }
    }

    public function postTweet()
    {
        return 'nothing';
    }
}
