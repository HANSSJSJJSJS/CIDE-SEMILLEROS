<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View; // Importación necesaria para tipado de vistas Blade

// Se eliminan las importaciones de Inertia ya que estás usando Blade
// use Inertia\Inertia;
// use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View // Cambiado de Response a View
    {
        // Esto devuelve la vista Blade 'resources/views/auth/login.blade.php'
        return view('auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
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
            // El rol INSTRUCTOR puede mapearse a lider_semi.dashboard
            'INSTRUCTOR' => 'lider_semi.dashboard',
            'APRENDIZ' => 'aprendiz.dashboard',
            'LIDER_SEMILLERO' => 'lider_semi.dashboard',
            'LIDER_GENERAL' => 'lider.dashboard',
            // Asegúrate de que los nombres de rol aquí coincidan con los de tu base de datos
        ];

        // Redirige a la ruta específica del rol, o a 'dashboard' como fallback
        $route = $map[$rol] ?? 'dashboard';

        return redirect()->route($route);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
