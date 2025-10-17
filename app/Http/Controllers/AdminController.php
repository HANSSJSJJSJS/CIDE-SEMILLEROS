<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;
use App\Models\LiderGeneral;
use App\Models\Administrador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function index(){ return view('usuarios.index'); }
    public function create(){ return view('usuarios.create'); }
    public function store(Request $r){ /* ... */ }
    public function show($id){ /* ... */ }
    public function edit($id){ /* ... */ }
    public function update(Request $r,$id){ /* ... */ }
    public function destroy($id){ /* ... */ }
}


class AdminUserController extends Controller
{
    public function create()
    {
        // Reusamos la misma vista de Breeze si quieres (auth.register),
        // pero cambiaremos la acción del form para que apunte a admin.users.store
        return view('auth.register', [
            'adminMode' => true, // bandera opcional por si la vista necesita ajustar textos
        ]);
    }

    public function store(Request $request)
    {
        // Misma validación que ya usas en RegisteredUserController
        $rules = [
            'role'       => ['required','string', Rule::in(['ADMIN','INSTRUCTOR','APRENDIZ','LIDER_SEMILLERO','LIDER_GENERAL'])],
            'nombres'    => ['required','string','max:255'],
            'apellidos'  => ['required','string','max:255'],
            'email'      => ['required','string','lowercase','email','max:255','unique:users,email'],
            'password'   => ['required','confirmed', Rules\Password::defaults()],
        ];

        if ($request->role === 'LIDER_SEMILLERO') {
            $rules = array_merge($rules, [
                'lider_tipo_documento' => ['required','string', Rule::in(['CC','CE'])],
                'lider_documento'      => ['required','string','max:50','unique:lideres_semillero,documento'],
            ]);
        }

        if ($request->role === 'APRENDIZ') {
            $rules = array_merge($rules, [
                'aprendiz_ficha'                => ['required','string','max:100'],
                'aprendiz_programa'             => ['required','string','max:255'],
                'aprendiz_tipo_documento'       => ['required','string', Rule::in(['CC','CE','TI'])],
                'aprendiz_documento'            => ['required','string','max:50','unique:aprendices,documento'],
                'aprendiz_celular'              => ['required','string','max:30'],
                'aprendiz_correo_institucional' => ['required','email','max:255'],
                'aprendiz_contacto'             => ['required','string','max:255'],
                'aprendiz_cel_contacto'         => ['required','string','max:30'],
            ]);
        }

        $request->validate($rules);

        // MUY IMPORTANTE: aquí NO iniciamos sesión con el usuario creado
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->nombres . ' ' . $request->apellidos,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // Crear perfil según rol (mismo flujo que ya tienes)
            if ($user->role === 'ADMIN') {
                Administrador::create([
                    'id_usuario' => $user->id,
                    'nombre'     => $request->nombres,
                    'apellidos'  => $request->apellidos,
                    'correo'     => $user->email,
                ]);
            }

            if ($user->role === 'LIDER_SEMILLERO') {
                LiderSemillero::create([
                    'id_usuario'           => $user->id,
                    'nombres'              => $request->nombres,
                    'apellidos'            => $request->apellidos,
                    'tipo_documento'       => $request->lider_tipo_documento,
                    'documento'            => $request->lider_documento,
                    'correo_institucional' => $user->email,
                ]);
            }

            if ($user->role === 'APRENDIZ') {
                Aprendiz::create([
                    'id_usuario'           => $user->id,
                    'nombres'              => $request->nombres,
                    'apellidos'            => $request->apellidos,
                    'ficha'                => $request->aprendiz_ficha,
                    'programa'             => $request->aprendiz_programa,
                    'tipo_documento'       => $request->aprendiz_tipo_documento,
                    'documento'            => $request->aprendiz_documento,
                    'celular'              => $request->aprendiz_celular,
                    'correo_institucional' => $request->aprendiz_correo_institucional,
                    'contacto'             => $request->aprendiz_contacto,
                    'celular_contacto'     => $request->aprendiz_cel_contacto,
                    'correo_personal'      => $user->email,
                ]);
            }

            if ($user->role === 'LIDER_GENERAL' || $user->role === 'LIDER GENERAL') {
                LiderGeneral::create([
                    'id_usuario'           => $user->id,
                    'nombres'              => $request->nombres,
                    'apellidos'            => $request->apellidos,
                    'Correo_institucional' => $user->email,
                ]);
            }
        });

        // Volvemos al panel admin con mensaje de éxito
        return redirect()->route('admin.dashboard')->with('success', 'Usuario creado correctamente.');
    }
}


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

