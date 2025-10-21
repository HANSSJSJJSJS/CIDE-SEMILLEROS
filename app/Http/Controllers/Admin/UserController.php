<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\Administrador;
use App\Models\LiderGeneral;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Mostrar formulario para crear un nuevo usuario.
     */
    public function create()
    {
        // Lista de roles disponibles
        $roles = [
            'ADMIN'            => 'Administrador',
            'LIDER GENERAL'    => 'Líder General',
            'LIDER SEMILLERO'  => 'Líder Semillero',
            'APRENDIZ'         => 'Aprendiz',
        ];

        return view('admin.usuarios.create', compact('roles'));
    }

    /**
     * Guardar un nuevo usuario y su información relacionada.
     */
    public function store(StoreUserRequest $request)
    {
        return DB::transaction(function () use ($request) {

            // 1️⃣ Combinar nombre y apellido para guardar en 'name'
            $fullName = trim($request->input('nombre') . ' ' . $request->input('apellido'));

            // 2️⃣ Crear el usuario base en tabla users
            $user = User::create([
                'name'     => $fullName,
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role'     => $request->input('role'), // ADMIN / LIDER GENERAL / LIDER SEMILLERO / APRENDIZ
            ]);

            // 3️⃣ Crear registro adicional según el rol
            switch ($request->input('role')) {
                case 'ADMIN':
                    $admin = new Administrador();
                    $admin->id_usuario = $user->id;
                    $admin->nombre     = $request->input('nombre'); // 👈 añade esto
                    $admin->save();
                    break;

                case 'LIDER GENERAL':
                    $liderGeneral = new LiderGeneral();
                    $liderGeneral->id_usuario = $user->id;
                    $liderGeneral->save();
                    break;

                case 'LIDER SEMILLERO':
                    $liderSemi = new LiderSemillero();
                    $liderSemi->id_usuario        = $user->id;
                    $liderSemi->tipo_documento    = $request->input('tipo_documento_lider');
                    $liderSemi->numero_documento  = $request->input('numero_documento_lider');
                    $liderSemi->save();
                    break;

                case 'APRENDIZ':
                    $aprendiz = new Aprendiz();
                    $aprendiz->id_usuario            = $user->id;
                    $aprendiz->tipo_documento        = $request->input('tipo_documento_aprendiz');
                    $aprendiz->numero_documento      = $request->input('numero_documento_aprendiz');
                    $aprendiz->ficha                 = $request->input('ficha');
                    $aprendiz->programa_formacion    = $request->input('programa_formacion');
                    $aprendiz->celular               = $request->input('celular');
                    $aprendiz->correo_institucional  = $request->input('correo_institucional');
                    $aprendiz->contacto_emergencia   = $request->input('contacto_emergencia');
                    $aprendiz->numero_contrato       = $request->input('numero_contrato');
                    $aprendiz->save();
                    break;
            }

            // 4️⃣ Redirigir con mensaje de éxito
            return redirect()
                ->route('admin.usuarios.create')
                ->with('status', 'Usuario creado correctamente.');
        });
    }
}
