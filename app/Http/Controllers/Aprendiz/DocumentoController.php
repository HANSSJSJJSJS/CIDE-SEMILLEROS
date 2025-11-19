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

        // Obtener el aprendiz actual (compat: id_usuario o user_id)
        $aprendiz = $this->getAprendizByUserId($userId);

        if (!$aprendiz) {
            // Mostrar la vista de forma tolerante aunque no exista el perfil, para no dejar la página vacía
            session()->flash('error', 'No se encontró el perfil de aprendiz');
            $proyectos = collect([]);
            $documentos = collect([]);
            $pendientesAsignadas = collect([]);
            $aprendizStub = (object) [
                'id_aprendiz' => 0,
                'nombre_completo' => '',
                'nombres' => '',
                'apellidos' => '',
            ];
            return view('aprendiz.documentos', compact('proyectos', 'documentos', 'pendientesAsignadas'))
                ->with('aprendiz', $aprendizStub);
        }

        // Obtener proyectos asignados al aprendiz, aunque aún no tenga documentos
        $proyectoIds = [];
        // 1) proyecto_user (user_id -> id_proyecto)
        if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_user')) {
            $proyectoIds = DB::table('proyecto_user')
                ->where('user_id', $userId)
                ->pluck('id_proyecto')->map(fn($v)=>(int)$v)->all();
        }
        // 2) aprendiz_proyecto (id_aprendiz -> id_proyecto) si aún no hay ids
        if (empty($proyectoIds) && \Illuminate\Support\Facades\Schema::hasTable('aprendiz_proyecto')) {
            $proyectoIds = DB::table('aprendiz_proyecto')
                ->where('id_aprendiz', $aprendiz->id_aprendiz)
                ->pluck('id_proyecto')->map(fn($v)=>(int)$v)->all();
        }
        // 3) Si no hay pivote, intentar deducir por documentos (como antes)
        if (empty($proyectoIds) && \Illuminate\Support\Facades\Schema::hasTable('documentos')) {
            $docAprCol = $this->getDocumentoAprendizColumn();
            $aprId = $this->getAprendizId($aprendiz);
            $proyectoIds = DB::table('documentos')
                ->where($docAprCol, $aprId)
                ->pluck('id_proyecto')->map(fn($v)=>(int)$v)->all();
        }
        $proyectos = empty($proyectoIds)
            ? collect([])
            : DB::table('proyectos')
                ->whereIn('id_proyecto', $proyectoIds)
                ->select('id_proyecto','nombre_proyecto')
                ->orderBy('nombre_proyecto')
                ->get();

        // Si viene ?proyecto=ID y no está en la lista (porque no hay pivote), incluirlo para permitir la selección
        $proyectoQS = request('proyecto');
        if ($proyectoQS && !$proyectos->contains(fn($p)=> (string)$p->id_proyecto === (string)$proyectoQS)) {
            $p = DB::table('proyectos')
                ->where('id_proyecto', $proyectoQS)
                ->select('id_proyecto','nombre_proyecto')
                ->first();
            if ($p) { $proyectos = $proyectos->push($p); }
        }

        // Obtener documentos del aprendiz (excluir placeholders)
        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId = $this->getAprendizId($aprendiz);
        $documentos = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where('documentos.' . $docAprCol, $aprId)
            ->whereRaw("documentos.documento NOT LIKE 'PLACEHOLDER%'")
            ->whereNotNull('documentos.ruta_archivo')
            ->where('documentos.ruta_archivo', '!=', '')
            ->select(
                'documentos.*',
                'proyectos.nombre_proyecto'
            )
            ->orderBy('documentos.fecha_subida', 'desc')
            ->get();

        $pendientesAsignadas = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where('documentos.' . $docAprCol, $aprId)
            ->where(function($q){
                $q->whereNull('documentos.ruta_archivo')
                  ->orWhere('documentos.ruta_archivo', '=', '');
            })
            ->select('documentos.*','proyectos.nombre_proyecto')
            ->orderBy('documentos.fecha_subida','desc')
            ->get();

        return view('aprendiz.documentos', compact('proyectos', 'documentos', 'aprendiz', 'pendientesAsignadas'));
    }

    /**
     * Subir un nuevo documento
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz) { return back()->with('error', 'No se encontró el perfil de aprendiz'); }

        $request->validate([
            'id_proyecto' => 'required|exists:proyectos,id_proyecto',
            'archivo' => 'required|file|max:10240', // Máximo 10MB
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Verificar que el usuario (aprendiz) está asignado al proyecto mediante pivote
        $asignado = false;
        if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_user')) {
            $asignado = DB::table('proyecto_user')
                ->where('user_id', $userId)
                ->where('id_proyecto', $request->id_proyecto)
                ->exists();
        }
        if (!$asignado && \Illuminate\Support\Facades\Schema::hasTable('aprendiz_proyecto')) {
            $asignado = DB::table('aprendiz_proyecto')
                ->where('id_aprendiz', $aprendiz->id_aprendiz)
                ->where('id_proyecto', $request->id_proyecto)
                ->exists();
        }

        if (!$asignado) {
            // Permitir si ya existe relación implícita por documentos previos (incluye placeholders)
            $docAprCol = $this->getDocumentoAprendizColumn();
            $aprId = $this->getAprendizId($aprendiz);
            $relPorDocs = \Illuminate\Support\Facades\Schema::hasTable('documentos')
                ? DB::table('documentos')
                    ->where('id_proyecto', $request->id_proyecto)
                    ->where($docAprCol, $aprId)
                    ->exists()
                : false;

            // O si no existen tablas de pivote, no podemos comprobar asignación
            $noHayPivotes = !\Illuminate\Support\Facades\Schema::hasTable('proyecto_user')
                         && !\Illuminate\Support\Facades\Schema::hasTable('aprendiz_proyecto');

            if (!($relPorDocs || $noHayPivotes)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['ok' => false, 'message' => 'No estás asignado a este proyecto'], 403);
                }
                return back()->with('error', 'No estás asignado a este proyecto');
            }
        }

        $archivo = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $extension = $archivo->getClientOriginalExtension();
        $tamanio = $archivo->getSize();
        // Usar la extensión (p.ej. pdf, docx) en vez del MIME para evitar truncamiento en columnas cortas o enums
        $tipoArchivo = strtolower($extension ?? '');

        // Generar nombre único para el archivo
        $nombreArchivo = time() . '_' . $aprendiz->id_aprendiz . '_' . $nombreOriginal;

        // Guardar el archivo en storage/app/public/documentos
        $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

        // Insertar en la base de datos (si id_documento no es autoincremental, generarlo)
        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId = $this->getAprendizId($aprendiz);
        $nextId = null;
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_documento')) {
            // Obtener el siguiente ID manualmente si no es autoincremental
            // Intentar detectar AI no es trivial; en su lugar, preasignamos y usamos insertGetId si es posible
            $max = DB::table('documentos')->max('id_documento');
            $nextId = (int)($max ?? 0) + 1;
        }
        $dataInsert = [
            'id_proyecto' => $request->id_proyecto,
            $docAprCol => $aprId,
            'documento' => $request->descripcion ?: $nombreOriginal,
            'ruta_archivo' => $ruta,
            'tipo_archivo' => $tipoArchivo,
            'tamanio' => $tamanio,
            'fecha_subida' => now(),
        ];
        if (!is_null($nextId)) {
            $dataInsert['id_documento'] = $nextId;
            DB::table('documentos')->insert($dataInsert);
            $idDocumento = $nextId;
        } else {
            // Intentar recuperar el ID insertado (para motores que soportan clave AI)
            try {
                $idDocumento = DB::table('documentos')->insertGetId($dataInsert, 'id_documento');
            } catch (\Throwable $e) {
                DB::table('documentos')->insert($dataInsert);
                // Fallback: buscar por ruta y aprendiz
                $idDocumento = (int) (DB::table('documentos')
                    ->where('ruta_archivo', $ruta)
                    ->where($docAprCol, $aprId)
                    ->max('id_documento') ?? 0);
            }
        }

        // Si es AJAX, devolver JSON con el nuevo registro
        if ($request->ajax() || $request->wantsJson()) {
            $doc = DB::table('documentos')
                ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
                ->where('id_documento', $idDocumento)
                ->select('documentos.*', 'proyectos.nombre_proyecto')
                ->first();

            // Formatear fecha de forma segura
            $fecha = '';
            if (!empty($doc->fecha_subida)) {
                try {
                    $fecha = \Illuminate\Support\Carbon::parse($doc->fecha_subida)->format('d/m/Y H:i');
                } catch (\Throwable $e) {
                    $fecha = (string)$doc->fecha_subida;
                }
            }
            return response()->json([
                'ok' => true,
                'documento' => [
                    'id' => $doc->id_documento,
                    'proyecto' => $doc->nombre_proyecto,
                    'documento' => $doc->documento,
                    'tipo' => pathinfo($doc->ruta_archivo, PATHINFO_EXTENSION),
                    'tamanio_kb' => round(($doc->tamanio ?? 0) / 1024, 2),
                    'fecha' => $fecha,
                    'download_url' => route('aprendiz.documentos.download', $doc->id_documento),
                    'delete_url' => route('aprendiz.documentos.destroy', $doc->id_documento),
                ]
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Documento subido correctamente');
    }

    /**
     * Editar /reemplazar un documento existente de un aprendiz
     */

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz){
            return back()->with('error','No se encontro el perfil de aprendiz');
        }

        // 1) Buscar el documento que pertenece a este aprendiz
        $docAprCol = $this->getDocumentoAprendizColumn(); // ya existe en este controlador
        $aprId = $this->getAprendizId($aprendiz); // ya existe en este controlador

        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where($docAprCol, $aprId)
            ->first();

        if (!$documento) {
            return back()->with('error', 'Documento no encontrado o no te pertenece');
        }

        // Si por algún motivo este registro aún no tiene archivo, no permitir actualizarlo aquí
        if (empty($documento->ruta_archivo)) {
            return back()->with('error', 'Este documento aún no tiene un archivo cargado para actualizar.');
        }

        // 2) Validar campos de edición
        $request->validate([
            'archivo'     => 'nullable|file|max:10240',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $dataUpdate = [];

        // Actualizar descripción/título si viene
        if ($request->filled('descripcion')) {
            $dataUpdate['documento'] = $request->descripcion;
        }

        // 3) Si se sube un nuevo archivo, reemplazarlo
        if ($request->hasFile('archivo')) {

            // Borrar archivo anterior (si existe en disco)
            if (!empty($documento->ruta_archivo)) {
                try {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                } catch (\Throwable $e) {
                    // en caso de error al borrar, lo ignoramos para no romper la edición
                }
            }

            $archivo        = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension      = $archivo->getClientOriginalExtension();
            $tamanio        = $archivo->getSize();
            $tipoArchivo    = strtolower($extension ?? '');

            // Generar nombre único
            $nombreArchivo = time() . '_' . $aprendiz->id_aprendiz . '_' . $nombreOriginal;

            // Guardar nuevo archivo
            $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

            $dataUpdate['ruta_archivo'] = $ruta;
            $dataUpdate['tipo_archivo'] = $tipoArchivo;
            $dataUpdate['tamanio']      = $tamanio;
            $dataUpdate['fecha_subida'] = now();

            // Si no se envió la descripción nueva, usar el nombre del archivo
            if (empty($dataUpdate['documento'])) {
                $dataUpdate['documento'] = $nombreOriginal;
            }
        }

        if (!empty($dataUpdate)) {
            DB::table('documentos')
                ->where('id_documento', $id)
                ->update($dataUpdate);
        }

        return back()->with('success', 'Entrega actualizada correctamente.');
    }


    public function uploadAssigned(Request $request, $id)
    {
        $userId = Auth::id();
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz) { return back()->with('error', 'No se encontró el perfil de aprendiz'); }

        // Validar: permitir archivo o link_url
        if ($request->hasFile('archivo')) {
            $request->validate([
                'archivo' => 'required|file|max:10240',
            ]);
        } else {
            $request->validate([
                'link_url' => 'required|url',
            ]);
        }

        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId = $this->getAprendizId($aprendiz);

        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where($docAprCol, $aprId)
            ->first();
        if (!$documento) { return back()->with('error', 'Documento no encontrado'); }

        // Construir datos a actualizar según tipo
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $tamanio = $archivo->getSize();
            $tipoArchivo = strtolower($extension ?? '');
            $nombreArchivo = time() . '_' . $aprId . '_' . $nombreOriginal;
            $ruta = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('documentos', $archivo, $nombreArchivo);
            $update = [
                'ruta_archivo' => $ruta,
                'tipo_archivo' => $tipoArchivo,
                'tamanio' => $tamanio,
                'fecha_subida' => now(),
            ];
        } else {
            $link = trim((string)$request->input('link_url'));
            $update = [
                'ruta_archivo' => $link,
                // No cambiar tipo_archivo para evitar truncamiento en ENUM; mantener el tipo que definió el líder (p.ej. 'Enlace')
                'tamanio' => 0,
                'fecha_subida' => now(),
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','mime_type')) {
                $update['mime_type'] = 'text/uri-list';
            }
        }
        // No actualizar 'estado' para evitar truncado en esquemas con ENUM desconocido
        DB::table('documentos')
            ->where('id_documento', $id)
            ->update($update);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Archivo subido a la evidencia');
    }

    /**
     * Descargar un documento
     */
    public function download($id)
    {
        $userId = Auth::id();
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz) { return back()->with('error', 'No se encontró el perfil de aprendiz'); }

        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId = $this->getAprendizId($aprendiz);
        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where($docAprCol, $aprId)
            ->first();

        if (!$documento) {
            return back()->with('error', 'Documento no encontrado');
        }

        // Si es un enlace externo, redirigir al URL
        if (filter_var($documento->ruta_archivo, FILTER_VALIDATE_URL)) {
            return redirect()->away($documento->ruta_archivo);
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
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz) { return back()->with('error', 'No se encontró el perfil de aprendiz'); }

        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId = $this->getAprendizId($aprendiz);
        $documento = DB::table('documentos')
            ->where('id_documento', $id)
            ->where($docAprCol, $aprId)
            ->first();

        if (!$documento) {
            return back()->with('error', 'Documento no encontrado');
        }

        // No permitir eliminar placeholders
        if (str_starts_with($documento->documento, 'PLACEHOLDER_')) {
            return back()->with('error', 'No se puede eliminar este registro');
        }

        // Eliminar archivo físico solo si es local (no URL)
        if (!filter_var($documento->ruta_archivo, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
        }

        // Eliminar registro de la base de datos
        DB::table('documentos')->where('id_documento', $id)->delete();

        return back()->with('success', 'Documento eliminado correctamente');
    }

    /**
     * Intentar obtener el aprendiz por el id del usuario autenticado
     * compatible con columnas id_usuario o user_id en la tabla aprendices.
     */
    private function getAprendizByUserId(int $userId)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_usuario')) {
            $ap = Aprendiz::where('id_usuario', $userId)->first();
            if ($ap) return $ap;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','user_id')) {
            $ap = Aprendiz::where('user_id', $userId)->first();
            if ($ap) return $ap;
        }
        return null;
    }

    // Obtener el ID del aprendiz (id_aprendiz o id_usuario/user_id) para usarlo en relaciones
    private function getAprendizId($aprendiz)
    {
        if (isset($aprendiz->id_aprendiz) && !empty($aprendiz->id_aprendiz)) { return $aprendiz->id_aprendiz; }
        if (isset($aprendiz->id_usuario) && !empty($aprendiz->id_usuario)) { return $aprendiz->id_usuario; }
        if (isset($aprendiz->user_id) && !empty($aprendiz->user_id)) { return $aprendiz->user_id; }
        return (int)($aprendiz->id ?? 0);
    }

    // Detectar la columna en 'documentos' que referencia al aprendiz
    private function getDocumentoAprendizColumn(): string
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz')) { return 'id_aprendiz'; }
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario')) { return 'id_usuario'; }
        return 'id_aprendiz';
    }
}
