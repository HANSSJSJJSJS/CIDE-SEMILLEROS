<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentosController extends Controller
{
    // Vista Documentación – lista proyectos con contadores
    public function documentos()
    {
        $userId = Auth::id();

        if (!Schema::hasTable('proyectos')) {
            return view('lider_semi.documentos', ['proyectos' => collect([])]);
        }

        $proyectos = DB::table('proyectos as p')
            ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
            ->where('s.id_lider_semi', $userId)
            ->select(
                'p.id_proyecto',
                DB::raw('COALESCE(p.nombre_proyecto, "Proyecto") as nombre'),
                DB::raw('COALESCE(p.descripcion, "") as descripcion'),
                DB::raw('COALESCE(p.estado, "ACTIVO") as estado')
            )
            ->get();

        if ($proyectos->isEmpty()) {
            $proyectos = DB::table('proyectos as p')
                ->select(
                    'p.id_proyecto',
                    DB::raw('COALESCE(p.nombre_proyecto, "Proyecto") as nombre'),
                    DB::raw('COALESCE(p.descripcion, "") as descripcion'),
                    DB::raw('COALESCE(p.estado, "ACTIVO") as estado')
                )
                ->get();
        }

        $proyectos->transform(function($proyecto){
            if (Schema::hasTable('documentos')) {
                $proyecto->entregas = DB::table('documentos')
                    ->where('id_proyecto', $proyecto->id_proyecto)
                    ->count();

                if (Schema::hasColumn('documentos', 'estado')) {
                    $proyecto->pendientes = DB::table('documentos')
                        ->where('id_proyecto', $proyecto->id_proyecto)
                        ->where('estado', 'pendiente')
                        ->count();
                    $proyecto->aprobadas = DB::table('documentos')
                        ->where('id_proyecto', $proyecto->id_proyecto)
                        ->where('estado', 'aprobado')
                        ->count();
                } else {
                    $proyecto->pendientes = $proyecto->entregas;
                    $proyecto->aprobadas = 0;
                }
            } else {
                $proyecto->entregas = 0;
                $proyecto->pendientes = 0;
                $proyecto->aprobadas = 0;
            }
            return $proyecto;
        });

        $proyectosActivos = $proyectos->filter(function($p){
            $estadoUpper = strtoupper($p->estado);
            $esEstadoActivo = in_array($estadoUpper, ['ACTIVO','EN_EJECUCION','EN EJECUCION','EJECUCION','EN_FORMULACION','EN FORMULACION','FORMULACION']);
            return ($p->pendientes ?? 0) > 0 || $esEstadoActivo;
        });
        $proyectosCompletados = $proyectos->filter(function($p){
            $estadoUpper = strtoupper($p->estado);
            $esEstadoCompletado = in_array($estadoUpper, ['COMPLETADO','FINALIZADO','TERMINADO','CERRADO']);
            return $esEstadoCompletado && (($p->pendientes ?? 0) === 0);
        });

        return view('lider_semi.documentos', compact('proyectosActivos','proyectosCompletados'));
    }

    /**
     * Permitir al líder abrir/ver el archivo asociado a una entrega.
     */
    public function verDocumento($id)
    {
        if (!Schema::hasTable('documentos')) {
            abort(404);
        }

        $doc = DB::table('documentos')->where('id_documento', $id)->first();
        if (!$doc) {
            abort(404);
        }

        // Si la ruta es una URL completa (Drive, etc.), redirigir
        if (!empty($doc->ruta_archivo) && filter_var($doc->ruta_archivo, FILTER_VALIDATE_URL)) {
            return redirect()->away($doc->ruta_archivo);
        }

        if (empty($doc->ruta_archivo)) {
            abort(404);
        }

        // Normalizar ruta (sin slash inicial ni prefijo public/)
        $ruta = ltrim($doc->ruta_archivo, '/');
        $rutaSinPublic = preg_replace('#^public/#', '', $ruta);

        // 1) Intentar servir desde disco 'public'
        if (Storage::disk('public')->exists($rutaSinPublic)) {
            return Storage::disk('public')->response($rutaSinPublic);
        }

        // 2) Intentar desde storage/app/ruta_archivo directa
        $full = storage_path('app/' . $ruta);
        if (is_file($full)) {
            return response()->file($full);
        }

        // 3) Intentar como storage/app/public/ruta_archivo (compat)
        $fullPublic = storage_path('app/public/' . $rutaSinPublic);
        if (is_file($fullPublic)) {
            return response()->file($fullPublic);
        }

        abort(404);
    }

    // Listar proyectos para el select del modal
    public function listarProyectos(Request $request)
    {
        if (!Schema::hasTable('proyectos') || !Schema::hasTable('semilleros')) {
            return response()->json(['proyectos' => []]);
        }
        if (!Schema::hasColumn('proyectos','id_semillero') || !Schema::hasColumn('semilleros','id_lider_semi')) {
            return response()->json(['proyectos' => []]);
        }

        $leaderId = (int) (Auth::id() ?? 0);

        $proyectos = DB::table('proyectos as p')
            ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
            ->where(function($w) use ($leaderId){
                $w->orWhere('s.id_lider_semi', $leaderId);
                if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                    $w->orWhere('s.id_lider_usuario', $leaderId);
                }
            })
            ->select('p.id_proyecto','p.nombre_proyecto')
            ->orderBy('p.nombre_proyecto')
            ->get();

        return response()->json(['proyectos' => $proyectos]);
    }

    // Obtener aprendices asignados a un proyecto
    public function obtenerAprendicesProyecto($proyectoId)
    {
        try {
            Log::info("Obteniendo aprendices para proyecto ID: {$proyectoId}");
            $leaderId = (int) (Auth::id() ?? 0);
            $pertenece = false;
            if (Schema::hasTable('proyectos')) {
                $chk = DB::table('proyectos as p');
                if (Schema::hasTable('semilleros') && Schema::hasColumn('proyectos','id_semillero')) {
                    $chk->join('semilleros as s','s.id_semillero','=','p.id_semillero');
                    $chk->where('p.id_proyecto', $proyectoId);
                    $chk->where(function($w) use ($leaderId){
                        if (Schema::hasColumn('semilleros','id_lider_usuario')) $w->orWhere('s.id_lider_usuario', $leaderId);
                        if (Schema::hasColumn('semilleros','id_lider_semi'))    $w->orWhere('s.id_lider_semi', $leaderId);
                    });
                    $pertenece = $chk->exists();
                } else {
                    $chk->where('p.id_proyecto', $proyectoId);
                    $chk->where(function($w) use ($leaderId){
                        if (Schema::hasColumn('proyectos','id_lider_usuario')) $w->orWhere('p.id_lider_usuario', $leaderId);
                        if (Schema::hasColumn('proyectos','id_lider_semi'))    $w->orWhere('p.id_lider_semi', $leaderId);
                    });
                    $pertenece = $chk->exists();
                }
            }
            if (!$pertenece) {
                Log::warning('Proyecto no pertenece explícitamente al líder, continuando de forma tolerante', ['proyectoId'=>$proyectoId,'leaderId'=>$leaderId]);
            }

            if (!Schema::hasTable('proyectos') || !Schema::hasTable('aprendices')) {
                Log::warning('Tablas proyectos o aprendices no existen');
                return response()->json(['aprendices' => [], 'data' => []]);
            }

            // 1) Reunir IDs de aprendices desde todas las pivotes posibles
            $aprIds = collect();
            if (Schema::hasTable('aprendiz_proyecto')) {
                $aprIds = $aprIds->merge(
                    DB::table('aprendiz_proyecto')
                        ->where('id_proyecto', $proyectoId)
                        ->pluck('id_aprendiz')
                );
            }
            if (Schema::hasTable('proyecto_aprendiz')) {
                $aprIds = $aprIds->merge(
                    DB::table('proyecto_aprendiz')
                        ->where('id_proyecto', $proyectoId)
                        ->pluck('id_aprendiz')
                );
            }
            if (Schema::hasTable('proyecto_user')) {
                // Mapear user_id -> id_aprendiz
                $userIds = DB::table('proyecto_user')
                    ->where('id_proyecto', $proyectoId)
                    ->pluck('user_id');
                if ($userIds->isNotEmpty()) {
                    $aprUserFkCol = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario'
                                   : (Schema::hasColumn('aprendices','user_id') ? 'user_id' : null);
                    if ($aprUserFkCol) {
                        $aprIds = $aprIds->merge(
                            DB::table('aprendices')->whereIn($aprUserFkCol, $userIds)->pluck('id_aprendiz')
                        );
                    }
                }
            }

            $aprIds = $aprIds->filter()->unique()->values();
            $aprendices = collect();
            if ($aprIds->isNotEmpty()) {
                $aprendices = DB::table('aprendices')
                    ->whereIn('id_aprendiz', $aprIds)
                    ->select(
                        DB::raw('id_aprendiz as id_aprendiz'),
                        DB::raw("CONCAT(COALESCE(nombres,''),' ',COALESCE(apellidos,'')) as nombre_completo")
                    )
                    ->orderBy('nombre_completo')
                    ->get();
            }

            // Fallback: si no se encontró nadie por pivote, listar aprendices activos del semillero del proyecto
            if (($aprendices ?? collect())->isEmpty()) {
                $semilleroId = null;
                if (Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero')) {
                    $semilleroId = DB::table('proyectos')->where('id_proyecto', $proyectoId)->value('id_semillero');
                }
                if ($semilleroId) {
                    $aprendices = DB::table('aprendices')
                        ->where('semillero_id', $semilleroId)
                        ->when(Schema::hasColumn('aprendices','estado'), function($q){ $q->where('estado', 'Activo'); })
                        ->select(
                            DB::raw('id_aprendiz as id_aprendiz'),
                            DB::raw("CONCAT(COALESCE(nombres,''),' ',COALESCE(apellidos,'')) as nombre_completo")
                        )
                        ->orderBy('nombre_completo')
                        ->get();
                }
            }

            return response()->json(['aprendices' => $aprendices, 'data' => $aprendices]);

        } catch (\Exception $e) {
            Log::error('Error al obtener aprendices del proyecto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['aprendices' => [], 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    // Guardar evidencia de avance (crea un registro en documentos)
    public function guardarEvidencia(Request $request)
    {
        try {
            $request->validate([
                'proyecto_id' => 'required|integer|exists:proyectos,id_proyecto',
                'aprendiz_id' => 'required|integer|exists:aprendices,id_aprendiz',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'tipo_evidencia' => 'required|string',
                'fecha' => 'required|date|after_or_equal:today'
            ], [
                'fecha.after_or_equal' => 'La fecha del avance no puede ser anterior a hoy. Por favor selecciona una fecha válida.'
            ]);

            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            $columns = DB::select("SHOW COLUMNS FROM documentos WHERE Field = 'id_aprendiz'");

            // Mapear tipo_evidencia (front) a enum tipo_archivo (BD)
            $tipoEvidencia = strtolower($request->tipo_evidencia);
            switch ($tipoEvidencia) {
                case 'pdf':
                    $tipoArchivoEnum = 'PDF';
                    break;
                case 'documento': // Documento Word en el formulario
                    $tipoArchivoEnum = 'WORD';
                    break;
                case 'presentacion':
                    $tipoArchivoEnum = 'PRESENTACION';
                    break;
                case 'video':
                    $tipoArchivoEnum = 'VIDEO';
                    break;
                case 'imagen':
                    $tipoArchivoEnum = 'IMAGEN';
                    break;
                case 'enlace':
                    $tipoArchivoEnum = 'ENLACE';
                    break;
                case 'otro':
                default:
                    $tipoArchivoEnum = 'OTRO';
                    break;
            }

            $dataToInsert = [
                'id_proyecto' => $request->proyecto_id,
                'documento' => $request->titulo,
            ];

            if (Schema::hasColumn('documentos', 'ruta_archivo')) {
                $dataToInsert['ruta_archivo'] = '';
            }
            if (Schema::hasColumn('documentos', 'tipo_archivo')) {
                $dataToInsert['tipo_archivo'] = $tipoArchivoEnum;
            }
            if (Schema::hasColumn('documentos', 'tamanio')) {
                $dataToInsert['tamanio'] = 0;
            }
            if (Schema::hasColumn('documentos', 'mime_type')) {
                $dataToInsert['mime_type'] = '';
            }
            if (Schema::hasColumn('documentos', 'fecha_subida')) {
                $dataToInsert['fecha_subida'] = now();
            }
            if (Schema::hasColumn('documentos', 'fecha_limite')) {
                $dataToInsert['fecha_limite'] = $request->fecha;
            }
            if (Schema::hasColumn('documentos', 'estado')) {
                $dataToInsert['estado'] = 'pendiente';
            }
            // Inicializar marca de rechazo si existe la columna
            if (Schema::hasColumn('documentos', 'rechazado_en')) {
                $dataToInsert['rechazado_en'] = null;
            }
            if (Schema::hasColumn('documentos', 'tipo_documento')) {
                $dataToInsert['tipo_documento'] = $request->tipo_evidencia;
            }
            if (Schema::hasColumn('documentos', 'descripcion')) {
                $dataToInsert['descripcion'] = $request->descripcion;
            }

            // Siempre debe venir un aprendiz asignado (opción A)
            $dataToInsert['id_aprendiz'] = $request->aprendiz_id;
            if (Schema::hasColumn('documentos', 'id_usuario')) {
                $usrId = null;
                if (Schema::hasColumn('aprendices', 'id_usuario')) {
                    $usrId = DB::table('aprendices')->where('id_aprendiz', $request->aprendiz_id)->value('id_usuario');
                } elseif (Schema::hasColumn('aprendices', 'user_id')) {
                    $usrId = DB::table('aprendices')->where('id_aprendiz', $request->aprendiz_id)->value('user_id');
                }
                if (!is_null($usrId)) {
                    $dataToInsert['id_usuario'] = (int)$usrId;
                }
            }

            $nextId = null;
            if (Schema::hasColumn('documentos', 'id_documento')) {
                try {
                    $max = DB::table('documentos')->max('id_documento');
                    $nextId = (int) ($max ?? 0) + 1;
                    $dataToInsert['id_documento'] = $nextId;
                } catch (\Throwable $ex) {
                    // continuar sin id manual
                }
            }

            DB::table('documentos')->insert($dataToInsert);
            $documentoId = $nextId ?? 0;

            return response()->json([
                'success' => true,
                'message' => 'Evidencia registrada exitosamente.',
                'documento_id' => $documentoId
            ]);

        } catch (\Exception $e) {
            Log::error('Error al guardar evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener entregas de un proyecto
    public function obtenerEntregas($proyectoId)
    {
        try {
            if (!Schema::hasTable('documentos')) {
                return response()->json(['entregas' => []]);
            }

            // Consulta directa para obtener entregas del proyecto
            $entregas = DB::table('documentos as d')
                ->leftJoin('aprendices as a', 'a.id_aprendiz', '=', 'd.id_aprendiz')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->where('d.id_proyecto', $proyectoId)
                ->select(
                    'd.id_documento as id',
                    'd.documento as titulo',
                    'd.ruta_archivo',
                    'd.tipo_archivo',
                    'd.tamanio',
                    DB::raw("COALESCE(d.fecha_subido, d.fecha_subida) as fecha"),
                    DB::raw("COALESCE(d.estado, 'pendiente') as estado"),
                    DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.apellidos,''))) as nombre_aprendiz"),
                    'd.ruta_archivo as archivo_url',
                    'd.documento as archivo_nombre',
                    DB::raw("NULL as descripcion"),
                    DB::raw("NULL as rechazado_en")
                )
                ->orderBy('d.fecha_subido', 'desc')
                ->get();

            $ahora = new \DateTime();
            $entregas = $entregas->map(function($entrega) use ($ahora) {
                $tieneArchivo = !empty($entrega->ruta_archivo);
                if ($tieneArchivo) {
                    if (filter_var($entrega->ruta_archivo, FILTER_VALIDATE_URL)) {
                        // URL externa (Drive, etc.)
                        $entrega->archivo_url = $entrega->ruta_archivo;
                    } else {
                        // Ruta local: usar la ruta protegida del líder semillero
                        $entrega->archivo_url = route('lider_semi.documentos.ver', ['id' => $entrega->id]);
                    }
                }

                // Solo mostrar "Actualizada por el aprendiz" para RE-ENVÍOS:
                // Debe tener archivo, estar en pendiente, haber sido rechazado antes (rechazado_en no null)
                // y la fecha reciente <= 24h
                $entrega->recien_actualizada = false;
                if ($tieneArchivo && isset($entrega->estado) && $entrega->estado === 'pendiente' && !empty($entrega->rechazado_en) && $entrega->fecha) {
                    try {
                        $fecha = new \DateTime($entrega->fecha);
                        $entrega->fecha = $fecha->format('Y-m-d');
                        $diffSegundos = $ahora->getTimestamp() - $fecha->getTimestamp();
                        if ($diffSegundos >= 0 && $diffSegundos <= 86400) {
                            $entrega->recien_actualizada = true;
                        }
                    } catch (\Exception $e) { }
                }
                return $entrega;
            });

            return response()->json(['entregas' => $entregas]);

        } catch (\Exception $e) {
            return response()->json([
                'entregas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    // Cambiar estado de una entrega
    public function cambiarEstadoEntrega(Request $request, $entregaId)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,aprobado,rechazado',
                'motivo' => 'nullable|string'
            ]);

            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            $documento = DB::table('documentos')
                ->where('id_documento', $entregaId)
                ->first();

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            if (!Schema::hasColumn('documentos', 'estado')) {
                Schema::table('documentos', function($table) {
                    $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('fecha_subida');
                });
            }

            // Asegurar columna 'rechazado_en' para marcar último rechazo
            if (!Schema::hasColumn('documentos', 'rechazado_en')) {
                Schema::table('documentos', function($table) {
                    $table->dateTime('rechazado_en')->nullable()->after('estado');
                });
            }

            // Asegurar columna descripcion si vamos a guardar motivos de rechazo
            if (!Schema::hasColumn('documentos', 'descripcion')) {
                Schema::table('documentos', function($table) {
                    $table->text('descripcion')->nullable()->after('estado');
                });
            }

            $updateData = [
                'estado' => $request->estado
            ];

            // Si el líder envía un motivo y el estado es rechazado, guardarlo en descripcion
            if ($request->estado === 'rechazado' && $request->filled('motivo')) {
                $updateData['descripcion'] = $request->motivo;
            }

            // Gestionar marca de rechazo según nuevo estado
            if (Schema::hasColumn('documentos', 'rechazado_en')) {
                if ($request->estado === 'rechazado') {
                    $updateData['rechazado_en'] = now();
                } elseif ($request->estado === 'aprobado') {
                    $updateData['rechazado_en'] = null; // limpiar al aprobar
                }
            }

            DB::table('documentos')
                ->where('id_documento', $entregaId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'proyecto_id' => $documento->id_proyecto
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    // Actualizar documento completo
    public function actualizarDocumento(Request $request, $documentoId)
    {
        try {
            $request->validate([
                'tipo_documento' => 'required|string',
                'fecha_limite' => 'required|date',
                'descripcion' => 'nullable|string'
            ]);

            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            $documento = DB::table('documentos')
                ->where('id_documento', $documentoId)
                ->first();

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            if (!Schema::hasColumn('documentos', 'tipo_documento')) {
                Schema::table('documentos', function($table) {
                    $table->string('tipo_documento', 50)->nullable()->after('tipo_archivo');
                });
            }
            if (!Schema::hasColumn('documentos', 'fecha_limite')) {
                Schema::table('documentos', function($table) {
                    $table->date('fecha_limite')->nullable()->after('fecha_subida');
                });
            }
            if (!Schema::hasColumn('documentos', 'descripcion')) {
                Schema::table('documentos', function($table) {
                    $table->text('descripcion')->nullable()->after('estado');
                });
            }

            $updateData = [
                'tipo_documento' => $request->tipo_documento,
                'fecha_limite' => $request->fecha_limite
            ];
            if ($request->has('descripcion')) {
                $updateData['descripcion'] = $request->descripcion;
            }

            DB::table('documentos')
                ->where('id_documento', $documentoId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Documento actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper: detectar la tabla pivote proyecto-aprendiz
    private function pivotProyectoAprendiz(): array
    {
        $pivotCandidates = [
            'aprendiz_proyecto', 'aprendices_proyectos', 'aprendiz_proyectos', 'proyecto_aprendiz', 'proyectos_aprendices', 'proyecto_aprendices',
            'proyecto_user'
        ];
        $table = null;
        foreach ($pivotCandidates as $cand) {
            if (Schema::hasTable($cand)) { $table = $cand; break; }
        }

        if ($table) {
            $projCols = ['id_proyecto','proyecto_id','idProyecto'];
            $aprCols  = ['id_aprendiz','aprendiz_id','idAprendiz','id_usuario','user_id'];
            $pivotProjCol = null; $pivotAprCol = null;
            foreach ($projCols as $c) { if (Schema::hasColumn($table, $c)) { $pivotProjCol = $c; break; } }
            foreach ($aprCols as $c) { if (Schema::hasColumn($table, $c)) { $pivotAprCol = $c; break; } }
            if ($pivotProjCol && $pivotAprCol) {
                return ['table'=>$table,'projCol'=>$pivotProjCol,'aprCol'=>$pivotAprCol];
            }
        }

        if (Schema::hasTable('documentos') &&
            Schema::hasColumn('documentos','id_proyecto') &&
            Schema::hasColumn('documentos','id_aprendiz')) {
            return ['table'=>'documentos','projCol'=>'id_proyecto','aprCol'=>'id_aprendiz'];
        }

        return [];
    }
}

