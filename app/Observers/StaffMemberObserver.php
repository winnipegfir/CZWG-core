<?php

namespace App\Observers;

use App\Models\Users\StaffMember;
use App\Services\PermissionSyncService;

class StaffMemberObserver
{
    public function saved(StaffMember $staffMember)
    {
        if ($staffMember->user_id) {
            PermissionSyncService::syncForUser($staffMember->user_id);
        }
    }

    public function deleted(StaffMember $staffMember)
    {
        if ($staffMember->user_id) {
            PermissionSyncService::syncForUser($staffMember->user_id);
        }
    }
}
