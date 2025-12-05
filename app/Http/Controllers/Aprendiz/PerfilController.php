<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;


class PerfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Buscar el aprendiz vinculado al usuario actual sin asumir columnas específicas
        $aprendiz = null;
        if (Schema::hasTable('aprendices')) {
            if (Schema::hasColumn('aprendices', 'id_usuario')) {
                $aprendiz = DB::table('aprendices')->where('id_usuario', $user->id)->first();
            } elseif (Schema::hasColumn('aprendices', 'user_id')) {
                $aprendiz = DB::table('aprendices')->where('user_id', $user->id)->first();
            } elseif (Schema::hasColumn('aprendices', 'email')) {
                $aprendiz = DB::table('aprendices')->where('email', $user->email)->first();
            }
        }

        return view('aprendiz.perfil.perfil_aprendiz', [
            'user' => $user,
            'aprendiz' => $aprendiz
        ]);
    }


    public function edit()
    {
        return view('aprendiz.perfil.perfil_aprendiz', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'tipo_documento' => 'nullable|string|max:50',
            'documento' => 'nullable|string|max:60',
            'celular' => 'nullable|string|max:30',
            'sexo' => 'nullable|string|max:20',
            'rh' => 'nullable|string|max:5',
            'correo_personal' => 'nullable|email|max:255',
            'nivel_academico' => 'nullable|string|max:60',
        ]);

        $user = User::findOrFail(Auth::id());
        $fullName = trim((string)$request->name);
        if (Schema::hasColumn('users', 'name')) {
            $user->name = $fullName;
        }
        if ($fullName !== '') {
            $parts = preg_split('/\s+/', $fullName);
            $user->nombre = $parts[0] ?? null;
            $user->apellidos = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : null;
        }
        $user->email = $request->email;

        // Guardar campos personales en users si existen
        try {
            if (Schema::hasColumn('users','tipo_documento') && $request->filled('tipo_documento')) {
                $user->tipo_documento = $request->input('tipo_documento');
            }
            if (Schema::hasColumn('users','documento') && $request->filled('documento')) {
                $user->documento = $request->input('documento');
            }
            if (Schema::hasColumn('users','celular') && $request->filled('celular')) {
                $user->celular = $request->input('celular');
            }
            // sexo/genero
            if ($request->filled('sexo')) {
                if (Schema::hasColumn('users','sexo')) {
                    $user->sexo = $request->input('sexo');
                } elseif (Schema::hasColumn('users','genero')) {
                    $user->genero = $request->input('sexo');
                }
            }
            // RH variantes: rh, tipo_rh, tipo_sangre, grupo_sanguineo, grupo_sangre
            if ($request->filled('rh')) {
                $rh = $request->input('rh');
                if (Schema::hasColumn('users','rh')) $user->rh = $rh;
                elseif (Schema::hasColumn('users','tipo_rh')) $user->tipo_rh = $rh;
                elseif (Schema::hasColumn('users','tipo_sangre')) $user->tipo_sangre = $rh;
                elseif (Schema::hasColumn('users','grupo_sanguineo')) $user->grupo_sanguineo = $rh;
                elseif (Schema::hasColumn('users','grupo_sangre')) $user->grupo_sangre = $rh;
            }
            // correo personal
            if ($request->filled('correo_personal')) {
                if (Schema::hasColumn('users','correo_personal')) {
                    $user->correo_personal = $request->input('correo_personal');
                } elseif (Schema::hasColumn('users','email_personal')) {
                    $user->email_personal = $request->input('correo_personal');
                }
            }
            // nivel académico en users si hay alguna variante
            if ($request->filled('nivel_academico')) {
                $nivel = $request->input('nivel_academico');
                if (Schema::hasColumn('users','nivel_academico')) $user->nivel_academico = $nivel;
                elseif (Schema::hasColumn('users','nivelAcademico')) $user->nivelAcademico = $nivel;
                elseif (Schema::hasColumn('users','nivel')) $user->nivel = $nivel;
                elseif (Schema::hasColumn('users','nivel_educativo')) $user->nivel_educativo = $nivel;
            }
        } catch (\Throwable $e) { /* tolerante */ }

        $user->save();

        // Guardar nivel académico y correo personal en aprendices si existen
        try {
            if (Schema::hasTable('aprendices')) {
                $apr = null;
                if (Schema::hasColumn('aprendices','user_id')) {
                    $apr = DB::table('aprendices')->where('user_id', $user->id)->first();
                } elseif (Schema::hasColumn('aprendices','id_usuario')) {
                    $apr = DB::table('aprendices')->where('id_usuario', $user->id)->first();
                } elseif (Schema::hasColumn('aprendices','correo_institucional')) {
                    $apr = DB::table('aprendices')->where('correo_institucional', $user->email)->first();
                }
                if ($apr) {
                    $upd = [];
                    if ($request->filled('nivel_academico')) {
                        if (Schema::hasColumn('aprendices','nivel_academico')) $upd['nivel_academico'] = $request->input('nivel_academico');
                        elseif (Schema::hasColumn('aprendices','nivel_educativo')) $upd['nivel_educativo'] = $request->input('nivel_academico');
                        elseif (Schema::hasColumn('aprendices','nivel')) $upd['nivel'] = $request->input('nivel_academico');
                    }
                    if ($request->filled('correo_personal') && Schema::hasColumn('aprendices','correo_personal')) {
                        $upd['correo_personal'] = $request->input('correo_personal');
                    }
                    if (!empty($upd)) {
                        DB::table('aprendices')->where('id_aprendiz', $apr->id_aprendiz)->update($upd);
                    }
                }
            }
        } catch (\Throwable $e) { /* tolerante */ }

        return redirect()->route('aprendiz.perfil.show')->with('success', 'Perfil actualizado correctamente.');
    }
}
