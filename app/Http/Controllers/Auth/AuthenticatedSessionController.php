<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

        $map = [
            'ADMIN' => 'admin.dashboard',
            'INSTRUCTOR' => 'instructor.dashboard',
            'APRENDIZ' => 'aprendiz.dashboard',
            'LIDER_SEMILLERO' => 'lider_semi.instructor.dashboard',
            'LIDER_GENERAL' => 'lider.dashboard',
        ];

        $route = $map[$rol] ?? 'dashboard';

        return redirect()->route($route);
    }


    /**
     * Cerrar la sesión del usuario autenticado.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
