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

class EventPosition extends Model
{
    protected $fillable = [
        'id', 'position',
    ];
}
