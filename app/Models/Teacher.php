<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;

class Teacher extends Model
{
    public $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_cid');
    }
}
