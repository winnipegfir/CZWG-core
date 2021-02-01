<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'slug', 'secure',
    ];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }

    public function usersrole()
    {
        return $this->hasMany(UsersRole::class);
    }

}
