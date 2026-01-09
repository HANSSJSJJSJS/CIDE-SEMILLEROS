<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle($request, Closure $next)
    {
        if (
            Auth::check() &&
            Auth::user()->must_change_password == 1 &&
            !$request->routeIs('password.change.*') &&
            !$request->routeIs('logout')
        ) {
            return redirect()->route('password.change.form');
        }

        return $next($request);
    }
}
