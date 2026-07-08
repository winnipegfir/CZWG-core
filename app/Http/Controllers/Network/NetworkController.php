<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class NetworkController extends Controller
{
    public function index()
    {
        return view('dashboard.network.index');
    }

    public function activityIndex()
    {
        $quarterLabel = 'Q'.ceil(Carbon::now()->month / 3).' '.Carbon::now()->year;

        $members = RosterMember::where('active', '1')
            ->whereIn('status', ['home', 'visit', 'instructor', 'training'])
            ->get()
            ->map(function ($member) {
                $requirement = config('currency.'.$member->status);
                $member->requirement = $requirement;
                $member->meets_requirement = $requirement === null ? null : $member->currency >= $requirement;

                return $member;
            })
            ->sortBy('currency')
            ->values();

        $totalMembers = $members->count();
        $meetingRequirement = $members->where('meets_requirement', true)->count();
        $belowRequirement = $members->where('meets_requirement', false)->count();

        return view('dashboard.network.activity.index', compact('members', 'quarterLabel', 'totalMembers', 'meetingRequirement', 'belowRequirement'));
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
