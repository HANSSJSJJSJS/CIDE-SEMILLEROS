<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Administrador;
use App\Models\LiderSemillero;
use App\Models\LiderInvestigacion;
use App\Models\Aprendiz;

class UsuarioController extends Controller
{
    // ============================================================
    // LISTADO DE USUARIOS
    // ============================================================
    public function index(Request $request)
    {
        $q           = trim($request->get('q',''));
        $roleFilter  = trim($request->get('role',''));
        $semilleroId = $request->integer('semillero_id');

        if ($roleFilter === 'LIDER_GENERAL') {
            $roleFilter = 'ADMIN';
        }

        // FK usada en aprendices (id_usuario o user_id)
        $aprFk      = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
        // Por si en algún entorno aún no existe la tabla lideres_investigacion
        $hasLiTable = Schema::hasTable('lideres_investigacion');

        $usuarios = User::query()
            // Líder de semillero
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 'users.id')
            ->leftJoin('semilleros as sl', 'sl.id_lider_semi', '=', 'ls.id_lider_semi')

            // Aprendiz y su semillero
            ->leftJoin('aprendices as ap', "ap.$aprFk", '=', 'users.id')
            ->leftJoin('semilleros as sa', 'sa.id_semillero', '=', 'ap.semillero_id')

            // Líder de investigación (solo si existe la tabla)
            ->when($hasLiTable, function ($q) {
                $q->leftJoin('lideres_investigacion as li', 'li.user_id', '=', 'users.id');
            })

            ->select([
                'users.*',
                DB::raw('COALESCE(sa.nombre, sl.nombre) as semillero_nombre'),
                DB::raw('COALESCE(sa.id_semillero, sl.id_semillero) as semillero_id'),
                DB::raw('COALESCE(sa.linea_investigacion, sl.linea_investigacion) as linea_investigacion'),
                DB::raw("
                    CASE
                        WHEN users.role = 'ADMIN'               THEN 'Líder general'
                        WHEN users.role = 'LIDER_SEMILLERO'     THEN 'Líder semillero'
                        WHEN users.role = 'LIDER_INVESTIGACION' THEN 'Líder de investigación'
                        WHEN users.role = 'APRENDIZ'            THEN 'Aprendiz'
                        ELSE users.role
                    END AS role_label
                "),
                DB::raw(($hasLiTable ? 'li.tiene_permisos' : 'NULL') . ' as li_tiene_permisos'),
            ])

            // Filtro búsqueda
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('users.name','like',"%{$q}%")
                      ->orWhere('users.apellidos','like',"%{$q}%")
                      ->orWhere('users.email','like',"%{$q}%");
                });
            })

            // Filtro rol
            ->when($roleFilter !== '', fn($w) => $w->where('users.role', $roleFilter))

            // Filtro semillero
            ->when($semilleroId, function ($w) use ($semilleroId) {
                $w->where(function ($x) use ($semilleroId) {
                    $x->where('sa.id_semillero', $semilleroId)
                      ->orWhere('sl.id_semillero', $semilleroId);
                });
            })

            ->distinct('users.id')
            ->orderByDesc('users.created_at')
            ->paginate(12)
            ->withQueryString();

        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->orderBy('nombre')
            ->get();

        $roles = [
            'ADMIN'               => 'Líder general',
            'LIDER_SEMILLERO'     => 'Líder semillero',
            'LIDER_INVESTIGACION' => 'Líder de investigación',
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
    // DAR / QUITAR PERMISOS A LÍDER INVESTIGACIÓN
    // ============================================================
    public function togglePermisosInvestigacion(User $usuario)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        if ($usuario->role !== 'LIDER_INVESTIGACION') {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Solo se pueden gestionar permisos de líderes de investigación.');
        }

        $registro = DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->first();

        if (!$registro) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Este usuario no tiene perfil de líder de investigación.');
        }

        $nuevoEstado = $registro->tiene_permisos ? 0 : 1;

        DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->update([
                'tiene_permisos' => $nuevoEstado,
                'updated_at'     => now(),
            ]);

        $mensaje = $nuevoEstado
            ? 'Se han otorgado permisos de investigación a este usuario.'
            : 'Se han quitado los permisos de investigación a este usuario.';

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', $mensaje);
    }

    // ============================================================
    // CREAR USUARIO (NORMALIZADO)
    // ============================================================
    public function store(Request $request)
    {
        // Mapear LIDER_GENERAL -> ADMIN para compatibilidad
        $roleMap = [
            'ADMIN'               => 'ADMIN',
            'LIDER_GENERAL'       => 'ADMIN',
            'LIDER_SEMILLERO'     => 'LIDER_SEMILLERO',
            'LIDER_INVESTIGACION' => 'LIDER_INVESTIGACION',
            'APRENDIZ'            => 'APRENDIZ',
        ];

        // VALIDACIÓN PRINCIPAL
        $data = $request->validate([
            // ------- USERS -------
            'role'            => ['required', 'in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,LIDER_INVESTIGACION,APRENDIZ'],
            'email'           => ['required', 'email', 'max:160', 'unique:users,email'],
            'nombre'          => ['required', 'string', 'max:120'],
            'apellido'        => ['required', 'string', 'max:255'],
            'password'        => ['required', 'string', 'min:6'],

            'tipo_documento'  => ['required', 'string', 'max:20'],
            'documento'       => ['required', 'string', 'max:40', 'unique:users,documento'],
            'celular'         => ['nullable', 'string', 'max:30'],
            'genero'          => ['nullable', 'in:HOMBRE,MUJER,NO DEFINIDO'],
            'tipo_rh'         => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

            // ------- LÍDER SEMILLERO -------
            'ls_correo_institucional' => ['nullable', 'email', 'max:160'],
            'ls_semillero_id'         => ['required_if:role,LIDER_SEMILLERO', 'nullable', 'exists:semilleros,id_semillero'],

            // ------- APRENDIZ -------
            'semillero_id'        => ['required_if:role,APRENDIZ', 'nullable', 'exists:semilleros,id_semillero'],
            'correo_institucional'=> ['nullable', 'email', 'max:160'],
            'vinculado_sena'      => ['nullable', 'in:0,1'],
            'ficha'               => ['nullable', 'string', 'max:30'],
            'programa'            => ['nullable', 'string', 'max:160'],
            'institucion'         => ['nullable', 'string', 'max:160'],
            'nivel_educativo'     => ['required_if:role,APRENDIZ', 'nullable',
                'in:ARTICULACION_MEDIA_10_11,TECNOACADEMIA_7_9,TECNICO,TECNOLOGO,PROFESIONAL',
            ],
        ], [
            'role.required'        => 'Debes seleccionar un rol.',
            'role.in'              => 'Rol no permitido.',
            'email.unique'         => 'Este correo ya está registrado.',
            'documento.unique'     => 'Este documento ya está registrado.',

            'ls_semillero_id.required_if' => 'Selecciona el semillero del líder.',
            'ls_semillero_id.exists'      => 'El semillero seleccionado no existe.',

            'semillero_id.required_if'    => 'Selecciona el semillero del aprendiz.',
            'semillero_id.exists'         => 'El semillero seleccionado no existe.',

            'nivel_educativo.required_if' => 'Selecciona el nivel educativo del aprendiz.',
        ]);

        // Rol canonical (ADMIN, LIDER_SEMILLERO, etc.)
        $role = $roleMap[$data['role']];

        // Normalizar vinculado_sena
        $vinculadoSena = (int) ($data['vinculado_sena'] ?? 1);

        // Si el género no es OTRO, limpiamos genero_otro
      

        DB::beginTransaction();

        try {
            // ==========================
            // 1) CREAR USER (tabla users)
            // ==========================
            $user = User::create([
                'name'           => $data['nombre'],
                'apellidos'      => $data['apellido'],
                'email'          => $data['email'],
                'password'       => Hash::make($data['password']),
                'role'           => $role,

                'tipo_documento' => $data['tipo_documento'],
                'documento'      => $data['documento'],
                'celular'        => $data['celular'] ?? null,
                'genero'         => $data['genero'] ?? null,
                'tipo_rh'        => $data['tipo_rh'] ?? null,
            ]);

            // ==========================
            // 2) CREAR PERFIL SEGÚN ROL
            // ==========================
            switch ($role) {
                // --------------------
                // ADMIN (Líder general)
                // --------------------
                case 'ADMIN':
                    Administrador::create([
                        'id_usuario'          => $user->id,
                        'nombre'              => $data['nombre'] . ' ' . $data['apellido'], // si usas este campo
                        'nombres'             => $data['nombre'],
                        'apellidos'           => $data['apellido'],
                     
                    ]);
                    break;

                // --------------------
                // LÍDER SEMILLERO
                // --------------------
                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->insert([
                        'id_lider_semi'        => $user->id, // FK = users.id
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],
                        'correo_institucional' => $data['ls_correo_institucional'] ?? $data['email'],
                        'id_semillero'         => $data['ls_semillero_id'],
                        'creado_en'            => now(),
                        'actualizado_en'       => now(),
                    ]);
                    break;

                // --------------------
                // LÍDER INVESTIGACIÓN
                // --------------------
                case 'LIDER_INVESTIGACION':
                    DB::table('lideres_investigacion')->insert([
                        'user_id'       => $user->id,
                        'tiene_permisos'=> 0,        // inicia sin permisos
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                    break;

                // --------------------
                // APRENDIZ
                // --------------------
                case 'APRENDIZ':
                    Aprendiz::create([
                        'user_id'             => $user->id,
                        'nombres'             => $data['nombre'],
                        'apellidos'           => $data['apellido'],
                        'ficha'               => $data['ficha'] ?? null,
                        'programa'            => $data['programa'] ?? null,
                        'nivel_educativo'     => $data['nivel_educativo'] ?? null,
                        'vinculado_sena'      => $vinculadoSena,
                        'institucion'         => $vinculadoSena === 1 ? null : ($data['institucion'] ?? null),
                        'correo_institucional'=> $data['correo_institucional'] ?? null,
                        'correo_personal'     => $data['email'],
                        'contacto_nombre'     => null,
                        'contacto_celular'    => null,
                        'semillero_id'        => $data['semillero_id'],
                        'estado'              => 'Activo',
                    ]);
                    break;
            }

            DB::commit();

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success','Se ha creado el usuario correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('admin.usuarios.index')
                ->withInput()
                ->with('error', 'Ocurrió un error al crear el usuario: '.$e->getMessage());
        }
    }

    // ============================================================
    // EDITAR (resource clásico, no se usa)
    // ============================================================
    public function edit(User $usuario)
    {
        return abort(404);
    }

    // ============================================================
    // EDITAR AJAX (si lo necesitas en otro lado)
    // ============================================================
    public function editAjax(User $usuario)
    {
        $perfil = null;

        switch ($usuario->role) {
            case 'ADMIN':
                $perfil = DB::table('administradores')
                    ->where('id_usuario', $usuario->id)
                    ->first();
                break;

            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero')
                    ->where('id_lider_semi', $usuario->id)
                    ->first();
                break;

            case 'APRENDIZ':
                $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
                    ? 'id_usuario'
                    : 'user_id';

                $perfil = DB::table('aprendices')
                    ->where($colUserFk, $usuario->id)
                    ->first();
                break;
        }

        return response()->json([
            'usuario' => $usuario,
            'perfil'  => $perfil,
        ]);
    }

    // ============================================================
    // ACTUALIZAR
    // ============================================================
    public function update(Request $request, User $usuario)
    {
        // El modal editar envía: name, apellidos, email, password (opcional)
        $rules = [
            'name'      => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => [
                'required',
                'email',
                'max:255',
                Rule::unique('users','email')->ignore($usuario->id),
            ],
            'password'  => 'nullable|min:6',
        ];

        $data = $request->validate($rules);

        DB::transaction(function () use ($usuario, $data) {

            $updateUser = [
                'name'       => $data['name'],
                'apellidos'  => $data['apellidos'],
                'email'      => $data['email'],
                'updated_at' => now(),
            ];

            if (!empty($data['password'])) {
                $updateUser['password'] = Hash::make($data['password']);
            }

            $usuario->update($updateUser);

            // Actualizar tablas relacionadas según rol
            switch ($usuario->role) {
                case 'ADMIN':
                    $adminUpdate = [
                        'apellidos'      => $data['apellidos'],
                        'actualizado_en' => now(),
                    ];
                    if (Schema::hasColumn('administradores','nombre')) {
                        $adminUpdate['nombre'] = $data['name'];
                    }
                    if (Schema::hasColumn('administradores','nombres')) {
                        $adminUpdate['nombres'] = $data['name'];
                    }

                    DB::table('administradores')
                        ->where('id_usuario', $usuario->id)
                        ->update($adminUpdate);
                    break;

                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')
                        ->where('id_lider_semi', $usuario->id)
                        ->update([
                            'nombres'              => $data['name'],
                            'apellidos'            => $data['apellidos'],
                            'correo_institucional' => $data['email'],
                            'actualizado_en'       => now(),
                        ]);
                    break;

                case 'APRENDIZ':
                    $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
                        ? 'id_usuario'
                        : 'user_id';

                    DB::table('aprendices')
                        ->where($colUserFk, $usuario->id)
                        ->update([
                            'nombres'         => $data['name'],
                            'apellidos'       => $data['apellidos'],
                            'correo_personal' => $data['email'],
                            'actualizado_en'  => now(),
                        ]);
                    break;
            }
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success','Se ha actualizado el usuario correctamente.');
    }

    // ============================================================
    // ELIMINAR
    // ============================================================
    public function destroy(User $usuario)
    {
        // 1) Si es aprendiz, comprobar proyectos antes de borrar
        $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
            ? 'id_usuario'
            : 'user_id';

        $aprendiz = DB::table('aprendices')
            ->where($colUserFk, $usuario->id)
            ->first();

        if ($aprendiz) {
            $tieneProyectos = DB::table('aprendiz_proyecto')
                ->where('id_aprendiz', $aprendiz->id_aprendiz)
                ->exists();

            if ($tieneProyectos) {
                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('error', 'No se puede eliminar el aprendiz porque tiene proyectos asociados.');
            }
        }

        // 2) Eliminar en cascada manual según rol
        DB::transaction(function () use ($usuario, $aprendiz) {

            if ($aprendiz) {
                DB::table('aprendices')
                    ->where('id_aprendiz', $aprendiz->id_aprendiz)
                    ->delete();
            }

            if ($usuario->role === 'ADMIN') {
                DB::table('administradores')
                    ->where('id_usuario', $usuario->id)
                    ->delete();
            }

            if ($usuario->role === 'LIDER_SEMILLERO') {
                DB::table('lideres_semillero')
                    ->where('id_lider_semi', $usuario->id)
                    ->delete();
            }

            if ($usuario->role === 'LIDER_INVESTIGACION') {
                DB::table('lideres_investigacion')
                    ->where('user_id', $usuario->id)
                    ->delete();
            }

            // Finalmente, borrar el usuario
            $usuario->delete();
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Se ha eliminado el usuario correctamente.');
    }
}
