<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validar lo básico
        $request->validate([
            'correo' => ['required', 'email', 'max:160', 'unique:usuarios,correo'],
            'rol' => ['required', Rule::in(['ADMINISTRADOR', 'LIDER_SEMILLERO', 'APRENDIZ'])],
        ]);

        // 2️⃣ Crear el usuario principal
        $usuario = Usuario::create([
            'correo' => $request->correo,
            'password_hash' => Hash::make('cambiar123'), // contraseña por defecto
            'rol' => $request->rol,
            'estado' => 'ACTIVO',
        ]);

        // 3️⃣ Si es administrador, puedes guardar datos extra en otra tabla (más adelante)
        if ($request->rol === 'ADMINISTRADOR') {
            // Aquí podrías crear su perfil más adelante
        }

        // 4️⃣ Mensaje de éxito
        return redirect()->back()->with('success', '✅ Usuario creado correctamente con rol ' . $request->rol);
    }
}
