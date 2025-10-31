<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;


// Modelos (si no usas Eloquent para perfiles, puedes borrar estos use)
use App\Models\User;

class UsuarioController extends Controller
{
    /**
     * Listado de usuarios (para mostrar en el dashboard).
     */
public function index(Request $request)
{
    $usuarios = User::query()
        ->orderByDesc('created_at')
        ->paginate(12)              // ← Esto agrega paginación real
        ->withQueryString();        // ← Conserva filtros si los tienes

    return view('Admin.usuarios.index', compact('usuarios'));
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

        // Líder Semillero
        if ($request->role === 'LIDER_SEMILLERO') {
            $rules = array_merge($rules, [
                'ls_tipo_documento' => 'required|string|max:5',
                'ls_documento'      => 'required|string|max:40',
            ]);
        }

        // Aprendiz
        if ($request->role === 'APRENDIZ') {
            $rules = array_merge($rules, [
                'ap_ficha'                => 'required|string|max:30',
                'ap_programa'             => 'required|string|max:160',
                'ap_tipo_documento'       => 'nullable|string|max:5', // opcional
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

                // 1) Crear usuario base
                $userId = DB::table('users')->insertGetId([
                    'name'       => $data['nombre'],
                    'apellidos'  => $data['apellido'],
                    'email'      => $data['email'],
                    'password'   => Hash::make($data['password']),
                    'role'       => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2) Crear perfil según rol (coincidiendo con tu esquema)
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
                        // OJO: en tu tabla vimos que la FK puede llamarse id_usuario o user_id.
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';

                        DB::table('aprendices')->insert([
                            $colUserFk            => $userId,
                            'nombres'              => $data['nombre'],
                            'apellidos'            => $data['apellido'],
                            'ficha'                => $data['ap_ficha'],
                            'programa'             => $data['ap_programa'],
                            'tipo_documento'       => $data['ap_tipo_documento'], // puede ir null
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
            'role'     => 'nullable|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
            'estado'   => 'nullable|in:Activo,Inactivo'
        ];
        $data = $request->validate($rules);

        DB::transaction(function () use ($user, $data, $id) {
            // Mapear rol (posible cambio)
            $roleMap = [
                'ADMIN' => 'ADMIN',
                'LIDER_GENERAL' => 'LIDER GENERAL',
                'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
                'APRENDIZ' => 'APRENDIZ',
            ];

            $finalRole = !empty($data['role']) ? ($roleMap[$data['role']] ?? $user->role) : $user->role;

            // Users
            $payload = [
                'name'       => trim(($data['nombre'] ?? '').' '.($data['apellido'] ?? '')),
                'email'      => $data['email'] ?? $user->email,
                'updated_at' => now(),
                'role'       => $finalRole,
            ];
            $user->update($payload);

            // ADMINISTRADORES
            if ($finalRole === 'ADMIN') {
                $adminIdCol = Schema::hasColumn('administradores','id_usuario') ? 'id_usuario' : 'user_id';
                $nCol = Schema::hasColumn('administradores','nombres') ? 'nombres' : (Schema::hasColumn('administradores','nombre') ? 'nombre' : null);
                $upd = [ 'apellidos' => $data['apellido'] ?? null, 'actualizado_en' => now() ];
                if ($nCol) $upd[$nCol] = $data['nombre'] ?? null;
                DB::table('administradores')->where($adminIdCol, $id)->update($upd);
            }

            // LIDER GENERAL
            if ($finalRole === 'LIDER GENERAL') {
                $lgIdCol = Schema::hasColumn('lider_general','id_usuario') ? 'id_usuario' : (Schema::hasColumn('lider_general','id_lidergen') ? 'id_lidergen' : 'id_usuario');
                $nCol = Schema::hasColumn('lider_general','nombres') ? 'nombres' : (Schema::hasColumn('lider_general','nombre') ? 'nombre' : null);
                $emailCol = Schema::hasColumn('lider_general','Correo_institucional') ? 'Correo_institucional' : (Schema::hasColumn('lider_general','correo_institucional') ? 'correo_institucional' : null);
                $upd = [ 'apellidos' => $data['apellido'] ?? null, 'actualizado_en' => now() ];
                if ($nCol) $upd[$nCol] = $data['nombre'] ?? null;
                if ($emailCol) $upd[$emailCol] = $data['email'] ?? null;
                DB::table('lider_general')->where($lgIdCol, $id)->update($upd);
            }

            // LIDER SEMILLERO
            if ($finalRole === 'LIDER_SEMILLERO') {
                $lsIdCol = Schema::hasColumn('lideres_semillero','id_usuario') ? 'id_usuario' : (Schema::hasColumn('lideres_semillero','id_lider_semi') ? 'id_lider_semi' : 'id_usuario');
                $nCol = Schema::hasColumn('lideres_semillero','nombres') ? 'nombres' : (Schema::hasColumn('lideres_semillero','nombre') ? 'nombre' : null);
                $upd = [ 'apellidos' => $data['apellido'] ?? null, 'actualizado_en' => now() ];
                if ($nCol) $upd[$nCol] = $data['nombre'] ?? null;
                if (Schema::hasColumn('lideres_semillero','correo_institucional')) $upd['correo_institucional'] = $data['email'] ?? null;
                DB::table('lideres_semillero')->where($lsIdCol, $id)->update($upd);
            }

            // APRENDIZ
            if ($finalRole === 'APRENDIZ') {
                $colUserFk = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                $nCol = Schema::hasColumn('aprendices','nombres') ? 'nombres' : (Schema::hasColumn('aprendices','nombre') ? 'nombre' : null);
                $aprUpdate = [ 'apellidos' => $data['apellido'] ?? null, 'correo_personal' => $data['email'] ?? null, 'actualizado_en' => now() ];
                if ($nCol) $aprUpdate[$nCol] = $data['nombre'] ?? null;
                if (!empty($data['estado']) && Schema::hasColumn('aprendices', 'estado')) $aprUpdate['estado'] = $data['estado'];
                DB::table('aprendices')->where($colUserFk, $id)->update($aprUpdate);
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'ok' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $request->input('nombre'),
                    'apellidos' => $request->input('apellido'),
                    'email' => $request->input('email'),
                    'role' => $request->input('role'),
                    'estado' => $request->input('estado'),
                ]
            ]);
        }

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
