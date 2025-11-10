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
        ]);

        $user = User::findOrFail(Auth::id());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('aprendiz.perfil.show')->with('success', 'Perfil actualizado correctamente.');
    }
}
