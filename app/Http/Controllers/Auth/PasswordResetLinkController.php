<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Mostrar formulario "Olvidé mi contraseña"
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar enlace de recuperación
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'status',
            'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.'
        );
    }
}
