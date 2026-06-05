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
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        $totalVatsimHours = null;
        if ($potentialRosterMember && in_array($potentialRosterMember->status, ['home', 'instructor'])) {
            $quarterStart = Carbon::now()->startOfQuarter()->format('Y-m-d');
            $cid = $potentialRosterMember->cid;
            $cacheKey = 'vatsim_total_hours_' . $cid . '_' . $quarterStart;

            $totalVatsimHours = Cache::remember($cacheKey, 3600, function () use ($cid, $quarterStart) {
                try {
                    $client = new Client();
                    $pageNum = 1;
                    $totalMinutes = 0;

                    do {
                        $url = sprintf('https://api.vatsim.net/api/ratings/%s/atcsessions/?page=%s&start=%s', $cid, $pageNum, $quarterStart);
                        $response = $client->request('GET', $url, ['timeout' => 10]);
                        $data = json_decode($response->getBody()->getContents());
                        foreach ($data->results as $result) {
                            $totalMinutes += $result->minutes_on_callsign;
                        }
                        $hasNext = !empty($data->next);
                        $pageNum++;
                    } while ($hasNext);

                    return $totalMinutes / 60;
                } catch (\Exception $e) {
                    return null;
                }
            });
        }

        if ($user->permissions == 0) {
            return view('dashboard.index2', compact('openTickets', 'confirmedevent', 'cbtnotifications', 'yourinstructor', 'waitlistPosition', 'waitlistTypeTotal'));
        } else {
            return view('dashboard.index', compact('event', 'potentialRosterMember', 'yourinstructor', 'waitlistPosition', 'waitlistTypeTotal', 'openTickets', 'staffTickets', 'certification', 'active', 'atcResources', 'unconfirmedapp', 'confirmedapp', 'confirmedevent', 'cbtnotifications', 'myBookings', 'totalVatsimHours'));
        }
    }

    public function postTweet()
    {
        return 'nothing';
    }
}
