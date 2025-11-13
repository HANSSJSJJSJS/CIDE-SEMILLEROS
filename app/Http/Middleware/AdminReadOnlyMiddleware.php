<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReadOnlyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            $normalized = strtoupper(str_replace([' ', '-'], '_', $user->role));

            // Solo LIDER_GENERAL: sin restricciones
            if ($normalized === 'LIDER_GENERAL') {
                return $next($request);
            }

            // ADMIN (CIDEINNOVA): solo lectura por defecto, salvo permisos por módulo
            if ($normalized === 'ADMIN') {
                $method = $request->getMethod();
                if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
                    return $next($request);
                }

                // Detectar módulo por nombre de ruta admin.modulo.* o por URL /admin/modulo/...
                $routeName = $request->route() ? $request->route()->getName() : null;
                $module = null;
                if ($routeName && str_starts_with($routeName, 'admin.')) {
                    $parts = explode('.', $routeName);
                    $module = $parts[1] ?? null;
                }
                if (!$module) {
                    $segments = $request->segments();
                    if ((count($segments) > 1) && strtolower($segments[0]) === 'admin') {
                        $module = $segments[1];
                    }
                }

                // Mapear método HTTP a columna de permiso
                $flag = match ($method) {
                    'POST'   => 'can_create',
                    'PUT',
                    'PATCH'  => 'can_update',
                    'DELETE' => 'can_delete',
                    default  => null,
                };

                if ($module && $flag) {
                    // Normaliza clave de módulo heredada
                    $key = $module === 'reuniones' ? 'reuniones-lideres' : $module;
                    $allowed = DB::table('user_module_permissions')
                        ->where('user_id', $user->id)
                        ->where('module', $key)
                        ->value($flag);
                    if ($allowed) {
                        return $next($request);
                    }
                }

                return redirect()->route('admin.dashboard')
                    ->withErrors(['error' => 'No tienes permisos de escritura en esta sección.']);
            }
        }
        return $next($request);
    }
}

