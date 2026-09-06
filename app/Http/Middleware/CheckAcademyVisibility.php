<?php

namespace App\Http\Middleware;

use App\Services\AcademyVisibility;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAcademyVisibility
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! AcademyVisibility::canUseStudentAcademy($user)) {
            // Hide the in-development Academy rather than advertising that a private route exists.
            abort(404);
        }

        if (AcademyVisibility::shouldShowMaintenance($user)) {
            return response()->view('academy.maintenance', [], 503);
        }

        return $next($request);
    }
}
