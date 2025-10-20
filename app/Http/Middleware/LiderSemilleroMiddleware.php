<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LiderSemilleroMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'LIDER_SEMILLERO') {
            return redirect('/')->with('error', 'No tienes acceso a esta sección');
        }

        return $next($request);
    }
}