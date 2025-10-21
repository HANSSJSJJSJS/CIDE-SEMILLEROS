<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

        // Normaliza parámetros: puede venir "ADMIN,INSTRUCTOR" o varios args
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = trim($part);
                if ($part === '') continue;
                $allowed[] = strtoupper(str_replace([' ', '-'], '_', $part));
            }
        }

        if (! in_array($userRole, $allowed, true)) {
            // redirigir al dashboard genérico o abort(403)
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes permisos para acceder a esta sección.']);
        }

        return $next($request);
    }
}
