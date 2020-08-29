<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;

class VisitRosterMember extends Model

//WINNIPEG CONTROLLERS
{
    protected $table = 'visitroster';

    protected $fillable = [
        'cid', 'user_id', 'full_name', 'rating', 'del', 'gnd', 'twr', 'dep', 'app', 'ctr', 'remarks', 'active', 'homefir'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
