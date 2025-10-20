<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PerfilController extends Controller
{
    public function show()
{
    $user = Auth::user();

    // Buscar el aprendiz vinculado al usuario actual
    $aprendiz = \DB::table('aprendices')
        ->where('id_usuario', $user->id)
        ->first();

    return view('aprendiz.perfil.show', [
        'user' => $user,
        'aprendiz' => $aprendiz
    ]);
}


    public function edit()
    {
        return view('aprendiz.perfil.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('aprendiz.perfil.show')->with('success', 'Perfil actualizado correctamente.');
    }
}
