<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LiderSemillero;
use App\Models\Aprendiz;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\LiderGeneral;


class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validación base (users)
        $rules = [
            'role'     => ['required','string', Rule::in(['ADMIN','INSTRUCTOR','APRENDIZ','LIDER_SEMILLERO','LIDER GENERAL'])],
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','lowercase','email','max:255','unique:users,email'], // correo personal
            'password' => ['required','confirmed', Rules\Password::defaults()],
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
            // 1) Crear usuario (correo personal)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,          // personal
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // 2) LÍDER SEMILLERO
            if ($request->role === 'LIDER_SEMILLERO') {
                LiderSemillero::create([
                    'id_usuario'           => $user->id,
                    'nombre_completo'      => $request->name,
                    'tipo_documento'       => $request->lider_tipo_documento, // CC/CE
                    'documento'            => $request->lider_documento,
                    'correo_institucional' => $user->email, // mismo del usuario
                ]);
            }

            // 3) APRENDIZ
            if ($request->role === 'APRENDIZ') {
                Aprendiz::create([
                    'id_usuario'           => $user->id,
                    'nombre_completo'      => $request->name,
                    'ficha'                => $request->aprendiz_ficha,
                    'programa'             => $request->aprendiz_programa,
                    'tipo_documento'       => $request->aprendiz_tipo_documento,   // CC/CE/TI
                    'documento'            => $request->aprendiz_documento,
                    'celular'              => $request->aprendiz_celular,
                    'correo_institucional' => $request->aprendiz_correo_institucional,
                    'contacto'             => $request->aprendiz_contacto,
                    'celular_contacto'     => $request->aprendiz_cel_contacto,
                    'correo_personal'      => $user->email, // copia del users.email
                ]);
            }
       
            


            // 4) ADMIN o INSTRUCTOR (no se requiere tabla extra)






            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}

