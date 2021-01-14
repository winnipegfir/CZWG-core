<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if (! $request->user()->hasRole($role)) {
            abort(404);
        }

        if ($permission !== null && ! $request->user()->can($permission)) {
            abort(404);
        }

        return $next($request);
    }
}
