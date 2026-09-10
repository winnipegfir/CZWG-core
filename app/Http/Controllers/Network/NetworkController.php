<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Classes\VatsimStatsApi;
use App\Models\Network\MonitoredPosition;
use App\Services\ControllerActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class NetworkController extends Controller
{
    public function index()
    {
        return view('dashboard.network.index');
    }

    public function activityIndex(Request $request)
    {
        $now = Carbon::now();
        $quarterLabel = 'Q'.ceil($now->month / 3).' '.$now->year;
        $defaultStart = $now->copy()->startOfQuarter();
        $defaultEnd = $now->copy();

        $rangeStart = $request->filled('start') ? Carbon::parse($request->get('start'))->startOfDay() : $defaultStart;
        $rangeEnd = $request->filled('end') ? Carbon::parse($request->get('end'))->endOfDay() : $defaultEnd;
        $isCustomRange = $request->filled('start') || $request->filled('end');

        // Let the cache-warm cron know this range is actually being looked at, so it
        // backs it in the background instead of leaving it to fight for live-load
        // rate-limit budget on every reload.
        if ($isCustomRange) {
            VatsimStatsApi::rememberRangeStart($rangeStart);
        }

        // Not filtered by active/inactive -- this should match everyone the Roster
        // admin page shows, so a pending/inactive member isn't invisible here just
        // because they haven't been flipped active yet.
        $roster = RosterMember::whereIn('status', ['home', 'visit', 'instructor', 'training'])
            ->with('user')
            ->get();

        $members = ControllerActivityService::compute($roster, $rangeStart, $rangeEnd)
            ->sortBy('total_logged_hours')
            ->values();

        $totalMembers = $members->count();
        $meetingRequirement = $members->where('meets_requirement', true)->count();
        $belowRequirement = $members->where('meets_requirement', false)->count();
        $dataUnavailable = $members->where('vatsim_data_unavailable', true)->count();
        $notOnVatcan = $members->where('vatcan_status', 'none')->count();

        // Home controllers, instructors, and trainees all belong to the FIR itself;
        // visitors are the only status held to the flat hour minimum with no FIR-share rule.
        $homeMembers = $members->whereIn('status', ['home', 'instructor', 'training'])->values();
        $visitingMembers = $members->where('status', 'visit')->values();

        return view('dashboard.network.activity.index', compact(
            'homeMembers', 'visitingMembers', 'quarterLabel', 'totalMembers', 'meetingRequirement', 'belowRequirement', 'dataUnavailable', 'notOnVatcan',
            'rangeStart', 'rangeEnd', 'isCustomRange'
        ));
    }

    public function monitoredPositionsIndex()
    {
        $positions = MonitoredPosition::all()->sortByDesc('identifier');

        return view('dashboard.network.monitoredpositions.index', compact('positions'));
    }

    public function viewMonitoredPosition($position)
    {
        $position = MonitoredPosition::where(strtolower('identifier'), strtolower($position))->firstOrFail();

        return view('dashboard.network.monitoredpositions.view', compact('position'));
    }

    public function createMonitoredPosition(Request $request)
    {
        $messages = [
            'identifier.required' => 'Please type an identifier prefix/callsign.',
        ];

        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createMonitoredPosition');
        }

        $position = new MonitoredPosition();
        $position->identifier = $request->get('identifier');
        $position->save();

        return redirect()->route('network.monitoredpositions.view', strtolower($position->identifier));
    }
}
