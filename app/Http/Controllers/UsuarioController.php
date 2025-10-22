<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Modelos
use App\Models\User;
use App\Models\Administrador;
use App\Models\LiderGeneral;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;

class UsuarioController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validación base
        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
            'nombre'   => 'required|string|max:120',
            'apellido' => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

        if ($request->role === 'LIDER_SEMILLERO') {
            $rules += [
                'ls_tipo_documento' => 'required|string|max:5',
                'ls_documento'      => 'required|string|max:40',
            ];
        }

        if ($request->role === 'APRENDIZ') {
            $rules += [
                'ap_ficha'                => 'required|string|max:30',
                'ap_programa'             => 'required|string|max:160',
                'ap_tipo_documento'       => 'required|string|max:5',
                'ap_documento'            => 'required|string|max:40',
                'ap_correo_institucional' => 'required|email|max:160',
                'ap_celular'              => 'nullable|string|max:30',
                'ap_contacto_nombre'      => 'nullable|string|max:160',
                'ap_contacto_celular'     => 'nullable|string|max:30',
            ];
        }

        $request->validate($rules);

        // 2️⃣ Inserciones con transacción
        try {
            DB::transaction(function () use ($request) {

                // USERS (usa name + apellido)
                $user = User::create([
                    'name'     => $request->nombre,
                    'apellido' => $request->apellido,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => $request->role,
                ]);

                switch ($request->role) {
                    case 'ADMIN':
                        Administrador::create([
                            'id_usuario' => $user->id,
                            'nombres'    => $request->nombre,
                            'apellido'   => $request->apellido,
                        ]);
                        break;

                    case 'LIDER_GENERAL':
                        LiderGeneral::create([
                            'id_lidergen'          => $user->id,
                            'nombres'              => $request->nombre,
                            'apellido'             => $request->apellido,
                            'Correo_institucional' => $request->email,
                        ]);
                        break;

                    case 'LIDER_SEMILLERO':
                        LiderSemillero::create([
                            'id_lider_semi'        => $user->id,
                            'nombres'              => $request->nombre,
                            'apellido'             => $request->apellido,
                            'tipo_documento'       => $request->ls_tipo_documento,
                            'documento'            => $request->ls_documento,
                            'correo_institucional' => $request->email,
                        ]);
                        break;

                     case 'APRENDIZ':
                        Aprendiz::updateOrCreate(
                            ['id_usuario' => $user->id],   // busca por id_usuario (único)
                            [
                                // NO pongas 'id_aprendiz' 
                                'nombres'              => $request->nombre,
                                'apellido'             => $request->apellido,
                                'ficha'                => $request->ap_ficha,
                                'programa'             => $request->ap_programa,
                                'tipo_documento'       => $request->ap_tipo_documento,
                                'documento'            => $request->ap_documento,
                                'celular'              => $request->ap_celular,
                                'correo_institucional' => $request->ap_correo_institucional,
                                'correo_personal'      => $request->email, // personal = login
                                'contacto_nombre'      => $request->ap_contacto_nombre,
                                'contacto_celular'     => $request->ap_contacto_celular,
                            ]
                        );
                        break;
                                    }
            });

            return back()->with('success', 'Usuario registrado correctamente.');

        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['general' => 'Error al crear el usuario: '.$e->getMessage()])
                ->withInput();
        }
    }
}
