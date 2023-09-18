<?php

namespace App\Models\AtcTraining;

use App\Models\Events\EventConfirm;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class RosterMember extends Model

    //WINNIPEG CONTROLLERS
{
    protected $table = 'roster';

    protected $fillable = [
        'cid', 'user_id', 'status', 'full_name', 'rating', 'del', 'gnd', 'twr', 'dep', 'app', 'ctr', 'currency', 'rating_hours', 'remarks', 'active', 'home_fir', 'visit',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLeaderboardHours()
    { // Get hours from leaderboard
        return $this->monthly_hours;
    }

    public function meetsActivityRequirement()
    {
        if ($this->status == 'visit' && $this->active == '1' && $this->currency >= 1.0) {
            return true;
        } elseif ($this->status == 'home' && $this->active == '1' && $this->currency >= 2.0) {
            return true;
        } elseif ($this->status == 'instructor' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } else {
            return $IsActive = false;
        }

        return false;
    }

    public function eventconfirm()
    {
        return $this->hasMany(EventConfirm::class);
    }
}
