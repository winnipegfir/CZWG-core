<?php

namespace App\Services;

use App\Classes\VatsimStatsApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

// Single source of truth for "is this controller active" -- used by both the
// staff-facing network activity roster and each controller's own dashboard
// widget, so the two never drift into disagreeing about the same person.
class ControllerActivityService
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

    // Statuses that have to keep the majority of their hours inside Winnipeg FIR.
    // Visitors and trainees have no such split requirement, just the flat hour minimum.
    const HOME_TYPE_STATUSES = ['home', 'instructor'];

    /**
     * Compute activity stats for a set of roster members over a date range.
     * Mutates and returns each $rosterMembers item with: requirement, rating_short_name,
     * vatsim_data_unavailable, position_breakdown, qualifying_hours, total_logged_hours,
     * non_fir_hours, off_tier_hours, fir_percentage, is_home_type, meets_requirement,
     * fails_percentage_only.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\AtcTraining\RosterMember>  $rosterMembers
     */
    public static function compute(Collection $rosterMembers, Carbon $rangeStart, Carbon $rangeEnd): Collection
    {
        // Pull real ATC session history straight from VATSIM instead of relying on
        // our own activity bot, which only ever records positions matching this
        // FIR's callsign prefixes and silently drops everything else (e.g. a
        // controller working a Toronto position never shows up in our own log).
        $vatsimSessionsByCid = VatsimStatsApi::getAtcSessionsForMembers($rosterMembers->pluck('cid')->unique(), $rangeStart);

        return $rosterMembers->map(function ($member) use ($vatsimSessionsByCid, $rangeEnd) {
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
            $member->fir_percentage = $totalLoggedHours > 0 ? ($qualifyingHours / $totalLoggedHours) : 0;

            // Home controllers (and instructors) have to keep the majority of their
            // hours inside Winnipeg FIR -- a home controller who logs 3+ hours but
            // mostly at a foreign FIR (e.g. visiting Toronto Center) doesn't count as
            // active here.
            $isHomeType = in_array($member->status, self::HOME_TYPE_STATUSES);
            $member->is_home_type = $isHomeType;

            // Only hours worked at an eligible position within Winnipeg FIR can
            // ever satisfy the requirement -- raw total hours (which can be 100%
            // foreign-FIR time) must never be used to decide pass/fail.
            if ($member->vatsim_data_unavailable || $requirement === null) {
                $member->meets_requirement = null;
                $member->fails_percentage_only = false;
            } else {
                $meetsHours = $qualifyingHours >= $requirement;
                $meetsPercentage = ! $isHomeType || $member->fir_percentage >= 0.5;
                $member->meets_requirement = $meetsHours && $meetsPercentage;
                $member->fails_percentage_only = $meetsHours && ! $meetsPercentage;
            }

            return $member;
        });
    }
}
