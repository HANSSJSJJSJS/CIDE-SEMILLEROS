<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->where('s.id_lider_semi', $leaderId)
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
                return response()->json(['aprendices' => [], 'data' => []]);
            }

            if (!Schema::hasTable('proyectos') || !Schema::hasTable('aprendices')) {
                Log::warning('Tablas proyectos o aprendices no existen');
                return response()->json(['aprendices' => [], 'data' => []]);
            }

            $pivot = $this->pivotProyectoAprendiz();
            if (empty($pivot)) {
                Log::warning('No se encontró tabla pivot para proyecto-aprendiz');
                return response()->json(['aprendices' => [], 'data' => []]);
            }

            $pivotUsaUsuario = in_array($pivot['aprCol'], ['id_usuario', 'user_id'], true);
            $aprPkCol = null;
            if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCol = 'id_aprendiz'; }
            elseif (Schema::hasColumn('aprendices','id')) { $aprPkCol = 'id'; }
            elseif (Schema::hasColumn('aprendices','id_usuario')) { $aprPkCol = 'id_usuario'; }

            $aprUserFkCol = null;
            if (Schema::hasColumn('aprendices','id_usuario')) { $aprUserFkCol = 'id_usuario'; }
            elseif (Schema::hasColumn('aprendices','user_id')) { $aprUserFkCol = 'user_id'; }

            if ($pivotUsaUsuario && $aprUserFkCol) {
                $aprendices = DB::table($pivot['table'])
                    ->join('aprendices', 'aprendices.'.$aprUserFkCol, '=', DB::raw($pivot['table'].'.'.$pivot['aprCol']))
                    ->where(DB::raw($pivot['table'].'.'.$pivot['projCol']), $proyectoId)
                    ->where('aprendices.documento', '!=', 'SIN_ASIGNAR')
                    ->distinct()
                    ->select(
                        DB::raw('aprendices.' . ($aprPkCol ?? 'id_usuario') . ' as id_aprendiz'),
                        DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo")
                    )
                    ->orderBy('nombre_completo')
                    ->get();
            } else {
                $aprendizIds = DB::table($pivot['table'])
                    ->where($pivot['projCol'], $proyectoId)
                    ->whereNotNull($pivot['aprCol'])
                    ->where($pivot['aprCol'], '>', 0)
                    ->distinct()
                    ->pluck($pivot['aprCol']);

                if ($aprendizIds->isEmpty()) {
                    return response()->json(['aprendices' => [], 'data' => []]);
                }

                $aprendices = DB::table('aprendices')
                    ->when($aprPkCol !== null, function($q) use ($aprPkCol, $aprendizIds) {
                        return $q->whereIn($aprPkCol, $aprendizIds);
                    }, function($q) use ($aprendizIds) {
                        return $q->whereIn('id_usuario', $aprendizIds);
                    })
                    ->where('documento', '!=', 'SIN_ASIGNAR')
                    ->select(
                        DB::raw(($aprPkCol ?? 'id_usuario') . ' as id_aprendiz'),
                        DB::raw("CONCAT(COALESCE(nombres,''),' ',COALESCE(apellidos,'')) as nombre_completo")
                    )
                    ->orderBy('nombre_completo')
                    ->get();
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
                'aprendiz_id' => 'nullable|integer|exists:aprendices,id_aprendiz',
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
            $allowsNull = !empty($columns) && $columns[0]->Null === 'YES';

<<<<<<< HEAD
            $dataToInsert = [
                'id_proyecto' => $request->proyecto_id,
                'documento' => $request->titulo,
                'ruta_archivo' => '',
                'tipo_archivo' => $request->tipo_evidencia,
                'tamanio' => 0,
                'mime_type' => '',
                'fecha_subida' => now(),
                'fecha_limite' => $request->fecha,
                'estado' => 'pendiente',
                'tipo_documento' => $request->tipo_evidencia,
                'descripcion' => $request->descripcion
            ];

=======
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
            if (Schema::hasColumn('documentos', 'tipo_documento')) {
                $dataToInsert['tipo_documento'] = $request->tipo_evidencia;
            }
            if (Schema::hasColumn('documentos', 'descripcion')) {
                $dataToInsert['descripcion'] = $request->descripcion;
            }

>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
            if ($request->has('aprendiz_id') && $request->aprendiz_id) {
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
            } elseif ($allowsNull) {
                $dataToInsert['id_aprendiz'] = null;
            } else {
                $aprendizSinAsignar = DB::table('aprendices')
                    ->where('documento', '=', 'SIN_ASIGNAR')
                    ->first();

                if (!$aprendizSinAsignar) {
                    $idAprendizSinAsignar = DB::table('aprendices')->insertGetId([
                        'nombres' => 'Sin',
                        'apellidos' => 'Asignar',
                        'nombre_completo' => 'Sin Asignar',
                        'tipo_documento' => 'CC',
                        'documento' => 'SIN_ASIGNAR',
                        'celular' => '0000000000',
                        'correo_institucional' => 'sin.asignar@sena.edu.co',
                        'correo_personal' => 'sin.asignar@sena.edu.co',
                        'programa' => 'N/A',
                        'ficha' => '0000000',
                        'contacto_nombre' => 'N/A',
                        'contacto_celular' => '0000000000'
                    ]);
                    $dataToInsert['id_aprendiz'] = $idAprendizSinAsignar;
                } else {
                    $dataToInsert['id_aprendiz'] = $aprendizSinAsignar->id_aprendiz;
                }

                if (Schema::hasColumn('documentos', 'id_usuario')) {
                    $usrId = null;
                    if (Schema::hasColumn('aprendices', 'id_usuario')) {
                        $usrId = DB::table('aprendices')->where('id_aprendiz', $dataToInsert['id_aprendiz'])->value('id_usuario');
                    } elseif (Schema::hasColumn('aprendices', 'user_id')) {
                        $usrId = DB::table('aprendices')->where('id_aprendiz', $dataToInsert['id_aprendiz'])->value('user_id');
                    }
                    if (!is_null($usrId)) {
                        $dataToInsert['id_usuario'] = (int)$usrId;
                    }
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

            $hasDescripcion = Schema::hasColumn('documentos', 'descripcion');

            $selectFields = [
                'd.id_documento as id',
                'd.documento as titulo',
                'd.ruta_archivo',
                'd.tipo_archivo',
                'd.tamanio',
                'd.fecha_subido as fecha',
                DB::raw("COALESCE(d.estado, 'pendiente') as estado"),
<<<<<<< HEAD
                DB::raw("COALESCE(a.nombre_completo, 'Sin asignar') as nombre_aprendiz"),
=======
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(a.nombres,''),' ',COALESCE(a.apellidos,''))), ''), 'Sin Asignar') as nombre_aprendiz"),
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
                'd.ruta_archivo as archivo_url',
                'd.documento as archivo_nombre'
            ];

            if ($hasDescripcion) {
                $selectFields[] = DB::raw("COALESCE(d.descripcion, '') as descripcion");
            } else {
                $selectFields[] = DB::raw("'' as descripcion");
            }

            $entregas = DB::table('documentos as d')
                ->leftJoin('aprendices as a', 'd.id_aprendiz', '=', 'a.id_aprendiz')
                ->where('d.id_proyecto', $proyectoId)
                ->select($selectFields)
                ->orderBy('d.fecha_subido', 'desc')
                ->get();

            $entregas = $entregas->map(function($entrega) {
                if ($entrega->ruta_archivo) {
                    if (filter_var($entrega->ruta_archivo, FILTER_VALIDATE_URL)) {
                        $entrega->archivo_url = $entrega->ruta_archivo;
                    } else {
                        $entrega->archivo_url = asset('storage/' . $entrega->ruta_archivo);
                    }
                }
                if ($entrega->fecha) {
                    try {
                        $fecha = new \DateTime($entrega->fecha);
                        $entrega->fecha = $fecha->format('Y-m-d');
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
                'estado' => 'required|in:pendiente,aprobado,rechazado'
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

            DB::table('documentos')
                ->where('id_documento', $entregaId)
                ->update([
                    'estado' => $request->estado
                ]);

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
