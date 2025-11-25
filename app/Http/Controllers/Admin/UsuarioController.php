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
        if (!auth()->user()->role === 'ADMIN') {
            abort(403);
        }

        $registro = DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->first();

        if (!$registro) {
            return back()->withErrors(['msg' =>
                'Este usuario no tiene perfil de líder de investigación'
            ]);
        }

        DB::table('lideres_investigacion')
            ->where('user_id', $usuario->id)
            ->update([
                'tiene_permisos' => $registro->tiene_permisos ? 0 : 1,
            ]);

        return back()->with('success', 'Permisos actualizados correctamente.');
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

        // VALIDACIONES BÁSICAS
        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,LIDER_INVESTIGACION,APRENDIZ',
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

        $data = $request->validate($rules);
        $role = $roleMap[$data['role']];

        // VALIDACIÓN EXTRA PARA APRENDIZ
        if ($role === 'APRENDIZ') {
            $request->validate([
                'tipo_documento'  => 'required',
                'documento'       => 'required|max:40',
                'semillero_id'    => 'required|integer',
            ]);
        }

        // TRANSACCIÓN
        DB::transaction(function () use ($data, $role, $request) {

            $userId = DB::table('users')->insertGetId([
                'name'       => $data['nombre'],
                'apellidos'  => $data['apellido'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
                'role'       => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            switch ($role) {

                // ADMIN
                case 'ADMIN':
                    DB::table('administradores')->insert([
                        'id_usuario'     => $userId,
                        'nombres'        => $data['nombre'],
                        'apellidos'      => $data['apellido'],
                        'creado_en'      => now(),
                        'actualizado_en' => now(),
                    ]);
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
                    $colUserFk = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';

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

        return back()->with('success','Usuario creado correctamente.');
    }

    // ============================================================
    // EDITAR
    // ============================================================
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $perfil  = null;

        switch ($usuario->role) {
            case 'ADMIN':
                $perfil = DB::table('administradores')->where('id_usuario', $id)->first();
                break;
            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero')->where('id_lider_semi', $id)->first();
                break;
            case 'APRENDIZ':
                $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
                    ? 'id_usuario'
                    : 'user_id';
                $perfil = DB::table('aprendices')->where($colUserFk, $id)->first();
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
        $rules = [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($usuario->id)],
        ];

        $data = $request->validate($rules);

        DB::transaction(function () use ($usuario, $data) {

            $usuario->update([
                'name'      => $data['nombre'],
                'apellidos' => $data['apellido'],
                'email'     => $data['email'],
            ]);

            switch ($usuario->role) {
                case 'ADMIN':
                    DB::table('administradores')
                        ->where('id_usuario', $usuario->id)
                        ->update([
                            'nombres'        => $data['nombre'],
                            'apellidos'      => $data['apellido'],
                            'actualizado_en' => now(),
                        ]);
                    break;

                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')
                        ->where('id_lider_semi', $usuario->id)
                        ->update([
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
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
                            'nombres'         => $data['nombre'],
                            'apellidos'       => $data['apellido'],
                            'correo_personal' => $data['email'],
                            'actualizado_en'  => now(),
                        ]);
                    break;
            }
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success','Usuario actualizado correctamente.');
    }

    // ============================================================
    // ELIMINAR
    // ============================================================
    public function destroy(User $usuario)
    {
        DB::transaction(function () use ($usuario) {

            switch ($usuario->role) {
                case 'ADMIN':
                    DB::table('administradores')->where('id_usuario', $usuario->id)->delete();
                    break;
                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->where('id_lider_semi', $usuario->id)->delete();
                    break;
                case 'LIDER_INVESTIGACION':
                    DB::table('lideres_investigacion')->where('user_id', $usuario->id)->delete();
                    break;
                case 'APRENDIZ':
                    $colUserFk = Schema::hasColumn('aprendices', 'id_usuario')
                        ? 'id_usuario'
                        : 'user_id';
                    DB::table('aprendices')->where($colUserFk, $usuario->id)->delete();
                    break;
            }

            $usuario->delete();
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success','Usuario eliminado correctamente.');
    }
}
