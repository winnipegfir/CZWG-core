<?php

namespace App\Services;

use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcademyLaunchNotice
{
    public const MAX_DISPLAYS = 5;

    public static function eligible(?User $user): bool
    {
        return $user !== null
            && AcademyVisibility::accessMode() === 'normal'
            && AcademyVisibility::shouldShowNav($user)
            && ! AcademyVisibility::maintenanceMode();
    }

    public function record(User $user, string $action): bool
    {
        if (! self::eligible($user) || ! Schema::hasTable('academy_launch_notices')) {
            return false;
        }

        return DB::transaction(function () use ($user, $action) {
            // Lock the account so simultaneous tabs/devices cannot exceed the cap,
            // including the first request before a notice row exists.
            User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $notice = DB::table('academy_launch_notices')->where('user_id', $user->getKey())->first();
            $count = $notice ? (int) $notice->display_count : 0;

            if ($action === 'dismiss') {
                DB::table('academy_launch_notices')->updateOrInsert(
                    ['user_id' => $user->getKey()],
                    ['display_count' => $count, 'dismissed' => true]
                );

                return false;
            }

            if (($notice && $notice->dismissed) || $count >= self::MAX_DISPLAYS) {
                return false;
            }

            DB::table('academy_launch_notices')->updateOrInsert(
                ['user_id' => $user->getKey()],
                ['display_count' => $count + 1, 'dismissed' => false]
            );

            return true;
        });
    }
}
