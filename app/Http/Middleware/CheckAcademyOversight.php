<?php

namespace App\Http\Middleware;

use App\Services\AcademyVisibility;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAcademyOversight
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (AcademyVisibility::canUseStaffTools($user) && $user->canOverseeAcademy()) {
            return $next($request);
        }

        abort(403, 'Academy staff tools are currently restricted by an administrator.');
    }
}
