<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User si no lo está

class ArchivoController extends Controller
{
    public function index()
    {
        // 1. Obtener el usuario autenticado.
        $user = Auth::user();

        // 2. CORRECCIÓN: Obtener los proyectos a través de la relación de muchos a muchos.
        // Esto le dice a Laravel que use la tabla pivote (proyecto_user).
        $proyectos = $user->proyectos;

        // 3. El resto de la lógica para archivos está bien, ya que Archivo sí tiene user_id.
        $archivos = Archivo::where('user_id', $user->id)->get();

        return view('aprendiz.archivos.index', compact('proyectos', 'archivos'));
    }

    public function create()
    {
        return view('aprendiz.archivos.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            // Asegúrate de que el proyecto realmente pertenece al usuario antes de permitir la subida
            'proyecto_id' => 'required|exists:proyectos,id_proyecto', // Nota: Usé 'id_proyecto' si esa es tu clave primaria
            'documentos' => 'required|array',
            'documentos.*' => 'required|mimes:pdf|max:10240'
        ]);

        foreach ($request->file('documentos') as $documento) {
            $ruta = $documento->store('documentos', 'public');

            Archivo::create([
                'nombre_archivo' => $documento->getClientOriginalName(),
                'ruta' => $ruta,
                'proyecto_id' => $request->proyecto_id,
                'user_id' => Auth::id()
            ]);
        }

        return redirect()->route('aprendiz.archivos.index')
            ->with('success', 'Documentos subidos correctamente');
    }

    public function destroy(Archivo $archivo)
    {
        if ($archivo->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($archivo->ruta);
        $archivo->delete();

        return redirect()->route('aprendiz.archivos.index')
            ->with('success', 'Documento eliminado correctamente');
    }
}
