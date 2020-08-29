<?php

namespace App\Models\Events;

use App\Models\Users\User;
use App\Models\AtcTraining\RosterMember;
use App\Models\Events\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Parsedown;
use Illuminate\Support\HtmlString;
use Auth;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

}
