<?php

namespace App\Models\Events;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EventConfirm extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'start_timestamp', 'end_timestamp', 'airport', 'position',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userHasApplied()
    {
        if (EventConfirm::where('event_id', $this->id)->where('user_cid', Auth::id())->first()) {
            return true;
        }

        return false;
    }

    public function rostermember()
    {
        return $this->belongsTo(RosterMember::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * start_timestamp/end_timestamp on this model are time-of-day only
     * ("H:i", no date — see the flatpickr noCalendar config that produces
     * them). These helpers reconstruct a real UTC instant by combining that
     * time-of-day with the parent Event's calendar date, so the result can
     * be converted to a user's display timezone.
     */
    public function startAtUtc(): ?Carbon
    {
        if (!$this->event || !$this->start_timestamp) {
            return null;
        }

        return Carbon::create($this->event->start_timestamp)->setTimeFromTimeString($this->start_timestamp);
    }

    public function endAtUtc(): ?Carbon
    {
        $start = $this->startAtUtc();
        if (!$start || !$this->end_timestamp) {
            return null;
        }

        $end = $start->copy()->setTimeFromTimeString($this->end_timestamp);
        if ($end->lt($start)) {
            $end->addDay();
        }

        return $end;
    }
}
