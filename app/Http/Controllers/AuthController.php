<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Muestra el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesa el formulario de login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Si las credenciales son correctas
            return redirect()->intended('/dashboard')->with('success', 'Inicio de sesión exitoso');
        }

        // Si falló el login
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }
}
