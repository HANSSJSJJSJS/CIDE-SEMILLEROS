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

        // Mapea roles a rutas nombradas (ajusta los keys si tus roles en BD son diferentes)
        $map = [
            'ADMIN' => 'admin.dashboard',
            'LIDER SEMILLERO' => 'lider_semi.dashboard',   // coincide con routes/web.php
            'APRENDIZ' => 'aprendiz.dashboard',
            'LIDER GENERAL' => 'lider_general.dashboard', // coincide con routes/web.php
        ];

        $roleKey = strtoupper(trim($user->rol ?? ''));

        if (isset($map[$roleKey])) {
            return redirect()->route($map[$roleKey]);
        }

        // fallback al dashboard general
        return redirect()->route('dashboard');
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
