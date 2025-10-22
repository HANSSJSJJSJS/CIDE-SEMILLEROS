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
    /**
     * Listado de usuarios (para mostrar en el dashboard).
     */
    public function index()
    {
        // Recupera todos los usuarios con su rol
        $usuarios = User::latest()->get();
        return view('admin.dashboard-admin', compact('usuarios'));
    }

    /**
     * Guarda un usuario nuevo desde el formulario del modal (Dashboard Admin).
     */
    public function store(Request $request)
    {
        // Normalizamos el rol según tu BD (con espacios o guiones bajos)
        $roleMap = [
            'ADMIN'           => 'ADMIN',
            'LIDER_GENERAL'   => 'LIDER GENERAL',   // en BD se guarda con espacio
            'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
            'APRENDIZ'        => 'APRENDIZ',
        ];

        // Validación base
        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

        // Líder Semillero: requiere documento y tipo
        if ($request->role === 'LIDER_SEMILLERO') {
            $rules = array_merge($rules, [
                'ls_tipo_documento' => 'required|string|max:5',
                'ls_documento'      => 'required|string|max:40',
            ]);
        }

        // Aprendiz: requiere ficha, programa, documento y correo institucional
        if ($request->role === 'APRENDIZ') {
            $rules = array_merge($rules, [
                'ap_ficha'                => 'required|string|max:30',
                'ap_programa'             => 'required|string|max:160',
                'ap_tipo_documento'       => 'required|string|max:5',
                'ap_documento'            => 'required|string|max:40',
                'ap_correo_institucional' => 'required|email|max:160',
                'ap_celular'              => 'nullable|string|max:30',
                'ap_contacto_nombre'      => 'nullable|string|max:160',
                'ap_contacto_celular'     => 'nullable|string|max:30',
            ]);
        }

        $data = $request->validate($rules);
        $role = $roleMap[$data['role']];

        try {
            DB::transaction(function () use ($data, $role) {

                // 1Crear usuario base
                $userId = DB::table('users')->insertGetId([
                    'name'       => $data['nombre'],
                    'apellidos'  => $data['apellido'],
                    'email'      => $data['email'],
                    'password'   => Hash::make($data['password']),
                    'role'       => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2️⃣ Crear perfil según rol (usando estructura exacta de tu BD)
                switch ($role) {
                    case 'ADMIN':
                        DB::table('administradores')->insert([
                            'id_usuario'     => $userId,
                            'nombres'        => $data['nombre'],
                            'apellidos'      => $data['apellido'],
                            'creado_en'      => now(),
                            'actualizado_en' => now(),
                        ]);
                        break;

                    case 'LIDER GENERAL':
                        DB::table('lider_general')->insert([
                            'id_lidergen'          => $userId,
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
                            'Correo_institucional' => $data['email'],
                            'creado_en'            => now(),
                            'actualizado_en'       => now(),
                        ]);
                        break;

                    case 'LIDER_SEMILLERO':
                        DB::table('lideres_semillero')->insert([
                            'id_lider_semi'        => $userId,
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
                            'tipo_documento'       => $data['ls_tipo_documento'],
                            'documento'            => $data['ls_documento'],
                            'correo_institucional' => $data['email'],
                            'creado_en'            => now(),
                            'actualizado_en'       => now(),
                        ]);
                        break;

                    case 'APRENDIZ':
                        DB::table('aprendices')->insert([
                            'user_id'           => $userId,
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
                            'ficha'                => $data['ap_ficha'],
                            'programa'             => $data['ap_programa'],
                            'tipo_documento'       => $data['ap_tipo_documento'],
                            'documento'            => $data['ap_documento'],
                            'celular'              => $data['ap_celular'] ?? null,
                            'correo_institucional' => $data['ap_correo_institucional'],
                            'correo_personal'      => $data['email'],
                            'contacto_nombre'      => $data['ap_contacto_nombre'] ?? null,
                            'contacto_celular'     => $data['ap_contacto_celular'] ?? null,
                            'creado_en'            => now(),
                            'actualizado_en'       => now(),
                        ]);
                        break;
                }
            });

            return back()->with('success', 'Usuario creado correctamente.');

        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])
                ->withInput();
        }
    }
}
