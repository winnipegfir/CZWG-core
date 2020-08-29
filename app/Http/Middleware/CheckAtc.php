<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class CheckAtc
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next)
     {
         if (Auth::check()) {
             if (Auth::user()->permissions >= 1) {
                 return $next($request);
             }
         }

         abort(403, 'Only Current Controllers have access to this resource!');
     }
}
