<?php

namespace App\Observers;

use App\Models\AtcTraining\RosterMember;
use App\Services\PermissionSyncService;

class RosterMemberObserver
{
    public function saved(RosterMember $rosterMember)
    {
        if ($rosterMember->user_id) {
            PermissionSyncService::syncForUser($rosterMember->user_id);
        }
    }

    public function deleted(RosterMember $rosterMember)
    {
        if ($rosterMember->user_id) {
            PermissionSyncService::syncForUser($rosterMember->user_id);
        }
    }
}
