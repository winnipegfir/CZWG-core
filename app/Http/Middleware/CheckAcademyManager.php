<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAcademyManager
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->canManageAcademyEnrollments()) {
            return $next($request);
        }

        abort(403, 'Only administrators may manage Academy enrollments.');
    }
}
