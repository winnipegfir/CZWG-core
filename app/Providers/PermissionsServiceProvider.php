<?php

namespace App\Providers;

use App\Models\Roles\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    public function register()
    {
//
    }

    public function boot()
    {
        try {
            Permission::get()->map(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch (\Exception $e) {
            report($e);

            return false;
        }
        Blade::if('role', function ($role) {
            return Auth()->check() && Auth::user()->hasRole($role);
        });
    }
}
