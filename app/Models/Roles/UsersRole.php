<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Model;

class UsersRole extends Model
{
    protected $fillable = [
        'user_id', 'role_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function Role()
    {
        return $this->belongsTo(Role::class);
    }
}
