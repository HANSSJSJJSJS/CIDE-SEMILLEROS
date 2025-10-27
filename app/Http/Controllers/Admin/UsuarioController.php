<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;

class UsuarioController extends Controller
{
    /**
     * Listado con filtros: q (nombre/apellidos/email), role, semillero_id
     */
public function index(Request $request)
{
    $q           = trim($request->get('q',''));          // buscar por nombre/apellidos/email
    $roleFilter  = trim($request->get('role',''));       // filtro por rol (en query puede venir LIDER_GENERAL)
    $semilleroId = $request->integer('semillero_id');    // filtro por semillero

    // Normaliza el valor del select a como se guarda en BD
    if ($roleFilter === 'LIDER_GENERAL') {
        $roleFilter = 'LIDER GENERAL';
    }

    $usuarios = \App\Models\User::query()
        // Líder de semillero: users -> lideres_semillero (por id) -> semilleros (por id_lider_semi)
        ->leftJoin('lideres_semillero as ls', 'ls.id_lider_semi', '=', 'users.id')
        ->leftJoin('semilleros as s', 's.id_lider_semi', '=', 'ls.id_lider_semi')

        // Nota: no hay relación de semillero para LIDER GENERAL en tu esquema actual
        ->select([
            'users.*',
            DB::raw('s.nombre as semillero_nombre'),
            DB::raw('s.id_semillero as semillero_id'),
        ])

        // Buscar por nombre/apellidos/email
        ->when($q !== '', function ($w) use ($q) {
            $w->where(function ($s) use ($q) {
                $s->where('users.name','like',"%{$q}%")
                  ->orWhere('users.apellidos','like',"%{$q}%")
                  ->orWhere('users.email','like',"%{$q}%");
            });
        })

        // Filtro por rol
        ->when($roleFilter !== '', fn($w) => $w->where('users.role',$roleFilter))

        // Filtro por semillero (solo aplica a LIDER_SEMILLERO; otros roles quedarán fuera)
        ->when($semilleroId, fn($w) => $w->where('s.id_semillero', $semilleroId))

        ->orderByDesc('users.created_at')
        ->paginate(12)
        ->withQueryString();

    // Para el combo de semilleros
    $semilleros = DB::table('semilleros')
        ->select('id_semillero','nombre')
        ->orderBy('nombre')
        ->get();

    // Opciones del select de rol en la vista (clave = lo que se manda por query)
    $roles = [
        'ADMIN'           => 'ADMIN',
        'LIDER_GENERAL'   => 'LIDER GENERAL',   // en BD es con espacio; ya lo normalizamos arriba
        'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
        'APRENDIZ'        => 'APRENDIZ',
    ];

    return view('Admin.usuarios.index', [
        'usuarios'     => $usuarios,
        'semilleros'   => $semilleros,
        'roles'        => $roles,
        'q'            => $q,
        'roleFilter'   => $request->get('role',''),  // conserva el valor del select (con guion bajo)
        'semilleroId'  => $semilleroId,
    ]);
}
    /**
     * Crear usuario (desde el modal).
     */
    public function store(Request $request)
    {
        // Mapa de rol del form -> valor guardado en BD
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

        // Campos extra por rol
        if ($request->role === 'LIDER_SEMILLERO') {
            $rules = array_merge($rules, [
                'ls_tipo_documento' => 'required|string|max:5',
                'ls_documento'      => 'required|string|max:40',
            ]);
        }
        if ($request->role === 'APRENDIZ') {
            $rules = array_merge($rules, [
                'ap_ficha'                => 'required|string|max:30',
                'ap_programa'             => 'required|string|max:160',
                'ap_tipo_documento'       => 'nullable|string|max:5',
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

                // 1) users
                $userId = DB::table('users')->insertGetId([
                    'name'       => $data['nombre'],
                    'apellidos'  => $data['apellido'],
                    'email'      => $data['email'],
                    'password'   => Hash::make($data['password']),
                    'role'       => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2) perfiles por rol
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
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario')
                            ? 'id_usuario' : 'user_id';

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

            return back()->with('success', 'Usuario creado correctamente.');

        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])
                ->withInput();
        }
    }

    /**
     * Carga datos para editar (AJAX).
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);

        $perfil = null;
        switch ($usuario->role) {
            case 'ADMIN':
                $perfil = DB::table('administradores')->where('id_usuario', $id)->first();
                break;
            case 'LIDER GENERAL':
                $perfil = DB::table('lider_general')->where('id_lidergen', $id)->first();
                break;
            case 'LIDER_SEMILLERO':
                $perfil = DB::table('lideres_semillero')->where('id_lider_semi', $id)->first();
                break;
            case 'APRENDIZ':
                $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                $perfil = DB::table('aprendices')->where($colUserFk, $id)->first();
                break;
        }

        return response()->json(['usuario' => $usuario, 'perfil' => $perfil]);
    }

    /**
     * Actualiza datos del usuario y su perfil.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($id)],
        ];
        $data = $request->validate($rules);

        DB::transaction(function () use ($user, $data, $id) {
            // User
            $user->update([
                'name'       => $data['nombre'],
                'apellidos'  => $data['apellido'],
                'email'      => $data['email'],
                'updated_at' => now(),
            ]);

            // Perfil
            switch ($user->role) {
                case 'ADMIN':
                    DB::table('administradores')->where('id_usuario', $id)->update([
                        'nombres'        => $data['nombre'],
                        'apellidos'      => $data['apellido'],
                        'actualizado_en' => now(),
                    ]);
                    break;

                case 'LIDER GENERAL':
                    DB::table('lider_general')->where('id_lidergen', $id)->update([
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],
                        'Correo_institucional' => $data['email'],
                        'actualizado_en'       => now(),
                    ]);
                    break;

                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->where('id_lider_semi', $id)->update([
                        'nombres'              => $data['nombre'],
                        'apellidos'            => $data['apellido'],
                        'correo_institucional' => $data['email'],
                        'actualizado_en'       => now(),
                    ]);
                    break;

                case 'APRENDIZ':
                    $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                    DB::table('aprendices')->where($colUserFk, $id)->update([
                        'nombres'         => $data['nombre'],
                        'apellidos'       => $data['apellido'],
                        'correo_personal' => $data['email'],
                        'actualizado_en'  => now(),
                    ]);
                    break;
            }
        });

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina usuario + perfil (si no tienes FK ON DELETE CASCADE).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);

            switch ($user->role) {
                case 'ADMIN':
                    DB::table('administradores')->where('id_usuario', $id)->delete();
                    break;
                case 'LIDER GENERAL':
                    DB::table('lider_general')->where('id_lidergen', $id)->delete();
                    break;
                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->where('id_lider_semi', $id)->delete();
                    break;
                case 'APRENDIZ':
                    $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                    DB::table('aprendices')->where($colUserFk, $id)->delete();
                    break;
            }

            $user->delete();
        });

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
