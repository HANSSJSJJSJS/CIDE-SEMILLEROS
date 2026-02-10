<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PasswordController extends Controller
{
    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function showChangeForm()
    {
        return view('auth.password-change');
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:10',
                'regex:/[A-Z]/', // al menos una mayúscula
                'regex:/[0-9]/', // al menos un número
                'confirmed',
            ],
        ], [
            'password.min' => 'La contraseña debe tener mínimo 10 caracteres.',
            'password.regex' => 'Debe contener al menos una mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $update = [
            'password' => Hash::make($request->password),
        ];

        if (Schema::hasColumn('users', 'must_change_password')) {
            $update['must_change_password'] = 0;
        }

        Auth::user()->update($update);

        $user = Auth::user();

        return redirect()->route(match ($user->role) {
            'ADMIN', 'LIDER_INVESTIGACION' => 'admin.dashboard',
            'LIDER_SEMILLERO'             => 'lider_semi.dashboard',
            'APRENDIZ'                    => 'aprendiz.dashboard',
            default                       => 'home',
        })->with('success', 'Contraseña actualizada correctamente.');
    }
}
