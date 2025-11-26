<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

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
    // CREAR USUARIO
    // ============================================================
    public function store(Request $request)
    {
        $roleMap = [
            'ADMIN'               => 'ADMIN',
            'LIDER_GENERAL'       => 'ADMIN',
            'LIDER_SEMILLERO'     => 'LIDER_SEMILLERO',
            'LIDER_INVESTIGACION' => 'LIDER_INVESTIGACION',
            'APRENDIZ'            => 'APRENDIZ',
        ];

        // VALIDACIONES BÁSICAS (coinciden con modal crear)
        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,LIDER_INVESTIGACION,APRENDIZ',
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

        $data = $request->validate($rules);
        $role = $roleMap[$data['role']];

        // Validaciones adicionales según rol
        if ($role === 'LIDER_SEMILLERO') {
            $request->validate([
                'ls_tipo_documento' => 'required|string|max:50',
                'ls_documento'      => 'required|string|max:40',
            ]);
        }

        if ($role === 'APRENDIZ') {
            $request->validate([
                'tipo_documento' => 'required|string|max:50',
                'documento'      => 'required|string|max:40',
                'semillero_id'   => 'required|integer',
            ]);
        }

        DB::transaction(function () use ($data, $role, $request) {

            // Crear usuario base
            $userId = DB::table('users')->insertGetId([
                'name'       => $data['nombre'],
                'apellidos'  => $data['apellido'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
                'role'       => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Perfiles según rol
            switch ($role) {

                // ADMIN
                case 'ADMIN':
                    $adminData = [
                        'id_usuario'     => $userId,
                        'apellidos'      => $data['apellido'],
                        'creado_en'      => now(),
                        'actualizado_en' => now(),
                    ];

                    // Cubrimos tanto 'nombre' como 'nombres'
                    if (Schema::hasColumn('administradores', 'nombre')) {
                        $adminData['nombre'] = $data['nombre'];
                    }
                    if (Schema::hasColumn('administradores', 'nombres')) {
                        $adminData['nombres'] = $data['nombre'];
                    }

                    DB::table('administradores')->insert($adminData);
                    break;

                // LÍDER SEMILLERO
                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->insert([
                        'id_lider_semi'        => $userId,
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],
                        'tipo_documento'       => $request->ls_tipo_documento,
                        'documento'            => $request->ls_documento,
                        'correo_institucional' => $data['email'],
                        'creado_en'            => now(),
                        'actualizado_en'       => now(),
                    ]);
                    break;

                // LÍDER INVESTIGACIÓN
                case 'LIDER_INVESTIGACION':
                    DB::table('lideres_investigacion')->insert([
                        'user_id'        => $userId,
                        'tiene_permisos' => 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                    break;

                // APRENDIZ
                case 'APRENDIZ':
                    $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
                        ? 'id_usuario'
                        : 'user_id';

                    DB::table('aprendices')->insert([
                        $colUserFk             => $userId,
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],
                        'correo_personal'      => $data['email'],
                        'tipo_documento'       => $request->tipo_documento,
                        'documento'            => $request->documento,
                        'celular'              => $request->celular,
                        'correo_institucional' => $request->correo_institucional,
                        'ficha'                => $request->ficha,
                        'programa'             => $request->programa,
                        'vinculado_sena'       => $request->vinculado_sena ?? 1,
                        'institucion'          => $request->institucion,
                        'semillero_id'         => $request->semillero_id,
                        'creado_en'            => now(),
                        'actualizado_en'       => now(),
                    ]);
                    break;
            }
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success','Se ha creado el usuario correctamente.');
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
