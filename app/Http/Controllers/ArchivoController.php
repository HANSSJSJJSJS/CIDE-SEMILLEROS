<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'archivo' => 'required|file|mimes:pdf|max:5120', // hasta 5MB
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        // Obtener información del archivo
        $archivo = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreAlmacenado = uniqid() . '_' . $nombreOriginal; // nombre único
        $ruta = $archivo->storeAs('archivos', $nombreAlmacenado, 'public'); // carpeta archivos/ dentro de storage/app/public

        // Crear un nuevo registro en la base de datos
        Archivo::create([
            'user_id' => Auth::id(),
            'proyecto_id' => $request->proyecto_id,
            'nombre_original' => $nombreOriginal,
            'nombre_almacenado' => $nombreAlmacenado,
            'ruta' => $ruta,
            'estado' => 'pendiente', // Por defecto
            'mime_type' => $archivo->getClientMimeType(),
            'subido_en' => now(),
        ]);

        return redirect()->route('aprendiz.archivos.index')->with('success', 'Archivo subido exitosamente.');
    }

    public function download($id)
{
    $archivo = Archivo::findOrFail($id);
    $path = storage_path('app/public/'.$archivo->ruta);

    return response()->download($path, $archivo->nombre_original);
}
}
