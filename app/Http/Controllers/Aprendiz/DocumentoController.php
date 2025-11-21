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
        // 3) Si no hay pivote, intentar deducir por documentos (como antes),
        //    pero siendo tolerantes con id_aprendiz / id_usuario
        if (empty($proyectoIds) && \Illuminate\Support\Facades\Schema::hasTable('documentos')) {
            $aprId = $this->getAprendizId($aprendiz);
            $userIdLocal = $userId;

            $proyectoIds = DB::table('documentos')
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz') || \Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario'),
                    function($q) use ($aprId, $userIdLocal) {
                        $q->where(function($sub) use ($aprId, $userIdLocal) {
                            if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz')) {
                                $sub->orWhere('id_aprendiz', $aprId);
                            }
                            if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario')) {
                                $sub->orWhere('id_usuario', $userIdLocal);
                            }
                        });
                    })
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
        $limiteAprobadas = now()->subDays(2);

        $userIdLocal = $userId;

        $documentos = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where(function($q) use ($docAprCol, $aprId, $userIdLocal) {
                // Condición principal por columna detectada
                $q->where('documentos.' . $docAprCol, $aprId);
                // Tolerancia: si existe id_usuario, también aceptar coincidencia con el user_id
                if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario')) {
                    $q->orWhere('documentos.id_usuario', $userIdLocal);
                }
            })
            ->whereRaw("documentos.documento NOT LIKE 'PLACEHOLDER%'")
            ->whereNotNull('documentos.ruta_archivo')
            ->where('documentos.ruta_archivo', '!=', '')
            // Si existe columna estado, ocultar evidencias aprobadas con más de 2 días
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('documentos','estado'), function($q) use ($limiteAprobadas) {
                $q->where(function($inner) use ($limiteAprobadas) {
                    $inner->whereIn('documentos.estado', ['pendiente','rechazado'])
                          ->orWhere(function($sub) use ($limiteAprobadas) {
                              $sub->where('documentos.estado', 'aprobado')
                                  ->where('documentos.fecha_subida', '>=', $limiteAprobadas);
                          });
                });
            })
            ->select(
                'documentos.*',
                'proyectos.nombre_proyecto'
            )
            ->orderBy('documentos.fecha_subida', 'desc')
            ->get();

        // Evidencias pendientes: cualquier documento creado para este aprendiz (o su usuario)
        // que aún no tenga archivo y esté en estado pendiente (o sin estado definido).
        $pendientesQuery = DB::table('documentos')
            ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
            ->where(function($q) use ($docAprCol, $aprId, $userIdLocal) {
                $q->where('documentos.' . $docAprCol, $aprId);
                if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario')) {
                    $q->orWhere('documentos.id_usuario', $userIdLocal);
                }
            })
            // Solo evidencias reales, ignorar registros placeholder
            ->whereRaw("documentos.documento NOT LIKE 'PLACEHOLDER%'")
            // Pendiente = sin archivo
            ->where(function($q){
                $q->whereNull('documentos.ruta_archivo')
                  ->orWhere('documentos.ruta_archivo', '=', '');
            })
            // Estado pendiente o nulo (para ser tolerantes con esquemas sin estado)
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('documentos','estado'), function($q){
                $q->where(function($inner){
                    $inner->whereNull('documentos.estado')
                          ->orWhere('documentos.estado', 'pendiente');
                });
            });

        $pendientesAsignadas = $pendientesQuery
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
        $userId   = Auth::id();
        $aprendiz = $this->getAprendizByUserId($userId);
        if (!$aprendiz) {
            return back()->with('error', 'No se encontró el perfil de aprendiz');
        }

        // Validación básica del formulario principal
        $request->validate([
            'id_proyecto' => 'required|exists:proyectos,id_proyecto',
            'archivo'     => 'required|file|max:10240',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Verificar que el aprendiz está asignado al proyecto (igual que antes)
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
            return back()->with('error', 'No estás asignado a este proyecto');
        }

        // Nueva regla: solo permitir subir si existe al menos UNA evidencia asignada pendiente
        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId     = $this->getAprendizId($aprendiz);

        $hayPendientes = false;
        if (\Illuminate\Support\Facades\Schema::hasTable('documentos')) {
            $qPend = DB::table('documentos')
                ->where('id_proyecto', $request->id_proyecto)
                ->where($docAprCol, $aprId)
                ->whereRaw("documento NOT LIKE 'PLACEHOLDER%'")
                ->where(function($q){
                    $q->whereNull('ruta_archivo')
                      ->orWhere('ruta_archivo', '');
                });

            if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','estado')) {
                $qPend->where('estado', 'pendiente');
            }

            $hayPendientes = $qPend->exists();
        }

        if (!$hayPendientes) {
            return back()->with('error', 'En este momento no tienes evidencias asignadas pendientes para este proyecto.');
        }

        // ===============================
        // Datos comunes del archivo
        // ===============================
        $archivo        = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $extension      = $archivo->getClientOriginalExtension();
        $tamanio        = $archivo->getSize();
        $tipoArchivo    = strtolower($extension ?? '');

        $nombreArchivo = time() . '_' . $aprendiz->id_aprendiz . '_' . $nombreOriginal;
        $ruta          = $archivo->storeAs('documentos', $nombreArchivo, 'public');

        $docAprCol = $this->getDocumentoAprendizColumn();
        $aprId     = $this->getAprendizId($aprendiz);

        // ===============================
        // 1) Intentar completar evidencia pendiente de este proyecto
        // ===============================
        $evidenciaPendiente = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('documentos')) {
            $qPend = DB::table('documentos')
                ->where('id_proyecto', $request->id_proyecto)
                ->where($docAprCol, $aprId)
                ->where(function($q){
                    $q->whereNull('ruta_archivo')
                      ->orWhere('ruta_archivo', '');
                });

            if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','fecha_limite')) {
                $qPend->orderBy('fecha_limite', 'asc');
            } else {
                $qPend->orderBy('id_documento', 'asc');
            }

            $evidenciaPendiente = $qPend->first();
        }

        // Si hay evidencia pendiente, ACTUALIZAMOS ese registro
        if ($evidenciaPendiente) {
            $update = [
                'ruta_archivo' => $ruta,
                'tamanio'      => $tamanio,
                'fecha_subida' => now(),
            ];

            $tipoSeguro = $this->getTipoArchivoSafe($tipoArchivo);
            if (!is_null($tipoSeguro) && \Illuminate\Support\Facades\Schema::hasColumn('documentos','tipo_archivo')) {
                $update['tipo_archivo'] = $tipoSeguro;
            }

            if ($request->filled('descripcion')) {
                $update['documento'] = $request->descripcion;
            }

            DB::table('documentos')
                ->where('id_documento', $evidenciaPendiente->id_documento)
                ->update($update);

            if ($request->ajax() || $request->wantsJson()) {
                // Indicamos al frontend que debe recargar para refrescar alertas y listas de pendientes
                return response()->json(['ok' => true, 'reload' => true]);
            }

            return back()->with('success', 'Evidencia asignada actualizada correctamente.');
        }

        // ===============================
        // 2) Si no hay pendientes, crear un nuevo documento (lógica original)
        // ===============================
        $nextId = null;
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_documento')) {
            $max    = DB::table('documentos')->max('id_documento');
            $nextId = (int)($max ?? 0) + 1;
        }

        $dataInsert = [
            'id_proyecto'  => $request->id_proyecto,
            $docAprCol     => $aprId,
            'documento'    => $request->descripcion ?: $nombreOriginal,
            'ruta_archivo' => $ruta,
            'tamanio'      => $tamanio,
            'fecha_subida' => now(),
        ];

        // Toda nueva evidencia subida por el aprendiz debe comenzar como "pendiente"
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','estado')) {
            $dataInsert['estado'] = 'pendiente';
        }

        $tipoSeguro = $this->getTipoArchivoSafe($tipoArchivo);
        if (!is_null($tipoSeguro)) {
            $dataInsert['tipo_archivo'] = $tipoSeguro;
        }

        if (!is_null($nextId)) {
            $dataInsert['id_documento'] = $nextId;
            DB::table('documentos')->insert($dataInsert);
            $idDocumento = $nextId;
        } else {
            try {
                $idDocumento = DB::table('documentos')->insertGetId($dataInsert, 'id_documento');
            } catch (\Throwable $e) {
                DB::table('documentos')->insert($dataInsert);
                $idDocumento = (int) (DB::table('documentos')
                    ->where('ruta_archivo', $ruta)
                    ->where($docAprCol, $aprId)
                    ->max('id_documento') ?? 0);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            $doc = DB::table('documentos')
                ->join('proyectos', 'proyectos.id_proyecto', '=', 'documentos.id_proyecto')
                ->where('id_documento', $idDocumento)
                ->select('documentos.*', 'proyectos.nombre_proyecto')
                ->first();

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
                    'id'           => $doc->id_documento,
                    'proyecto'     => $doc->nombre_proyecto,
                    'documento'    => $doc->documento,
                    'tipo'         => pathinfo($doc->ruta_archivo, PATHINFO_EXTENSION),
                    'tamanio_kb'   => round(($doc->tamanio ?? 0) / 1024, 2),
                    'fecha'        => $fecha,
                    'download_url' => route('aprendiz.documentos.download', $doc->id_documento),
                    'delete_url'   => route('aprendiz.documentos.destroy', $doc->id_documento),
                ],
            ]);
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

        // Si la evidencia ya fue aprobada por el líder, no permitir que el aprendiz la modifique
        if (property_exists($documento, 'estado')) {
            $estadoActual = strtolower((string)($documento->estado ?? ''));
            if ($estadoActual === 'aprobado') {
                return back()->with('error', 'Esta evidencia ya fue aprobada y no puede ser modificada.');
            }
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

        // Actualizar descripción/título solo si cambia
        if ($request->has('descripcion')) {
            $nuevaDescripcion = (string)$request->input('descripcion');
            $descripcionActual = (string)($documento->documento ?? '');
            if ($nuevaDescripcion !== $descripcionActual) {
                $dataUpdate['documento'] = $nuevaDescripcion;
            }
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

        if (empty($dataUpdate)) {
            return back()->with('error', 'Debes seleccionar un nuevo archivo o cambiar la descripción para actualizar la evidencia.');
        }

        DB::table('documentos')
            ->where('id_documento', $id)
            ->update($dataUpdate);

        return back()->with('success', 'Entrega actualizada correctamente.');
    }


    public function uploadAssigned(Request $request, $id)
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
        if (!$documento) { return back()->with('error', 'Documento no encontrado'); }

        // Determinar el tipo definido por el líder (base textual)
        $tipoBase = strtolower(trim((string)($documento->tipo_documento ?? $documento->tipo_archivo ?? '')));

        // Normalizar algunos alias
        if (in_array($tipoBase, ['doc', 'docx', 'word', 'documento'], true)) {
            $tipoBase = 'word';
        } elseif (in_array($tipoBase, ['ppt', 'pptx', 'presentacion', 'presentación'], true)) {
            $tipoBase = 'presentacion';
        } elseif (in_array($tipoBase, ['img', 'imagen', 'image'], true)) {
            $tipoBase = 'imagen';
        } elseif (in_array($tipoBase, ['link', 'enlace', 'url'], true)) {
            $tipoBase = 'enlace';
        }

        // Reglas de validación según el tipo asignado por el líder
        if ($tipoBase === 'enlace') {
            // Debe ser un link, no se acepta archivo físico
            if ($request->hasFile('archivo')) {
                return back()->with('error', 'Para esta evidencia solo se permite enviar un enlace, no un archivo.');
            }
            $request->validate([
                'link_url' => 'required|url',
            ]);
        } else {
            // Debe ser un archivo; no permitimos solo link_url
            if (!$request->hasFile('archivo')) {
                return back()->with('error', 'Debes seleccionar un archivo del tipo asignado para esta evidencia.');
            }

            // Validar tamaño máximo
            $request->validate([
                'archivo' => 'required|file|max:10240',
            ]);

            // Verificar extensión explícitamente según el tipo definido por el líder
            $archivo = $request->file('archivo');
            $extension = strtolower((string)$archivo->getClientOriginalExtension());

            $extPermitidas = [];
            switch ($tipoBase) {
                case 'pdf':
                    $extPermitidas = ['pdf'];
                    break;
                case 'word':
                    $extPermitidas = ['doc', 'docx'];
                    break;
                case 'presentacion':
                    $extPermitidas = ['ppt', 'pptx'];
                    break;
                case 'imagen':
                    $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
                    break;
                case 'video':
                    $extPermitidas = ['mp4', 'avi', 'mov', 'mkv'];
                    break;
                default:
                    // Tipo genérico u "otro": permitir cualquier archivo (solo se controla tamaño)
                    $extPermitidas = [];
                    break;
            }

            if (!empty($extPermitidas) && !in_array($extension, $extPermitidas, true)) {
                return back()->with('error', 'El tipo de archivo seleccionado no coincide con el tipo asignado para esta evidencia.');
            }
        }

        // Construir datos a actualizar según tipo
        if ($tipoBase !== 'enlace' && $request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $tamanio = $archivo->getSize();
            $tipoArchivo = strtolower($extension ?? '');
            $nombreArchivo = time() . '_' . $aprId . '_' . $nombreOriginal;
            $ruta = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('documentos', $archivo, $nombreArchivo);
            $update = [
                'ruta_archivo' => $ruta,
                'tamanio' => $tamanio,
                'fecha_subida' => now(),
            ];
            $tipoSeguro = $this->getTipoArchivoSafe($tipoArchivo);
            if (!is_null($tipoSeguro)) { $update['tipo_archivo'] = $tipoSeguro; }
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

    // Normaliza el valor de tipo_archivo según el esquema real (ENUM o VARCHAR con longitud) para evitar truncamientos
    private function getTipoArchivoSafe(?string $ext): ?string
    {
        $ext = strtolower((string)($ext ?? ''));
        if ($ext === '') { return null; }

        try {
            $db = \DB::getDatabaseName();
            $col = \DB::table('information_schema.columns')
                ->where('table_schema', $db)
                ->where('table_name', 'documentos')
                ->where('column_name', 'tipo_archivo')
                ->select('data_type', 'column_type', 'character_maximum_length')
                ->first();
            if (!$col) { return $ext; }

            // Si es ENUM, intentar mapear a un valor permitido
            if (isset($col->data_type) && strtolower($col->data_type) === 'enum' && !empty($col->column_type)) {
                // column_type: enum('PDF','DOC','XLS')
                $m = [];
                if (preg_match("/enum\\((.*)\\)/i", $col->column_type, $m)) {
                    $vals = array_map(function($v){ return trim($v, "'\" "); }, explode(',', $m[1]));
                    // Intentar coincidencia exacta (case-insensitive)
                    foreach ($vals as $v) {
                        if (strcasecmp($v, $ext) === 0) { return $v; }
                    }
                    // Mapear extensiones comunes a valores típicos
                    $map = [
                        'docx' => 'DOC', 'doc' => 'DOC',
                        'xlsx' => 'XLS', 'xls' => 'XLS',
                        'pptx' => 'PPT', 'ppt' => 'PPT',
                        'jpeg' => 'JPG', 'jpg' => 'JPG',
                        'png'  => 'PNG', 'pdf' => 'PDF',
                        'zip'  => 'ZIP', 'rar' => 'RAR',
                        'txt'  => 'TXT', 'csv' => 'CSV',
                    ];
                    if (isset($map[$ext])) {
                        // Usar la variante que exista en el ENUM (case-insensitive)
                        foreach ($vals as $v) {
                            if (strcasecmp($v, $map[$ext]) === 0) { return $v; }
                        }
                    }
                    // Si no hay mapeo válido, no escribir tipo_archivo
                    return null;
                }
            }

            // Si es VARCHAR/CHAR, respetar longitud máxima
            if (isset($col->character_maximum_length) && $col->character_maximum_length) {
                $len = (int)$col->character_maximum_length;
                if ($len > 0) {
                    // Convención: usar mayúsculas si longitud es muy corta (3-4)
                    $val = strlen($ext) > $len ? substr($ext, 0, $len) : $ext;
                    if ($len <= 4) { $val = strtoupper($val); }
                    return $val;
                }
            }

            return $ext;
        } catch (\Throwable $e) {
            // Ante cualquier problema, devolver la extensión original para no romper el flujo
            return $ext;
        }
    }
}
