<?php

namespace App\Models\Network;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class ActivityWarning extends Model
{
    protected $fillable = [
        'roster_member_id', 'cid', 'member_name', 'quarter_label', 'quarter_start',
        'hours_logged', 'hours_required', 'warning_sent', 'warning_sent_at', 'warning_sent_by',
        'deadline', 'result', 'notes',
    ];

    protected $casts = [
        'quarter_start' => 'date',
        'warning_sent' => 'boolean',
        'warning_sent_at' => 'datetime',
        'deadline' => 'date',
    ];

    // Days a flagged controller gets to get current before further action.
    const WARNING_PERIOD_DAYS = 14;

    const RESULTS = [
        'pending' => 'Pending',
        'resolved' => 'Resolved — became active',
        'removed' => 'Removed from roster',
        'extended' => 'Extension granted',
        'no_action' => 'No action taken',
    ];

    public function rosterMember()
    {
        return $this->belongsTo(RosterMember::class);
    }

    public function warningSentBy()
    {
        return $this->belongsTo(User::class, 'warning_sent_by');
    }
}
