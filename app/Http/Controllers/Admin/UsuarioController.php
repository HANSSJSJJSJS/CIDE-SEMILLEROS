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
    // =========================
    // Listado con filtros
    // =========================
    public function index(Request $request)
    {
        $q           = trim($request->get('q',''));
        $roleFilter  = trim($request->get('role',''));
        $semilleroId = $request->integer('semillero_id');

        // Normaliza el valor del select a como se guarda en BD
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
            ])
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('users.name','like',"%{$q}%")
                      ->orWhere('users.apellidos','like',"%{$q}%")
                      ->orWhere('users.email','like',"%{$q}%");
                });
            })
            ->when($roleFilter !== '', fn($w) => $w->where('users.role',$roleFilter))
            ->when($semilleroId, fn($w) => $w->where('s.id_semillero', $semilleroId))
            ->orderByDesc('users.created_at')
            ->paginate(12)
            ->withQueryString();

        $semilleros = DB::table('semilleros')
            ->select('id_semillero','nombre')
            ->orderBy('nombre')
            ->get();

        $roles = [
            'ADMIN'           => 'ADMIN',
            'LIDER_GENERAL'   => 'LIDER GENERAL',
            'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
            'APRENDIZ'        => 'APRENDIZ',
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

    // =========================
    // Form de creación (opcional)
    // =========================
    public function create()
    {
        $roles = ['ADMIN','LIDER_GENERAL','LIDER_SEMILLERO','APRENDIZ'];
        $semilleros = DB::table('semilleros')->select('id_semillero','nombre')->orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('roles','semilleros'));
    }

    // =========================
    // Guardar (form normal)
    // =========================
    public function store(Request $request)
    {
        $roleMap = [
            'ADMIN'           => 'ADMIN',
            'LIDER_GENERAL'   => 'LIDER GENERAL',
            'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
            'APRENDIZ'        => 'APRENDIZ',
        ];

        $rules = [
            'role'     => 'required|in:ADMIN,LIDER_GENERAL,LIDER_SEMILLERO,APRENDIZ',
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];

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

        $data = $request->validate($rules);
        $role = $roleMap[$data['role']];

        try {
            DB::transaction(function () use ($data, $role) {
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
                        $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
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
            return back()->withErrors(['general' => 'Ocurrió un error al crear el usuario.'])->withInput();
        }
    }

    // =========================
    // Editar (VISTA)
    // =========================
    public function editForm(User $usuario)
    {
        // Si necesitas combos:
        // $roles = ['ADMIN','LIDER GENERAL','LIDER_SEMILLERO','APRENDIZ'];
        // $semilleros = DB::table('semilleros')->select('id_semillero','nombre')->orderBy('nombre')->get();

        return view('admin.usuarios.edit', compact('usuario')); // , 'roles', 'semilleros'
    }

    // =========================
    // Editar (AJAX JSON)
    // =========================
    public function editAjax($id)
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

    // =========================
    // Actualizar
    // =========================
 public function update(Request $request, User $usuario)
{
    $data = $request->validate([
        'nombre'   => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($usuario->id)],
    ]);

    DB::transaction(function () use ($usuario, $data) {
        // ← Asignación explícita, sin mass assignment
        $usuario->name      = $data['nombre'];
        $usuario->apellidos = $data['apellido'];
        $usuario->email     = $data['email'];
        $usuario->save(); // << GUARDA

        // Perfiles
        switch ($usuario->role) {
            case 'ADMIN':
                DB::table('administradores')->where('id_usuario', $usuario->id)->update([
                    'nombres'        => $data['nombre'],
                    'apellidos'      => $data['apellido'],
                    'actualizado_en' => now(),
                ]);
                break;

            case 'LIDER GENERAL':
                DB::table('lider_general')->where('id_lidergen', $usuario->id)->update([
                    'nombres'              => $data['nombre'],
                    'apellidos'            => $data['apellido'],
                    'Correo_institucional' => $data['email'],
                    'actualizado_en'       => now(),
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
                $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                DB::table('aprendices')->where($colUserFk, $usuario->id)->update([
                    'nombres'         => $data['nombre'],
                    'apellidos'       => $data['apellido'],
                    'correo_personal' => $data['email'],
                    'actualizado_en'  => now(),
                ]);
                break;
        }
    });

    return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
}


    // =========================
    // Eliminar
    // =========================
    public function destroy(User $usuario)
    {
        DB::transaction(function () use ($usuario) {
            switch ($usuario->role) {
                case 'ADMIN':
                    DB::table('administradores')->where('id_usuario', $usuario->id)->delete();
                    break;
                case 'LIDER GENERAL':
                    DB::table('lider_general')->where('id_lidergen', $usuario->id)->delete();
                    break;
                case 'LIDER_SEMILLERO':
                    DB::table('lideres_semillero')->where('id_lider_semi', $usuario->id)->delete();
                    break;
                case 'APRENDIZ':
                    $colUserFk = DB::getSchemaBuilder()->hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'user_id';
                    DB::table('aprendices')->where($colUserFk, $usuario->id)->delete();
                    break;
            }

            $usuario->delete();
        });

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    // =========================
    // (Opcional) Store vía AJAX
    // =========================
    public function storeAjax(Request $request)
    {
        // Puedes reutilizar las mismas reglas de store()
        // o delegar internamente a store($request)
        return $this->store($request);
    }
}
