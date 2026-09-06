<?php

namespace App\Http\Middleware;

use App\Services\AcademyVisibility;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAcademyGrader
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (AcademyVisibility::canUseStaffTools($user) && $user->canGradeAcademy()) {
            return $next($request);
        }

        abort(403, 'Only enabled instructors and administrators may grade Academy self assessments.');
    }
}
