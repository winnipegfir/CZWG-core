<?php

namespace App\Observers;

use App\Models\AtcTraining\Instructor;
use App\Services\PermissionSyncService;

class InstructorObserver
{
    public function saved(Instructor $instructor)
    {
        if ($instructor->user_id) {
            PermissionSyncService::syncForUser($instructor->user_id);
        }
    }

    public function deleted(Instructor $instructor)
    {
        if ($instructor->user_id) {
            PermissionSyncService::syncForUser($instructor->user_id);
        }
    }
}
