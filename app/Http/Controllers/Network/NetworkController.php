<?php

namespace App\Http\Controllers\Network;

use App\Classes\VatsimStatsApi;
use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NetworkController extends Controller
{
    // Position tiers, low to high. A controller keeps their position(s) current
    // by controlling at their rating's tier, or the tier directly below it.
    const POSITION_TIERS = [
        'GND' => 1, 'DEL' => 1,
        'TWR' => 2,
        'APP' => 3, 'DEP' => 3,
        'CTR' => 4,
    ];

    const RATING_TIERS = [
        'S1' => 1,
        'S2' => 2,
        'S3' => 3,
        'C1' => 4, 'C2' => 4, 'C3' => 4,
        'I1' => 4, 'I2' => 4, 'I3' => 4,
        'SUP' => 4, 'ADM' => 4,
    ];

    const TIER_LABELS = [
        1 => 'GND/DEL',
        2 => 'TWR',
        3 => 'APP/DEP',
        4 => 'CTR',
    ];

    // Same prefixes ActivityLog uses to decide a position belongs to Winnipeg FIR.
    // Anything logged on a callsign that doesn't match one of these (e.g. a Toronto
    // position) is a foreign-FIR session and never counts toward this FIR's requirement.
    const HOME_FIR_PREFIXES = ['ZWG', 'CZWG', 'CYWG', 'CYAV', 'CYPG', 'CYQR', 'CYXE', 'CYQT', 'CYMJ', 'WPG'];

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

        $roster = RosterMember::where('active', '1')
            ->whereIn('status', ['home', 'visit', 'instructor', 'training'])
            ->with('user')
            ->get();

        // Pull real ATC session history straight from VATSIM instead of relying on
        // our own activity bot, which only ever records positions matching this
        // FIR's callsign prefixes and silently drops everything else (e.g. a
        // controller working a Toronto position never shows up in our own log).
        $vatsimSessionsByCid = VatsimStatsApi::getAtcSessionsForMembers($roster->pluck('cid')->unique(), $rangeStart);

        $members = $roster
            ->map(function ($member) use ($vatsimSessionsByCid, $rangeEnd) {
                $requirement = config('currency.'.$member->status);
                $member->requirement = $requirement;

                $ratingShortName = $member->user && $member->user->rating ? $member->user->rating->getShortName() : null;
                $member->rating_short_name = $ratingShortName;
                $ratingTier = self::RATING_TIERS[$ratingShortName] ?? null;

                $sessions = $vatsimSessionsByCid[(string) $member->cid] ?? null;
                $member->vatsim_data_unavailable = $sessions === null;

                $breakdown = [];
                $qualifyingHours = 0;
                $totalLoggedHours = 0;
                $nonFirHours = 0;

                foreach ($sessions ?? [] as $session) {
                    $sessionStart = Carbon::parse($session['start']);
                    if ($sessionStart->gt($rangeEnd)) {
                        continue;
                    }

                    $durationHours = ((float) $session['minutes_on_callsign']) / 60;
                    $callsign = strtoupper($session['callsign']);
                    $isHomeFir = Str::contains($callsign, self::HOME_FIR_PREFIXES);

                    if (! $isHomeFir) {
                        $category = 'Non-FIR';
                        $qualifies = false;
                        $nonFirHours += $durationHours;
                    } else {
                        $suffix = Str::of($callsign)->afterLast('_')->toString();
                        $positionTier = self::POSITION_TIERS[$suffix] ?? null;
                        $category = $positionTier ? self::TIER_LABELS[$positionTier] : 'Other';

                        $qualifies = $ratingTier !== null && $positionTier !== null
                            && ($positionTier === $ratingTier || $positionTier === $ratingTier - 1);
                    }

                    // Keyed by raw callsign (not just tier) so staff can see exactly
                    // which position each chunk of hours came from.
                    if (! isset($breakdown[$callsign])) {
                        $breakdown[$callsign] = ['hours' => 0, 'qualifies' => $qualifies, 'category' => $category];
                    }
                    $breakdown[$callsign]['hours'] += $durationHours;

                    $totalLoggedHours += $durationHours;
                    if ($qualifies) {
                        $qualifyingHours += $durationHours;
                    }
                }

                ksort($breakdown);

                $member->position_breakdown = $breakdown;
                $member->qualifying_hours = $qualifyingHours;
                $member->total_logged_hours = $totalLoggedHours;
                $member->non_fir_hours = $nonFirHours;
                $member->off_tier_hours = $totalLoggedHours - $qualifyingHours - $nonFirHours;
                $member->meets_requirement = $member->vatsim_data_unavailable || $requirement === null ? null : $totalLoggedHours >= $requirement;
                $member->meets_position_requirement = $member->vatsim_data_unavailable || $ratingTier === null ? null
                    : ($requirement === null ? null : $qualifyingHours >= $requirement);

                return $member;
            })
            ->sortBy('total_logged_hours')
            ->values();

        $totalMembers = $members->count();
        $meetingRequirement = $members->where('meets_requirement', true)->count();
        $belowRequirement = $members->where('meets_requirement', false)->count();
        $dataUnavailable = $members->where('vatsim_data_unavailable', true)->count();

        return view('dashboard.network.activity.index', compact(
            'members', 'quarterLabel', 'totalMembers', 'meetingRequirement', 'belowRequirement', 'dataUnavailable',
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
