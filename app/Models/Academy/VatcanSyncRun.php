<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class VatcanSyncRun extends Model
{
    protected $table = 'academy_vatcan_sync_runs';

    protected $fillable = [
        'initiated_by', 'status', 'controllers_found', 'visitors_ignored', 'users_matched',
        'pending_cids', 'enrollments_activated', 'enrollments_deactivated', 'message',
    ];
}
