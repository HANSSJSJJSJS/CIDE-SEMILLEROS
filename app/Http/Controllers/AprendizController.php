<?php

namespace App\Http\Controllers;

use App\Models\Aprendiz;
use Illuminate\Http\Request;

class AprendizController extends Controller
{
    public function dashboard(Request $request)
    {
        $aprendiz = $request->user(); // Obtener el aprendiz autenticado
        return view('aprendiz.dashboard', compact('aprendiz'));
    }

    public function verPerfil(Request $request)
    {
        $aprendiz = $request->user(); // Obtener el aprendiz autenticado
        return view('aprendiz.perfil', compact('aprendiz'));
    }

    public function verProyectos(Request $request)
    {
        $aprendiz = $request->user(); // Obtener el aprendiz autenticado
        $proyectos = $aprendiz->proyectos; // Obtener proyectos asociados al aprendiz
        return view('aprendiz.proyectos.index', compact('proyectos'));
    }

    public function verArchivos(Request $request)
    {
        $aprendiz = $request->user(); // Obtener el aprendiz autenticado
        $archivos = $aprendiz->archivos; // Obtener archivos asociados al aprendiz
        return view('aprendiz.archivos.index', compact('archivos'));
    }

    public function subirArchivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:2048', // Validar que sea un archivo PDF
        ]);

        $aprendiz = $request->user(); // Obtener el aprendiz autenticado
        $archivo = new Archivo();
        $archivo->nombre = $request->file('archivo')->getClientOriginalName();
        $archivo->ruta = $request->file('archivo')->store('archivos', 'public'); // Guardar el archivo
        $archivo->aprendiz_id = $aprendiz->id; // Asociar el archivo al aprendiz
        $archivo->save();

        return redirect()->back()->with('success', 'Archivo subido exitosamente.');
    }
}
