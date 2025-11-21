<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View; // Importación necesaria para tipado de vistas Blade

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista de inicio de sesión.
     */
    public function create(): View
    {
        // Esto devuelve la vista Blade 'resources/views/auth/login.blade.php'
        return view('auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Gestionar una solicitud de autenticación entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        // Nota: Se recomienda normalizar el rol antes de usarlo.
        $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

        $map = [
            'ADMIN' => 'admin.dashboard',
            'LIDER_INTERMEDIARIO' => 'admin.dashboard',

            // CORRECCIÓN: Se incluye el rol INSTRUCTOR

            
            'INSTRUCTOR' => 'lider_semi.dashboard',

            'APRENDIZ' => 'aprendiz.dashboard',
            'LIDER_SEMILLERO' => 'lider_semi.dashboard',

            // CORRECCIÓN: Se usa el nombre de ruta correcto 'lider_general.dashboard'
            'LIDER_GENERAL' => 'lider_general.dashboard',
        ];

        // Redirige a la ruta específica del rol, o a 'dashboard' como fallback
        $route = $map[$rol] ?? 'dashboard';

        return redirect()->route($route);
    }

    /**
     * Destruir una sesión autenticada.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
