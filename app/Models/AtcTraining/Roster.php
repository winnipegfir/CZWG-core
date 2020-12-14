<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'cid',  'fname', 'lname', 'email', 'rating', 'visit', 'home_fir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
