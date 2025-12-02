<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Models\User;
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

        $aprFk      = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
        $hasLiTable = Schema::hasTable('lideres_investigacion');

        $usuarios = User::query()
            ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 'users.id')
            ->leftJoin('semilleros as sl', 'sl.id_lider_semi', '=', 'ls.id_lider_semi')

            ->leftJoin('aprendices as ap', "ap.$aprFk", '=', 'users.id')
            ->leftJoin('semilleros as sa', 'sa.id_semillero', '=', 'ap.semillero_id')

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

            // 🔧 AQUÍ estaba el problema: users.name -> users.nombre
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('users.nombre','like',"%{$q}%")
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
    // DAR / QUITAR PERMISOS LÍDER INVESTIGACIÓN
    // ============================================================
    public function togglePermisosInvestigacion(User $usuario)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        if ($usuario->role !== 'LIDER_INVESTIGACION') {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Solo se pueden gestionar permisos de líderes de investigación.');
        }

        $registro = DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->first();

        if (!$registro) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Este usuario no tiene perfil de líder de investigación.');
        }

        $nuevo = $registro->tiene_permisos ? 0 : 1;

        DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->update([
                'tiene_permisos' => $nuevo,
                'updated_at'     => now(),
            ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', $nuevo
                ? 'Se otorgaron permisos.'
                : 'Se retiraron los permisos.'
            );
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

                $data = $request->validate([
            'role'             => ['required','in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,LIDER_INVESTIGACION,APRENDIZ'],
            'email'            => ['required','email','max:160','unique:users,email'],
            'nombre'           => ['required','string','max:120'],
            'apellido'         => ['required','string','max:255'],
            'password'         => ['required','string','min:6'],

            'tipo_documento'   => [
                'required',
                'string',
                Rule::in(['CC','TI','CE','PASAPORTE','PERMISO ESPECIAL','REGISTRO CIVIL']),
            ],

            'documento'        => ['required','string','max:40','unique:users,documento'],
            'celular'          => ['nullable','string','max:30'],
            'genero'           => ['nullable','in:HOMBRE,MUJER,NO DEFINIDO'],
            'tipo_rh'          => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

            'ls_correo_institucional' => ['nullable','email','max:160'],
            'ls_semillero_id'         => ['nullable','exists:semilleros,id_semillero'],

            'semillero_id'            => ['required_if:role,APRENDIZ','nullable','exists:semilleros,id_semillero'],
            'correo_institucional'    => ['nullable','email','max:160'],
            'vinculado_sena'          => ['nullable','in:0,1'],
            'ficha'                   => ['nullable','string','max:30'],
            'programa'                => ['nullable','string','max:160'],
            'institucion'             => ['nullable','string','max:160'],
            'nivel_educativo'         => ['required_if:role,APRENDIZ','nullable',
                'in:ARTICULACION_MEDIA_10_11,TECNOACADEMIA_7_9,TECNICO,TECNOLOGO,PROFESIONAL'],
            'contacto_nombre'         => ['nullable','string','max:160'],
            'contacto_celular'        => ['nullable','string','max:30'],
        ]);

        $role      = $roleMap[$data['role']];
        $vinculado = (int) ($data['vinculado_sena'] ?? 1);

        DB::beginTransaction();

        try {
            // ============= USERS =============
            $user = User::create([
                'nombre'         => $data['nombre'],
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

            // ============= PERFILES =============
            switch ($role) {
                case 'ADMIN':
                    DB::table('administradores')->insert([
                        'id_usuario'    => $user->id,
                        'creado_en'     => now(),
                        'actualizado_en'=> now(),
                    ]);
                    break;

                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->insert([
                        'id_lider_semi'        => $user->id,
                        'id_usuario'           => $user->id,
                        'correo_institucional' => $data['ls_correo_institucional'] ?? $data['email'],
                        'id_semillero'         => $data['ls_semillero_id'] ?? null,
                        'creado_en'            => now(),
                        'actualizado_en'       => now(),
                    ]);
                    break;

                case 'LIDER_INVESTIGACION':
                    DB::table('lideres_investigacion')->insert([
                        'user_id'       => $user->id,
                        'tiene_permisos'=> 0,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                    break;

                case 'APRENDIZ':
                    Aprendiz::create([
                        'user_id'             => $user->id,
                        'ficha'               => $data['ficha'] ?? null,
                        'programa'            => $data['programa'] ?? null,
                        'nivel_educativo'     => $data['nivel_educativo'] ?? null,
                        'vinculado_sena'      => $vinculado,
                        'institucion'         => $vinculado === 1 ? null : ($data['institucion'] ?? null),
                        'correo_institucional'=> $data['correo_institucional'] ?? $data['email'],
                        'contacto_nombre'     => $data['contacto_nombre'] ?? null,
                        'contacto_celular'    => $data['contacto_celular'] ?? null,
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
                ->with('error',"Error al crear usuario: ".$e->getMessage());
        }
    }

    // ============================================================
    // EDITAR AJAX
    // ============================================================
    public function editAjax(User $usuario)
    {
        $perfil = null;

        switch ($usuario->role) {

            case 'ADMIN':
                $perfil = DB::table('administradores')
                    ->where('id_usuario',$usuario->id)
                    ->first();
                break;

            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero')
                    ->where('id_lider_semi',$usuario->id)
                    ->first();
                break;

            case 'APRENDIZ':
                $col = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : 'user_id';
                $perfil = DB::table('aprendices')->where($col,$usuario->id)->first();
                break;
        }

        return response()->json([
            'usuario'=>$usuario,
            'perfil'=>$perfil
        ]);
    }

    // ============================================================
    // VER DETALLE (AJAX)
    // ============================================================
    public function showAjax(User $usuario)
    {
        $perfil = null;
        $colFk = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : 'user_id';

        switch ($usuario->role) {

            case 'ADMIN':
                $perfil = DB::table('administradores')
                    ->where('id_usuario',$usuario->id)
                    ->first();
                break;

            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero as ls')
                    ->leftJoin('semilleros as s','s.id_semillero','=','ls.id_semillero')
                    ->where('ls.id_lider_semi',$usuario->id)
                    ->select('ls.*','s.nombre as semillero_nombre')
                    ->first();
                break;

            case 'LIDER_INVESTIGACION':
                $perfil = DB::table('lideres_investigacion')
                    ->where('user_id',$usuario->id)
                    ->first();
                break;

            case 'APRENDIZ':
                $perfil = DB::table('aprendices as ap')
                    ->leftJoin('semilleros as s','s.id_semillero','=','ap.semillero_id')
                    ->where("ap.$colFk",$usuario->id)
                    ->select('ap.*','s.nombre as semillero_nombre')
                    ->first();
                break;
        }

        return response()->json([
            'usuario'=>$usuario,
            'perfil'=>$perfil
        ]);
    }

    // ============================================================
    // ACTUALIZAR
    // ============================================================
public function update(Request $request, User $usuario)
{
    $data = $request->validate([
        'nombre'          => ['required','string','max:120'],
        'apellido'        => ['required','string','max:255'],
        'email'           => ['required','email','max:160', Rule::unique('users','email')->ignore($usuario->id)],
        'password'        => ['nullable','string','min:6'],

        'tipo_documento'  => [
            'required',
            'string',
            Rule::in(['CC','TI','CE','PASAPORTE','PERMISO ESPECIAL','REGISTRO CIVIL']),
        ],
        'documento'       => ['required','string','max:40', Rule::unique('users','documento')->ignore($usuario->id)],
        'celular'         => ['nullable','string','max:30'],
        'genero'          => ['nullable','in:HOMBRE,MUJER,NO DEFINIDO'],
        'tipo_rh'         => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

        // CAMPOS PERFIL LÍDER SEMILLERO
        'ls_correo_institucional' => ['nullable','email','max:160'],
        'ls_semillero_id'         => ['nullable','exists:semilleros,id_semillero'],

        // CAMPOS PERFIL APRENDIZ
        'semillero_id'            => ['nullable','exists:semilleros,id_semillero'],
        'correo_institucional'    => ['nullable','email','max:160'],
        'vinculado_sena'          => ['nullable','in:0,1'],
        'ficha'                   => ['nullable','string','max:30'],
        'programa'                => ['nullable','string','max:160'],
        'institucion'             => ['nullable','string','max:160'],
        'nivel_educativo'         => ['nullable',
            'in:ARTICULACION_MEDIA_10_11,TECNOACADEMIA_7_9,TECNICO,TECNOLOGO,PROFESIONAL'],
        'contacto_nombre'         => ['nullable','string','max:160'],
        'contacto_celular'        => ['nullable','string','max:30'],
    ]);

    $vinculado = (int) ($data['vinculado_sena'] ?? 1);

    DB::transaction(function () use ($usuario, $data, $vinculado) {

        // ========= USERS =========
        $updateUser = [
            'nombre'         => $data['nombre'],
            'apellidos'      => $data['apellido'],
            'email'          => $data['email'],
            'tipo_documento' => $data['tipo_documento'],
            'documento'      => $data['documento'],
            'celular'        => $data['celular'] ?? null,
            'genero'         => $data['genero'] ?? null,
            'tipo_rh'        => $data['tipo_rh'] ?? null,
            'updated_at'     => now(),
        ];

        if (!empty($data['password'])) {
            $updateUser['password'] = Hash::make($data['password']);
        }

        $usuario->update($updateUser);

        // ========= PERFILES =========
        switch ($usuario->role) {

            case 'ADMIN':
                DB::table('administradores')
                    ->where('id_usuario', $usuario->id)
                    ->update([
                        'actualizado_en' => now(),
                    ]);
                break;

            case 'LIDER_SEMILLERO':
                DB::table('lideres_semillero')
                    ->where('id_lider_semi', $usuario->id)
                    ->update([
                        'correo_institucional' => $data['ls_correo_institucional'] ?? $data['email'],
                        'id_semillero'         => $data['ls_semillero_id'] ?? null,
                        'actualizado_en'       => now(),
                    ]);
                break;

            case 'APRENDIZ':
                $col = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : 'user_id';
                DB::table('aprendices')
                    ->where($col, $usuario->id)
                    ->update([
                        'ficha'                => $data['ficha'] ?? null,
                        'programa'             => $data['programa'] ?? null,
                        'nivel_educativo'      => $data['nivel_educativo'] ?? null,
                        'vinculado_sena'       => $vinculado,
                        'institucion'          => $vinculado === 1 ? null : ($data['institucion'] ?? null),
                        'correo_institucional' => $data['correo_institucional'] ?? $data['email'],
                        'contacto_nombre'      => $data['contacto_nombre'] ?? null,
                        'contacto_celular'     => $data['contacto_celular'] ?? null,
                        'semillero_id'         => $data['semillero_id'] ?? null,
                        'actualizado_en'       => now(),
                    ]);
                break;

            case 'LIDER_INVESTIGACION':
                // De momento solo sincronizamos correo en su tabla si quieres:
                DB::table('lideres_investigacion')
                    ->where('user_id', $usuario->id)
                    ->update([
                        'updated_at' => now(),
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
        $col = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : 'user_id';

        $aprendiz = DB::table('aprendices')
            ->where($col,$usuario->id)
            ->first();

        if ($aprendiz) {
            $tieneProyectos = DB::table('aprendiz_proyecto')
                ->where('id_aprendiz',$aprendiz->id_aprendiz)
                ->exists();

            if ($tieneProyectos) {
                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('error','No se puede eliminar el aprendiz porque tiene proyectos asociados.');
            }
        }

        DB::transaction(function () use ($usuario,$aprendiz,$col) {

            if ($aprendiz) {
                DB::table('aprendices')
                    ->where('id_aprendiz',$aprendiz->id_aprendiz)
                    ->delete();
            }

            if ($usuario->role === 'ADMIN') {
                DB::table('administradores')
                    ->where('id_usuario',$usuario->id)
                    ->delete();
            }

            if ($usuario->role === 'LIDER_SEMILLERO') {
                DB::table('lideres_semillero')
                    ->where('id_lider_semi',$usuario->id)
                    ->delete();
            }

            if ($usuario->role === 'LIDER_INVESTIGACION') {
                DB::table('lideres_investigacion')
                    ->where('user_id',$usuario->id)
                    ->delete();
            }

            $usuario->delete();
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success','Se ha eliminado el usuario correctamente.');
    }
}
