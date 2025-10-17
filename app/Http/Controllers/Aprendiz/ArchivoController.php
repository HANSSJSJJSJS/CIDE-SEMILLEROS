<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    // Ver todos los archivos del usuario (solo los suyos)
    public function index()
    {
        $archivos = Archivo::where('user_id', Auth::id())->latest()->get();

        return view('Aprendiz.Archivos.index', compact('archivos'));
    }

    // Subir archivo PDF
    public function upload(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:10240', // máx 10MB
        ]);

        $archivoSubido = $request->file('archivo');

        $ruta = $archivoSubido->store('archivos_aprendiz');

        Archivo::create([
            'user_id' => Auth::id(),
            'nombre_archivo' => $ruta,
        ]);

        return back()->with('success', 'Archivo subido correctamente.');
    }

    // Descargar archivo (si es del mismo usuario)
    public function download($id)
    {
        $archivo = Archivo::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return Storage::download($archivo->nombre_archivo);
    }
}
