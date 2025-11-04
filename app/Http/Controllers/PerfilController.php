<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aprendiz;

class PerfilController extends Controller
{
    public function mostrarPerfil($id)
    {
        $aprendiz = Aprendiz::findOrFail($id);
        return view('aprendiz.perfil', compact('aprendiz'));
    }

    public function actualizarPerfil(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            // Agregar otras validaciones según sea necesario
        ]);

        $aprendiz = Aprendiz::findOrFail($id);
        $aprendiz->nombre = $request->nombre;
        $aprendiz->email = $request->email;
        // Actualizar otros campos según sea necesario
        $aprendiz->save();

        return redirect()->route('aprendiz.perfil', ['id' => $id])->with('success', 'Perfil actualizado correctamente.');
    }
}
