<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // ============================================================
    // LISTADO DE USUARIOS (con búsqueda, filtros y labels bonitos)
    // ============================================================
    public function index(Request $request)
    {
        $q           = trim($request->get('q',''));                 // filtro de texto
        $roleFilter  = trim($request->get('role',''));              // filtro de rol
        $semilleroId = $request->integer('semillero_id');           // filtro por semillero

        // Normaliza filtro para coincidir con el valor real en BD
        // En BD el super admin se guarda como 'LIDER GENERAL' (con espacio)
        if ($roleFilter === 'LIDER_GENERAL') {
            $roleFilter = 'LIDER GENERAL';
        }

        $usuarios = User::query()
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 'users.id')
            ->leftJoin('semilleros as s', 's.id_lider_semi', '=', 'ls.id_lider_semi')
            ->select([
                'users.*',
                DB::raw('s.nombre as semillero_nombre'),
                DB::raw('s.id_semillero as semillero_id'),
                DB::raw("CASE 
                            WHEN users.role = 'ADMIN'            THEN 'Líder grupo de investigación CIDEINNOVA'
                            WHEN users.role = 'LIDER GENERAL'    THEN 'Líder General'
                            WHEN users.role = 'LIDER_SEMILLERO'  THEN 'Líder semillero'
                            WHEN users.role = 'APRENDIZ'         THEN 'Aprendiz'
                            ELSE users.role
                         END AS role_label")
            ])
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('users.name','like',"%{$q}%")
                      ->orWhere('users.apellidos','like',"%{$q}%")
                      ->orWhere('users.email','like',"%{$q}%");
                });
            })
            ->when($roleFilter !== '', function($w) use ($roleFilter) {
                if ($roleFilter === 'LIDER_GENERAL' || $roleFilter === 'LIDER GENERAL') {
                    $w->whereIn('users.role', ['LIDER_GENERAL','LIDER GENERAL']);
                } else {
                    $w->where('users.role', $roleFilter);
                }
            })
            ->when($semilleroId, fn($w) => $w->where('s.id_semillero', $semilleroId))
            ->orderByDesc('users.created_at')
            ->paginate(12)
            ->withQueryString();

        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->orderBy('nombre')
            ->get();

        $roles = [
            'LIDER_GENERAL'       => 'Líder General',
            'ADMIN'               => 'Líder grupo de investigación CIDEINNOVA',
            'LIDER_SEMILLERO'     => 'Líder semillero',
            'APRENDIZ'            => 'Aprendiz',
        ];

        return view('admin.usuarios.index', [
            'usuarios'     => $usuarios,
            'semilleros'   => $semilleros,
            'roles'        => $roles,
            'q'            => $q,
            'roleFilter'   => $request->get('role',''),
            'semilleroId'  => $semilleroId,
        ]);
    }

    // ============================================================
    // FORMULARIO DE CREACIÓN DE USUARIO
    // ============================================================
    public function create()
    {
        $roles = [
            'ADMIN'           => 'Líder general',
            'LIDER_SEMILLERO' => 'Líder semillero',
            'APRENDIZ'        => 'Aprendiz',
        ];

        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->orderBy('nombre')
            ->get();

        return view('admin.usuarios.create', compact('roles','semilleros'));
    }

    // ============================================================
    // GUARDAR NUEVO USUARIO
    // ============================================================
    public function store(Request $request)
    {
        // Reglas base
        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

        // Reglas condicionales
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
                'ap_tipo_documento'       => 'nullable|string|max:5',
                'ap_documento'            => 'required|string|max:40',
                'ap_correo_institucional' => 'required|email|max:160',
                'ap_celular'              => 'nullable|string|max:30',
                'ap_contacto_nombre'      => 'nullable|string|max:160',
                'ap_contacto_celular'     => 'nullable|string|max:30',
            ];
        }

        // Mensajes y alias de atributos (ES)
        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'email'    => 'El campo :attribute debe ser un correo válido.',
            'max'      => 'El campo :attribute no puede superar :max caracteres.',
            'min'      => 'El campo :attribute debe tener al menos :min caracteres.',
            'in'       => 'El valor seleccionado de :attribute no es válido.',
            'unique'   => 'El :attribute ya está registrado.',
        ];
        $attributes = [
            'role'                   => 'rol',
            'nombre'                 => 'nombre',
            'apellido'               => 'apellido',
            'email'                  => 'correo',
            'password'               => 'contraseña',
            'ls_tipo_documento'      => 'tipo de documento (líder semillero)',
            'ls_documento'           => 'documento (líder semillero)',
            'ap_ficha'               => 'ficha (aprendiz)',
            'ap_programa'            => 'programa (aprendiz)',
            'ap_tipo_documento'      => 'tipo de documento (aprendiz)',
            'ap_documento'           => 'documento (aprendiz)',
            'ap_correo_institucional'=> 'correo institucional (aprendiz)',
            'ap_celular'             => 'celular (aprendiz)',
            'ap_contacto_nombre'     => 'contacto (aprendiz)',
            'ap_contacto_celular'    => 'celular del contacto (aprendiz)',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        // Normaliza "LIDER_GENERAL" -> "ADMIN"
        $role = match ($data['role']) {
            'LIDER_GENERAL' => 'ADMIN',
            default          => $data['role'],
        };

        // Helper para label visible
        $roleLabel = match ($role) {
            'ADMIN'               => 'Líder general',
            'LIDER_INTERMEDIARIO' => 'Líder intermediario',
            'LIDER_SEMILLERO'     => 'Líder semillero',
            'APRENDIZ'            => 'Aprendiz',
            default               => $role,
        };

        $userId = null;

        try {
            DB::transaction(function () use (&$userId, $data, $role) {
                // users
                $userId = DB::table('users')->insertGetId([
                    'name'       => $data['nombre'],
                    'apellidos'  => $data['apellido'],
                    'email'      => $data['email'],
                    'password'   => Hash::make($data['password']),
                    'role'       => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // perfil
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
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                            ? 'id_usuario'
                            : 'user_id';
                        DB::table('aprendices')->insert([
                            $colUserFk             => $userId,
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

            // Respuesta según expectativa del cliente (AJAX vs normal)
            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => true,
                    'message' => 'Usuario creado correctamente.',
                    'usuario' => [
                        'id'        => $userId,
                        'correo'    => $data['email'],
                        'rol'       => $role,
                        'rol_label' => $roleLabel,
                    ],
                ], 201);
            }

            return back()->with('success', 'Usuario creado correctamente.');

        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Ocurrió un error al crear el usuario.',
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])
                         ->withInput();
        }
    }

    // ============================================================
    // FORMULARIO DE EDICIÓN (solo vista)
    // ============================================================
    public function edit(User $usuario)
    {
        return $this->editForm($usuario);
    }

    public function editForm(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }
    

    // ============================================================
    // OBTENER DATOS PARA EDICIÓN (AJAX)
    // ============================================================
    public function editAjax($id)
    {
        $usuario = User::findOrFail($id);

        $perfil = null;
        switch ($usuario->role) {
            case 'ADMIN':
                $perfil = DB::table('administradores')->where('id_usuario', $id)->first();
                break;
            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero')->where('id_lider_semi', $id)->first();
                break;
            case 'APRENDIZ':
                $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                    ? 'id_usuario'
                    : 'user_id';
                $perfil = DB::table('aprendices')->where($colUserFk, $id)->first();
                break;
        }

        return response()->json(['usuario' => $usuario, 'perfil' => $perfil]);
    }

    // ============================================================
    // ACTUALIZAR USUARIO (form normal)
    // ============================================================
    public function update(Request $request, User $usuario)
    {
        $rules = [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($usuario->id)],
        ];
        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'email'    => 'El campo :attribute debe ser un correo válido.',
            'max'      => 'El campo :attribute no puede superar :max caracteres.',
            'unique'   => 'El :attribute ya está registrado.',
        ];
        $attributes = [
            'nombre'   => 'nombre',
            'apellido' => 'apellido',
            'email'    => 'correo',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        try {
            DB::transaction(function () use ($usuario, $data) {
                $usuario->name      = $data['nombre'];
                $usuario->apellidos = $data['apellido'];
                $usuario->email     = $data['email'];
                $usuario->save();

                switch ($usuario->role) {
                    case 'ADMIN':
                        DB::table('administradores')->where('id_usuario', $usuario->id)->update([
                            'nombres'        => $data['nombre'],
                            'apellidos'      => $data['apellido'],
                            'actualizado_en' => now(),
                        ]);
                        break;

                    case 'LIDER_SEMILLERO':
                        DB::table('lideres_semillero')->where('id_lider_semi', $usuario->id)->update([
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
                            'correo_institucional' => $data['email'],
                            'actualizado_en'       => now(),
                        ]);
                        break;

                    case 'APRENDIZ':
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                            ? 'id_usuario'
                            : 'user_id';
                        DB::table('aprendices')->where($colUserFk, $usuario->id)->update([
                            'nombres'         => $data['nombre'],
                            'apellidos'       => $data['apellido'],
                            'correo_personal' => $data['email'],
                            'actualizado_en'  => now(),
                        ]);
                        break;
                }
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => true,
                    'message' => 'Usuario actualizado correctamente.',
                ]);
            }

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');

        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Ocurrió un error al actualizar el usuario.',
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al actualizar el usuario.'])
                         ->withInput();
        }
    }

    // ============================================================
    // ELIMINAR USUARIO
    // ============================================================
    public function destroy(User $usuario)
    {
        try {
            DB::transaction(function () use ($usuario) {
                switch ($usuario->role) {
                    case 'ADMIN':
                        DB::table('administradores')->where('id_usuario', $usuario->id)->delete();
                        break;
                    case 'LIDER_SEMILLERO':
                        DB::table('lideres_semillero')->where('id_lider_semi', $usuario->id)->delete();
                        break;
                    case 'APRENDIZ':
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                            ? 'id_usuario'
                            : 'user_id';
                        DB::table('aprendices')->where($colUserFk, $usuario->id)->delete();
                        break;
                }

                $usuario->delete();
            });

            if (request()->wantsJson()) {
                return response()->json([
                    'ok'      => true,
                    'message' => 'Usuario eliminado correctamente.',
                ]);
            }

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');

        } catch (\Throwable $e) {
            report($e);

            if (request()->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Ocurrió un error al eliminar el usuario.',
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al eliminar el usuario.']);
        }
    }

    // ============================================================
    // CREAR USUARIO POR AJAX (opcional)
    // ============================================================
    public function storeAjax(Request $request)
    {
        // Reutiliza la misma lógica de store(); si el cliente pide JSON, se lo enviamos.
        return $this->store($request);
    }
}
