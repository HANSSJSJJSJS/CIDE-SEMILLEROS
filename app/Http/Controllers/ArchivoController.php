<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    public function index()
    {
        // Obtener todos los archivos disponibles para el aprendiz
        $archivos = Archivo::all();
        return view('aprendiz.archivos.index', compact('archivos'));
    }

    public function show($id)
    {
        // Mostrar los detalles de un archivo específico
        $archivo = Archivo::findOrFail($id);
        return view('aprendiz.archivos.show', compact('archivo'));
    }

    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Subir el archivo
        $path = $request->file('archivo')->store('archivos');

        // Crear un nuevo registro en la base de datos
        Archivo::create([
            'ruta' => $path,
            'nombre' => $request->file('archivo')->getClientOriginalName(),
        ]);

        return redirect()->route('aprendiz.archivos.index')->with('success', 'Archivo subido exitosamente.');
    }

    public function download($id)
    {
        // Descargar un archivo específico
        $archivo = Archivo::findOrFail($id);
        return Storage::download($archivo->ruta);
    }
}
