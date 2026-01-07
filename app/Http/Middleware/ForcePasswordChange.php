<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check() &&
            Auth::user()->force_password_change &&
            !$request->is('password/change') &&
            !$request->is('logout')
        ) {
            return redirect()->route('password.change.form');
        }

        return $next($request);
    }
}
