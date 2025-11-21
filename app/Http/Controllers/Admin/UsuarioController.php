<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;


class UsuarioController extends Controller
{
    // ============================================================
    // LISTADO DE USUARIOS (con búsqueda, filtros y labels bonitos)
    // ============================================================
public function index(Request $request)
{
    $q           = trim($request->get('q',''));
    $roleFilter  = trim($request->get('role',''));
    $semilleroId = $request->integer('semillero_id');

    if ($roleFilter === 'LIDER_GENERAL') $roleFilter = 'ADMIN';

    $aprFk = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';

    $usuarios = User::query()
        ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 'users.id')
        ->leftJoin('semilleros as sl', 'sl.id_lider_semi', '=', 'ls.id_lider_semi')
        ->leftJoin('aprendices as ap', "ap.$aprFk", '=', 'users.id')
        ->leftJoin('semilleros as sa', 'sa.id_semillero', '=', 'ap.semillero_id')
        ->select([
            'users.*',
            DB::raw('COALESCE(sa.nombre, sl.nombre) as semillero_nombre'),
            DB::raw('COALESCE(sa.id_semillero, sl.id_semillero) as semillero_id'),
            DB::raw('COALESCE(sa.linea_investigacion, sl.linea_investigacion) as linea_investigacion'),
            DB::raw("CASE
                                WHEN users.role = 'ADMIN'             THEN 'Líder general'
                                WHEN users.role = 'LIDER_SEMILLERO'   THEN 'Líder semillero'
                                WHEN users.role = 'APRENDIZ'          THEN 'Aprendiz'
                                WHEN users.role = 'LIDER_INVESTIGACION' THEN 'Líder de investigación'  -- 👈 NUEVO
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
        ->when($roleFilter !== '', fn($w) => $w->where('users.role', $roleFilter))
        ->when($semilleroId, function ($w) use ($semilleroId) {
            $w->where(function ($x) use ($semilleroId) {
                $x->where('sa.id_semillero', $semilleroId)
                  ->orWhere('sl.id_semillero', $semilleroId);
            });
        })
        ->distinct('users.id') // <- opcional
        ->orderByDesc('users.created_at')
        ->paginate(12)
        ->withQueryString();

    $semilleros = DB::table('semilleros')
        ->select('id_semillero','nombre')
        ->orderBy('nombre')
        ->get();

    $roles = [
        'ADMIN'           => 'Líder general',
        'LIDER_SEMILLERO' => 'Líder semillero',
        'LIDER_INVESTIGACION'=> 'Líder de investigación', 
        'APRENDIZ'        => 'Aprendiz',
    ];

    return view('admin.usuarios.index', compact('usuarios','semilleros','roles','q') + [
        'roleFilter'  => $request->get('role',''),
        'semilleroId' => $semilleroId,
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
            'LIDER_INVESTIGACION'=> 'Líder de investigación',
            'APRENDIZ'        => 'Aprendiz',
        ];

        // Trae también linea_investigacion para el data-linea del select
        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre','linea_investigacion')
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
        'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,LIDER_INVESTIGACION,APRENDIZ',
        'nombre'   => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];

    // Reglas condicionales: Líder
    if ($request->role === 'LIDER_SEMILLERO') {
        $rules += [
            'ls_tipo_documento' => 'required|string|max:5',
            'ls_documento'      => 'required|string|max:40',
        ];
    }

    // Reglas condicionales: Aprendiz (SENA / No SENA)
    if ($request->role === 'APRENDIZ') {
        // base
        $rules += [
            'radio_vinculado_sena'    => 'required|in:1,0',
            'semillero_id'            => 'required|integer|exists:semilleros,id_semillero',
            'ap_tipo_documento'       => 'nullable|string|max:5',
            'ap_documento'            => 'required|string|max:40',
            'ap_correo_institucional' => 'required|email|max:160',
            'ap_celular'              => 'nullable|string|max:30',
            'ap_contacto_nombre'      => 'nullable|string|max:160',
            'ap_contacto_celular'     => 'nullable|string|max:30',
            // decidir según radio
            'ap_ficha'                => 'nullable|string|max:30',
            'ap_programa'             => 'nullable|string|max:160',
            'institucion'             => 'nullable|string|max:160',
        ];

        // refuerzo condicional
        if ((int)$request->input('radio_vinculado_sena') === 1) {
            // SÍ está en SENA → pedir ficha/programa
            $rules['ap_ficha']    = 'required|string|max:30';
            $rules['ap_programa'] = 'required|string|max:160';
        } else {
            // NO está en SENA → pedir institución
            $rules['institucion'] = 'required|string|max:160';
        }
    }

    // Mensajes y alias
    $messages = [
        'required' => 'El campo :attribute es obligatorio.',
        'email'    => 'El campo :attribute debe ser un correo válido.',
        'max'      => 'El campo :attribute no puede superar :max caracteres.',
        'min'      => 'El campo :attribute debe tener al menos :min caracteres.',
        'in'       => 'El valor seleccionado de :attribute no es válido.',
        'unique'   => 'El :attribute ya está registrado.',
        'exists'   => 'El :attribute no es válido.',
    ];

    $attributes = [
        'role'                    => 'rol',
        'nombre'                  => 'nombre',
        'apellido'                => 'apellido',
        'email'                   => 'correo',
        'password'                => 'contraseña',
        'ls_tipo_documento'       => 'tipo de documento (líder semillero)',
        'ls_documento'            => 'documento (líder semillero)',
        'radio_vinculado_sena'    => '¿Vinculado al SENA?',
        'ap_ficha'                => 'ficha (aprendiz)',
        'ap_programa'             => 'programa (aprendiz)',
        'institucion'             => 'institución',
        'ap_tipo_documento'       => 'tipo de documento (aprendiz)',
        'ap_documento'            => 'documento (aprendiz)',
        'ap_correo_institucional' => 'correo institucional (aprendiz)',
        'ap_celular'              => 'celular (aprendiz)',
        'ap_contacto_nombre'      => 'contacto (aprendiz)',
        'ap_contacto_celular'     => 'celular del contacto (aprendiz)',
        'semillero_id'            => 'semillero',
    ];

    $data = $request->validate($rules, $messages, $attributes);

    // Normaliza "LIDER_GENERAL" -> "ADMIN"
    $role = match ($data['role']) {
        'LIDER_GENERAL' => 'ADMIN',
        default          => $data['role'],
    };

    // Label amigable
    $roleLabel = match ($role) {
        'ADMIN'           => 'Líder general',
        'LIDER_SEMILLERO' => 'Líder semillero',
        'LIDER_INVESTIGACION'=> 'Líder de investigación',
        'APRENDIZ'        => 'Aprendiz',
        default           => $role,
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

                    case 'LIDER_INVESTIGACION': 
                    DB::table('lideres_investigacion')->insert([
                        'user_id'    => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                case 'APRENDIZ':
                    $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                        ? 'id_usuario'
                        : 'user_id';

                    $vinc = (int)($data['radio_vinculado_sena'] ?? 1); // 1 = SENA, 0 = otra institución

                    DB::table('aprendices')->insert([
                        $colUserFk             => $userId,
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],

                        // condicionados por vinculado_sena
                        'ficha'                => $vinc ? ($data['ap_ficha'] ?? null) : null,
                        'programa'             => $vinc ? ($data['ap_programa'] ?? null) : null,
                        'vinculado_sena'       => $vinc,
                        'institucion'          => $vinc ? null : ($data['institucion'] ?? null),

                        // resto de campos
                        'tipo_documento'       => $data['ap_tipo_documento'] ?? null,
                        'documento'            => $data['ap_documento'],
                        'celular'              => $data['ap_celular'] ?? null,
                        'correo_institucional' => $data['ap_correo_institucional'],
                        'correo_personal'      => $data['email'],
                        'contacto_nombre'      => $data['ap_contacto_nombre'] ?? null,
                        'contacto_celular'     => $data['ap_contacto_celular'] ?? null,

                        // semillero siempre requerido
                        'semillero_id'         => $data['semillero_id'],

                        'creado_en'            => now(),
                        'actualizado_en'       => now(),
                    ]);
                    break;
            } // <-- FIN switch
        }); // <-- FIN transaction

        // Respuesta OK (dentro del try)
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

        // Usamos el helper request() para evitar avisos de variable indefinida en algunos analizadores
        if (request()->wantsJson()) {
            return response()->json([
                'ok'      => false,
                'message' => 'Ocurrió un error al crear el usuario.',
            ], 500);
        }

        return back()
            ->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])
            ->withInput();
    }
}

    // ============================================================
    // FORMULARIO DE EDICIÓN (solo vista)
    // ============================================================
    public function editForm(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        return $this->editForm($usuario);
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

             case 'LIDER_INVESTIGACION': // 👈 NUEVO (opcional)
        $perfil = DB::table('lideres_investigacion')->where('user_id', $id)->first();
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

        // Si es aprendiz, permitimos actualizar el semillero también
        if ($usuario->role === 'APRENDIZ') {
            $rules['semillero_id'] = 'required|integer|exists:semilleros,id_semillero';
        }

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'email'    => 'El campo :attribute debe ser un correo válido.',
            'max'      => 'El campo :attribute no puede superar :max caracteres.',
            'unique'   => 'El :attribute ya está registrado.',
            'exists'   => 'El :attribute no es válido.',
        ];

        $attributes = [
            'nombre'       => 'nombre',
            'apellido'     => 'apellido',
            'email'        => 'correo',
            'semillero_id' => 'semillero',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        try {
            DB::transaction(function () use ($usuario, $data) {
                // users
                $usuario->name      = $data['nombre'];
                $usuario->apellidos = $data['apellido'];
                $usuario->email     = $data['email'];
                $usuario->save();

                // perfil según rol
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

                        $updateApr = [
                            'nombres'         => $data['nombre'],
                            'apellidos'       => $data['apellido'],
                            'correo_personal' => $data['email'],
                            'actualizado_en'  => now(),
                        ];

                        if (array_key_exists('semillero_id', $data)) {
                            $updateApr['semillero_id'] = $data['semillero_id'];
                        }

                        DB::table('aprendices')->where($colUserFk, $usuario->id)->update($updateApr);
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

            if (request()->wantsJson()) {
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
                    case 'LIDER_INVESTIGACION': // 👈 NUEVO
                        DB::table('lideres_investigacion')->where('user_id', $usuario->id)->delete();
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
