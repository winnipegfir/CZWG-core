<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;

class StaffMember extends Model
{
    protected $table = 'staff_member';

    protected $fillable = [
        'user_id', 'position', 'shortform', 'group', 'group_id', 'description', 'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
