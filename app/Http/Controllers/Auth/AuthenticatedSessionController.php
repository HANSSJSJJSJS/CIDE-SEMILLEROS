<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        if ($user) {
            $user->last_login_at = now();   // columna nueva
            $user->save();
        }

        $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

            $map = [
                'ADMIN'               => 'admin.dashboard',
                'LIDER_GENERAL'       => 'admin.dashboard',         // si lo usas
                'LIDER_INVESTIGACION' => 'admin.dashboard',         // 👈 NUEVO

                'LIDER_SEMILLERO'     => 'lider_semi.dashboard',
                'INSTRUCTOR'          => 'lider_semi.dashboard',

                'APRENDIZ'            => 'aprendiz.dashboard',
            ];

            $route = $map[$rol] ?? 'dashboard';
            return redirect()->route($route);
                }

    /**
     * Cerrar sesión (ruta nueva).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Compatibilidad con rutas viejas que llaman destroy().
     * Cualquier ruta que use destroy ahora reutiliza logout().
     */
    public function destroy(Request $request): RedirectResponse
    {
        return $this->logout($request);
    }
}
