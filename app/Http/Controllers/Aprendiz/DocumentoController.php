<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Aprendiz;

class DocumentoController extends Controller
{
    /**
     * Mostrar la vista de documentos del aprendiz
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Obtener el aprendiz actual
        $aprendiz = Aprendiz::where('id_usuario', $userId)->first();
        
        if (!$aprendiz) {
            return redirect()->back()->with('error', 'No se encontró el perfil de aprendiz');
        }
        
        // Obtener proyectos asignados al aprendiz
        $proyectos = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where('documentos.id_aprendiz', $aprendiz->id_aprendiz)
            ->select('proyectos.id_proyecto', 'proyectos.nombre_proyecto')
            ->distinct()
            ->get();
        
        // Obtener documentos del aprendiz (excluir placeholders)
        $documentos = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where('documentos.id_aprendiz', $aprendiz->id_aprendiz)
            ->whereRaw("documentos.documento NOT LIKE 'PLACEHOLDER%'")
            ->select(
                'documentos.*',
                'proyectos.nombre_proyecto'
            )
            ->orderBy('documentos.fecha_subida', 'desc')
            ->get();
        
        return view('aprendiz.documentos', compact('proyectos', 'documentos', 'aprendiz'));
    }
    
    /**
     * Subir un nuevo documento
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $aprendiz = Aprendiz::where('id_usuario', $userId)->firstOrFail();
        
        $request->validate([
            'id_proyecto' => 'required|exists:proyectos,id_proyecto',
            'archivo' => 'required|file|max:10240', // Máximo 10MB
            'descripcion' => 'nullable|string|max:255',
        ]);
        
        // Verificar que el aprendiz está asignado al proyecto
        $asignado = DB::table('documentos')
            ->where('id_proyecto', $request->id_proyecto)
            ->where('id_aprendiz', $aprendiz->id_aprendiz)
            ->exists();
        
        if (!$asignado) {
            return back()->with('error', 'No estás asignado a este proyecto');
        }
        
        $archivo = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $extension = $archivo->getClientOriginalExtension();
        $tamanio = $archivo->getSize();
        $tipoArchivo = $archivo->getMimeType();
        
        // Generar nombre único para el archivo
        $nombreArchivo = time() . '_' . $aprendiz->id_aprendiz . '_' . $nombreOriginal;
        
        // Guardar el archivo en storage/app/public/documentos
        $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');
        
        // Insertar en la base de datos
        DB::table('documentos')->insert([
            'id_proyecto' => $request->id_proyecto,
            'id_aprendiz' => $aprendiz->id_aprendiz,
            'documento' => $request->descripcion ?: $nombreOriginal,
            'ruta_archivo' => $ruta,
            'tipo_archivo' => $tipoArchivo,
            'tamanio' => $tamanio,
            'fecha_subida' => now(),
        ]);
        
        return back()->with('success', 'Documento subido correctamente');
    }
    
    /**
     * Descargar un documento
     */
    public function download($id)
    {
        $userId = Auth::id();
        $aprendiz = Aprendiz::where('id_usuario', $userId)->firstOrFail();
        
        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where('id_aprendiz', $aprendiz->id_aprendiz)
            ->first();
        
        if (!$documento) {
            return back()->with('error', 'Documento no encontrado');
        }
        
        if (!Storage::disk('public')->exists($documento->ruta_archivo)) {
            return back()->with('error', 'El archivo no existe en el servidor');
        }
        
        $absPath = storage_path('app/public/'.$documento->ruta_archivo);
        if (!file_exists($absPath)) {
            return back()->with('error', 'El archivo no existe en el servidor');
        }
        return response()->download($absPath, basename($documento->ruta_archivo));
    }
    
    /**
     * Eliminar un documento
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $aprendiz = Aprendiz::where('id_usuario', $userId)->firstOrFail();
        
        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where('id_aprendiz', $aprendiz->id_aprendiz)
            ->first();
        
        if (!$documento) {
            return back()->with('error', 'Documento no encontrado');
        }
        
        // No permitir eliminar placeholders
        if (str_starts_with($documento->documento, 'PLACEHOLDER_')) {
            return back()->with('error', 'No se puede eliminar este registro');
        }
        
        // Eliminar archivo físico
        if (Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }
        
        // Eliminar registro de la base de datos
        DB::table('documentos')->where('id_documento', $id)->delete();
        
        return back()->with('success', 'Documento eliminado correctamente');
    }
}
