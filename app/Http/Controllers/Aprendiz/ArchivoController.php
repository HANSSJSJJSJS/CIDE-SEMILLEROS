<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Proyecto;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User si no lo está

class ArchivoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el usuario autenticado.
        $user = Auth::user();

        // 2. Obtener los proyectos del usuario (pivot proyecto_user)
        $proyectos = $user->proyectos;

        // 3. Filtros simples
        $proyecto = $request->query('proyecto');
        $fecha = $request->query('fecha');

        // 4. Consulta con filtros y paginación
        $archivos = Archivo::with(['proyecto'])
            ->where('user_id', $user->id)
            ->when($proyecto, fn($q)=> $q->where('proyecto_id', $proyecto))
            ->when($fecha, fn($q)=> $q->whereDate('subido_en', $fecha))
            ->orderByDesc('subido_en')
            ->paginate(10)
            ->appends(['proyecto'=>$proyecto, 'fecha'=>$fecha]);

        return view('aprendiz.archivos.index', compact('proyectos', 'archivos', 'proyecto', 'fecha'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $proyectos = $user->proyectos ?? collect();
        $proyectoSeleccionado = $request->query('proyecto');
        return view('aprendiz.archivos.upload', compact('proyectos', 'proyectoSeleccionado'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id_proyecto',
            'documentos' => 'required', // puede ser array o archivo único
            'documentos.*' => 'mimes:pdf|max:10240',
        ]);

        $files = [];
        if ($request->hasFile('documentos')) {
            $files = is_array($request->file('documentos'))
                ? $request->file('documentos')
                : [$request->file('documentos')];
        }

        $resultados = [];
        foreach ($files as $documento) {
            if (!$documento) { continue; }
            $nombreOriginal = $documento->getClientOriginalName();
            $nombreAlmacenado = uniqid().'_'.$nombreOriginal;
            $ruta = $documento->storeAs('documentos', $nombreAlmacenado, 'public');

            $registro = Archivo::create([
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreAlmacenado,
                'ruta' => $ruta,
                'proyecto_id' => $request->proyecto_id,
                'user_id' => Auth::id(),
                'estado' => 'aprobado',
                'mime_type' => $documento->getClientMimeType(),
                'subido_en' => now(),
            ]);

            // Crear evidencia asociada para reflejarse en el detalle del proyecto
            Evidencia::create([
                'id_proyecto' => $request->proyecto_id,
                'id_usuario'  => Auth::id(),
                'nombre'      => $nombreOriginal,
                'estado'      => 'pendiente',
            ]);

            $resultados[] = [
                'id' => $registro->id ?? null,
                'nombre_original' => $registro->nombre_original,
                'ruta' => Storage::disk('public')->url($registro->ruta),
                'estado' => $registro->estado,
                'mime_type' => $registro->mime_type,
                'subido_en' => $registro->subido_en,
            ];
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'archivos' => $resultados]);
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
