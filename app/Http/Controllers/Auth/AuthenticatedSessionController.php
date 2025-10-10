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

        switch ($user->role) {
            case 'ADMIN':
                return redirect('/admin/dashboard');
            case 'INSTRUCTOR':
                return redirect('/instructor/dashboard');
            case 'APRENDIZ':
                return redirect('/aprendiz/dashboard');
            case 'LIDER GENERAL':
                return redirect('/lider/dashboard');
            default:
                return redirect('/');
        }
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
