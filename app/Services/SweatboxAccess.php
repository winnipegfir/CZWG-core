<?php

namespace App\Services;

use App\Models\Users\User;
use App\Models\Teacher;

class SweatboxAccess
{
    public static function canView(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return (int) $user->permissions >= 5
            || (int) $user->permissions === 3
            || $user->instructorProfile()->exists()
            || Teacher::where('user_cid', $user->id)->where('is_instructor', true)->exists();
    }
}
