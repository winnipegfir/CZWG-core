<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    public $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_cid');
    }
}
