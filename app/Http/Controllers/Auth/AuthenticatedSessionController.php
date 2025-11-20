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
<<<<<<< HEAD
=======
    /**
     * Mostrar la vista de inicio de sesión.
     */
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
    public function create(): View
    {
        return view('auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'),
        ]);
    }

<<<<<<< HEAD
=======
    /**
     * Gestionar una solicitud de autenticación entrante.
     */
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
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
<<<<<<< HEAD
            'ADMIN'           => 'admin.dashboard',
            'INSTRUCTOR'      => 'lider_semi.dashboard',
            'APRENDIZ'        => 'aprendiz.dashboard',
=======
            'ADMIN' => 'admin.dashboard',
            'LIDER_INTERMEDIARIO' => 'admin.dashboard',

            // CORRECCIÓN: Se incluye el rol INSTRUCTOR

            
            'INSTRUCTOR' => 'lider_semi.dashboard',

            'APRENDIZ' => 'aprendiz.dashboard',
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
            'LIDER_SEMILLERO' => 'lider_semi.dashboard',
            'LIDER_GENERAL'   => 'lider_general.dashboard',
        ];

        $route = $map[$rol] ?? 'dashboard';

        return redirect()->route($route);
    }

    /**
<<<<<<< HEAD
     * Cerrar sesión (ruta nueva).
=======
     * Destruir una sesión autenticada.
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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
