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
     * (Opcional) Listado de usuarios del panel admin.
     * Ajusta la vista si usas otro nombre/ubicación.
     */
    public function index()
    {
        // Si ya tienes tu propia vista, cámbiala aquí:
        // return view('admin.usuarios.index', [...]);

        $usuarios = User::latest()->get();
        return view('admin.dashboard-admin', compact('usuarios'));
    }

    /**
     * Guardado TRADICIONAL (submit normal del formulario del modal).
     * Valida según el rol y crea registros en las tablas relacionadas.
     */
   public function store(Request $request)
{
    // Reglas base (rol primero en UX, pero aquí validamos todo)
    $rules = [
        'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
        'nombre'   => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];

    // Líder semillero: documento obligatorio, institucional = login (no se pide otro)
    if ($request->role === 'LIDER_SEMILLERO') {
        $rules = array_merge($rules, [
            'ls_tipo_documento' => 'required|string|max:5',
            'ls_documento'      => 'required|string|max:40',
        ]);
    }

    // Aprendiz: institucional obligatorio; personal = login (no se pide)
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

    $validated = $request->validate($rules);

    try {
        DB::transaction(function () use ($request) {

            // USERS: tu BD usa 'name' y 'apellidos'
            $user = User::create([
                'name'      => trim($request->nombre . ' ' . $request->apellido),
                'apellidos' => $request->apellido,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => $request->role,
            ]);

            switch ($request->role) {
                case 'ADMIN':
                    // administradores: columnas 'nombre' y 'apellidos'
                    Administrador::create([
                        'id_usuario' => $user->id,
                        'nombre'     => $request->nombre,
                        'apellidos'  => $request->apellido,
                    ]);
                    break;

                case 'LIDER_GENERAL':
                    // lider_general: columnas 'nombres', 'apellidos', 'Correo_institucional'
                    LiderGeneral::create([
                        'id_usuario'           => $user->id,
                        'nombres'              => $request->nombre,
                        'apellidos'            => $request->apellido,
                        'Correo_institucional' => $request->email, // = login
                    ]);
                    break;

                case 'LIDER_SEMILLERO':
                    // lideres_semillero: 'nombres', 'apellidos', 'tipo_documento', 'documento', 'correo_institucional'
                    LiderSemillero::create([
                        'id_usuario'           => $user->id,
                        'nombres'              => $request->nombre,
                        'apellidos'            => $request->apellido,
                        'tipo_documento'       => $request->ls_tipo_documento,
                        'documento'            => $request->ls_documento,
                        'correo_institucional' => $request->email, // = login
                    ]);
                    break;

                case 'APRENDIZ':
                    // aprendices: 'nombres','apellidos','ficha','programa','tipo_documento','documento','celular','correo_institucional','correo_personal', etc.
                    Aprendiz::create([
                        'id_usuario'           => $user->id,
                        'nombres'              => $request->nombre,
                        'apellidos'            => $request->apellido,
                        'ficha'                => $request->ap_ficha,
                        'programa'             => $request->ap_programa,
                        'tipo_documento'       => $request->ap_tipo_documento,
                        'documento'            => $request->ap_documento,
                        'celular'              => $request->ap_celular,
                        'correo_institucional' => $request->ap_correo_institucional, // pedido en form
                        'correo_personal'      => $request->email, // = login
                        'contacto_nombre'      => $request->ap_contacto_nombre,
                        'contacto_celular'     => $request->ap_contacto_celular,
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