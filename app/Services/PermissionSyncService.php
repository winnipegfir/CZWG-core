<?php

namespace App\Services;

use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\RosterMember;
use App\Models\Users\StaffMember;
use App\Models\Users\User;

// Keeps users.permissions in step with the roster/instructor/staff data that
// actually determines someone's role, so staff don't have to remember to bump
// it by hand every time a roster status, Instructor row, or StaffMember row
// changes. Mentor (2) and Administrator (5) have no data-driven signal, so
// this never touches a user sitting at either -- those stay entirely manual.
class PermissionSyncService
{
    const MANUAL_ONLY_LEVELS = [2, 5];

    const GUEST = 0;
    const CONTROLLER = 1;
    const INSTRUCTOR = 3;
    const STAFF = 4;

    // Roster statuses that qualify someone as at least a current controller.
    const CONTROLLER_STATUSES = ['home', 'visit', 'training', 'instructor'];

    public static function syncForUser(int $userId): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        $target = self::pendingChangeFor($user);
        if ($target !== null) {
            $user->permissions = $target;
            $user->save();
        }
    }

    /**
     * The permission level $user would be changed to, or null if nothing would
     * change (either they're already correct, or they're sitting at a manual-only
     * level that this service never touches). Used for both the live sync and
     * the backfill command's dry-run preview, so the two can never disagree.
     */
    public static function pendingChangeFor(User $user): ?int
    {
        if (in_array((int) $user->permissions, self::MANUAL_ONLY_LEVELS, true)) {
            return null;
        }

        $target = self::deriveLevel($user->id);

        return (int) $user->permissions !== $target ? $target : null;
    }

    public static function deriveLevel(int $userId): int
    {
        if (StaffMember::where('user_id', $userId)->exists()) {
            return self::STAFF;
        }

        $rosterMember = RosterMember::where('user_id', $userId)->first();
        $isInstructor = Instructor::where('user_id', $userId)->exists()
            || ($rosterMember && $rosterMember->status === 'instructor');

        if ($isInstructor) {
            return self::INSTRUCTOR;
        }

        if ($rosterMember && in_array($rosterMember->status, self::CONTROLLER_STATUSES, true)) {
            return self::CONTROLLER;
        }

        return self::GUEST;
    }
}
