<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo'   => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [], [
            'correo' => 'correo',
            'password' => 'contraseña',
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'correo' => 'Las credenciales no son válidas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function username()
    {
        return 'correo';
    }
}

