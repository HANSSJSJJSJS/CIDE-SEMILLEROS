<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;
use App\Models\LiderGeneral;
use App\Models\Administrador;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validación base (USERS) — ya NO pedimos 'name'; pedimos 'nombres' y 'apellidos'
        $rules = [
            'role'       => ['required','string', Rule::in(['ADMIN','INSTRUCTOR','APRENDIZ','LIDER_SEMILLERO','LIDER GENERAL'])],
            'nombres'    => ['required','string','max:255'],
            'apellidos'  => ['required','string','max:255'],
            'email'      => ['required','string','lowercase','email','max:255','unique:users,email'], // correo personal
            'password'   => ['required','confirmed', Rules\Password::defaults()],
        ];

        // LÍDER SEMILLERO (extras)
        if ($request->role === 'LIDER_SEMILLERO') {
            $rules = array_merge($rules, [
                'lider_tipo_documento' => ['required','string', Rule::in(['CC','CE'])],
                'lider_documento'      => ['required','string','max:50','unique:lideres_semillero,documento'],
            ]);
        }

        // APRENDIZ (extras)
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

        $user = DB::transaction(function () use ($request) {
            // 1) Crear usuario (name = nombres + apellidos)
            $user = User::create([
                'name'     => $request->nombres . ' ' . $request->apellidos,
                'email'    => $request->email,          // correo personal
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

                // 5) ADMINISTRADOR
             if ($request->role === 'ADMIN') {
                 Administrador::create([
                    'id_usuario'           => $user->id,
                    'nombre'               => $request->nombres,
                    'apellidos'            => $request->apellidos,
                    'Correo_institucional' => $user->email, // o "Correo_institucional" si tu columna va con mayúscula
                ]);
             }

            // 2) LÍDER SEMILLERO
            if ($request->role === 'LIDER_SEMILLERO') {
                LiderSemillero::create([
                    'id_usuario'           => $user->id,
                    'nombres'              => $request->nombres,           // ← corregido
                    'apellidos'            => $request->apellidos,         // ← corregido
                    'tipo_documento'       => $request->lider_tipo_documento,
                    'documento'            => $request->lider_documento,
                    'correo_institucional' => $user->email,                // mismo correo del user
                ]);
            }

            // 3) APRENDIZ
            if ($request->role === 'APRENDIZ') {
                Aprendiz::create([
                    'id_usuario'           => $user->id,
                    'nombres'              => $request->nombres,           // ← corregido
                    'apellidos'            => $request->apellidos,         // ← corregido
                    'ficha'                => $request->aprendiz_ficha,
                    'programa'             => $request->aprendiz_programa,
                    'tipo_documento'       => $request->aprendiz_tipo_documento,
                    'documento'            => $request->aprendiz_documento,
                    'celular'              => $request->aprendiz_celular,
                    'correo_institucional' => $request->aprendiz_correo_institucional,
                    'contacto'             => $request->aprendiz_contacto,
                    'celular_contacto'     => $request->aprendiz_cel_contacto,
                    'correo_personal'      => $user->email,                // copia del users.email
                ]);
            }

            // 5) LÍDER GENERAL
        if ($request->role === 'LIDER GENERAL') {
            LiderGeneral::create([
                'id_usuario'           => $user->id,
                'nombre'               => $request->name,   // mismo nombre que users.name
                'Correo_institucional' => $user->email,     // mismo correo que users.email
            ]);
        }

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
