<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Proyecto;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendarioLiderController extends Controller
{
    // Vista principal del calendario del líder
    public function calendario()
    {
        // Reusar la lógica existente de SemilleroController::calendario
        // Obtenemos aprendices y proyectos con su relación a aprendices
        $aprendices = collect();
        if (Schema::hasTable('aprendices')) {
            $selectCols = ['aprendices.id_aprendiz'];
            $hasUsers = Schema::hasTable('users');
            $aprHas = fn(string $c) => Schema::hasColumn('aprendices', $c);
            $usrHas = fn(string $c) => Schema::hasColumn('users', $c);
            $joinUser = false;
            $joinCol = $aprHas('user_id') ? 'user_id' : ($aprHas('id_usuario') ? 'id_usuario' : null);

            if ($aprHas('tipo_documento')) {
                $selectCols[] = 'aprendices.tipo_documento';
            } elseif ($hasUsers && $usrHas('tipo_documento') && ($aprHas('user_id') || $aprHas('id_usuario'))) {
                $selectCols[] = 'u.tipo_documento as tipo_documento';
                $joinUser = true;
            }

            if ($aprHas('documento')) {
                $selectCols[] = 'aprendices.documento';
            } elseif ($hasUsers && $usrHas('documento') && ($aprHas('user_id') || $aprHas('id_usuario'))) {
                $selectCols[] = 'u.documento as documento';
                $joinUser = true;
            }

            foreach (['programa','ficha'] as $c) {
                if ($aprHas($c)) $selectCols[] = 'aprendices.'.$c;
            }

            $aprHasNombres = $aprHas('nombres');
            $aprHasApellidos = $aprHas('apellidos');
            $usrNameCol = $usrHas('name') ? 'name' : ($usrHas('nombre') ? 'nombre' : null);
            $usrLastCol = $usrHas('apellidos') ? 'apellidos' : ($usrHas('apellido') ? 'apellido' : null);
            if (!($aprHasNombres && $aprHasApellidos) && $hasUsers && ($usrNameCol || $usrLastCol) && ($aprHas('user_id') || $aprHas('id_usuario'))) {
                $joinUser = true;
            }
            $baseConcat = $aprHasNombres && $aprHasApellidos
                ? "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))"
                : (($usrNameCol || $usrLastCol)
                    ? "CONCAT(COALESCE(u.`".($usrNameCol??'')."`,''),' ',COALESCE(u.`".($usrLastCol??'')."`,''))"
                    : "''");
            $emailExpr = $aprHas('correo_institucional') ? 'aprendices.correo_institucional' : ($usrHas('email') ? 'u.email' : "''");
            $nameExpr = "COALESCE(NULLIF(TRIM($baseConcat),''), $emailExpr)";

            $aprendices = DB::table('aprendices')
                ->when($joinUser, function($q) use ($joinCol){ if ($joinCol) { $q->leftJoin('users as u','u.id','=',DB::raw('aprendices.'.$joinCol)); } })
                ->select(array_merge($selectCols, [ DB::raw($nameExpr.' as nombre_completo') ]))
                ->orderByRaw($nameExpr)
                ->get();
        }

        $proyectos = collect();
        if (Schema::hasTable('proyectos')) {
            $userId = Auth::id();
            $proyectos = Proyecto::query()
                ->when(Schema::hasColumn('proyectos','id_semillero'), function ($q) use ($userId) {
                    // filtrar por semilleros del líder si corresponde
                    $semillerosIds = DB::table('semilleros')
                        ->where('id_lider_semi', $userId)
                        ->pluck('id_semillero');
                    if ($semillerosIds->isNotEmpty()) {
                        $q->whereIn('id_semillero', $semillerosIds);
                    }
                })
                ->get();
        }

        return view('lider_semi.calendario_scml', compact('aprendices', 'proyectos'));
    }

    // Actualizar estado de asistencia de un participante en un evento (calendario del líder)
    public function actualizarAsistencia(Request $request, $eventoId, $aprendizId)
    {
        try {
            if (!Schema::hasTable('evento_participantes')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de participantes no disponible'
                ], 500);
            }

            if (!Schema::hasColumn('evento_participantes', 'asistencia')) {
                return response()->json([
                    'success' => false,
                    'message' => "La columna 'asistencia' no existe en evento_participantes"
                ], 500);
            }

            $data = $request->validate([
                'asistencia' => 'required|string|in:pendiente,asistio,no_asistio',
            ]);

            // Verificar que el evento pertenezca al líder autenticado (si existe columna de líder)
            $leaderCol = null;
            if (Schema::hasColumn('eventos','id_lider_semi')) {
                $leaderCol = 'id_lider_semi';
            } elseif (Schema::hasColumn('eventos','id_lider_usuario')) {
                $leaderCol = 'id_lider_usuario';
            } elseif (Schema::hasColumn('eventos','id_lider')) {
                $leaderCol = 'id_lider';
            }

            $eventoQuery = Evento::where('id_evento', $eventoId);
            if ($leaderCol) {
                $eventoQuery->where($leaderCol, Auth::id());
            }
            $evento = $eventoQuery->first();

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para modificar este evento'
                ], 403);
            }

            $updated = DB::table('evento_participantes')
                ->where('id_evento', $eventoId)
                ->where('id_aprendiz', $aprendizId)
                ->update(['asistencia' => $data['asistencia']]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante no encontrado en el evento'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar asistencia (CalendarioLiderController): '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar asistencia'
            ], 500);
        }
    }

    // === Lógica principal de calendario del líder (copiada desde SemilleroController) ===

    // Obtener eventos del mes
    public function obtenerEventos(Request $request)
    {
        try {
            if (!Schema::hasTable('eventos')) {
                return response()->json([
                    'success' => true,
                    'eventos' => []
                ]);
            }

            $userId = Auth::id();
            $leaderCol = Schema::hasColumn('eventos','id_lider_semi')
                ? 'id_lider_semi'
                : (Schema::hasColumn('eventos','id_lider_usuario') ? 'id_lider_usuario' : 'id_lider');
            $mes = $request->input('mes');
            $anio = $request->input('anio');

            $query = Evento::where($leaderCol, $userId)
                ->with([
                    'participantes' => function($q){
                        $q->select(DB::raw('aprendices.id_aprendiz as id_aprendiz'));
                        if (Schema::hasColumn('aprendices','nombres'))    { $q->addSelect('aprendices.nombres'); }
                        if (Schema::hasColumn('aprendices','apellidos'))  { $q->addSelect('aprendices.apellidos'); }
                    },
                    'proyecto:id_proyecto,nombre_proyecto'
                ]);

            if ($mes && $anio) {
                $query->whereYear('fecha_hora', $anio)
                      ->whereMonth('fecha_hora', $mes);
            }

            $eventos = $query->orderBy('fecha_hora', 'asc')->get();

            $tz = config('app.timezone', 'America/Bogota');
            $leaderCol = $leaderCol ?? (Schema::hasColumn('eventos','id_lider_semi')
                ? 'id_lider_semi'
                : (Schema::hasColumn('eventos','id_lider_usuario') ? 'id_lider_usuario' : 'id_lider'));

            return response()->json([
                'success' => true,
                'eventos' => $eventos->map(function($evento) use ($tz, $leaderCol) {
                    $dt = $evento->fecha_hora instanceof \DateTimeInterface
                        ? Carbon::instance($evento->fecha_hora)
                        : Carbon::parse($evento->fecha_hora);
                    $fechaLocal = $dt->setTimezone($tz)->format('Y-m-d H:i:s');

                    $parts = collect();
                    try {
                        if (Schema::hasTable('evento_participantes')) {
                            $hasAsistenciaCol = Schema::hasColumn('evento_participantes', 'asistencia');
                            $rows = DB::table('evento_participantes')
                                ->where('id_evento', $evento->id_evento)
                                ->select(
                                    'id_aprendiz',
                                    DB::raw($hasAsistenciaCol
                                        ? "COALESCE(asistencia, 'pendiente') as asistencia"
                                        : "'pendiente' as asistencia"
                                    )
                                )
                                ->get();

                            $parts = collect();
                            if ($rows->isNotEmpty() && Schema::hasTable('aprendices')) {
                                $aprIds = $rows->pluck('id_aprendiz')->map(fn($v)=>(int)$v)->unique()->values()->all();
                                $aprHas = fn(string $c) => Schema::hasColumn('aprendices', $c);
                                $usrHas = fn(string $c) => Schema::hasColumn('users', $c);
                                $hasUsers = Schema::hasTable('users');
                                $joinCol = $aprHas('user_id') ? 'user_id' : ($aprHas('id_usuario') ? 'id_usuario' : null);
                                $joinUser = $hasUsers && $joinCol !== null;
                                $aprHasNombres = $aprHas('nombres');
                                $aprHasApellidos = $aprHas('apellidos');
                                $usrNameCol = $usrHas('name') ? 'name' : ($usrHas('nombre') ? 'nombre' : null);
                                $usrLastCol = $usrHas('apellidos') ? 'apellidos' : ($usrHas('apellido') ? 'apellido' : null);
                                if (!($aprHasNombres && $aprHasApellidos) && !($usrNameCol || $usrLastCol)) {
                                    $joinUser = false;
                                }
                                $baseConcat = $aprHasNombres && $aprHasApellidos
                                    ? "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))"
                                    : (($usrNameCol || $usrLastCol)
                                        ? "CONCAT(COALESCE(u.`".($usrNameCol??'')."`,''),' ',COALESCE(u.`".($usrLastCol??'')."`,''))"
                                        : "''");
                                $emailExpr = $aprHas('correo_institucional') ? 'aprendices.correo_institucional' : ($usrHas('email') ? 'u.email' : "''");
                                $nameExpr = "COALESCE(NULLIF(TRIM($baseConcat),''), $emailExpr)";

                                $aprRowsQ = DB::table('aprendices');
                                if ($joinUser) {
                                    $aprRowsQ->leftJoin('users as u','u.id','=',DB::raw('aprendices.'.$joinCol));
                                }
                                $aprRows = $aprRowsQ
                                    ->whereIn('aprendices.id_aprendiz', $aprIds)
                                    ->select(DB::raw('aprendices.id_aprendiz as id_aprendiz'), DB::raw($nameExpr.' as nombre'))
                                    ->get()
                                    ->keyBy('id_aprendiz');

                                $parts = $rows->map(function($r) use ($aprRows){
                                    $id = (int) $r->id_aprendiz;
                                    $nombre = '';
                                    if ($aprRows->has($id)) {
                                        $nombre = trim((string)($aprRows[$id]->nombre ?? ''));
                                    }
                                    if ($nombre === '') {
                                        $nombre = 'Aprendiz #'.$id;
                                    }
                                    return [
                                        'id' => $id,
                                        'nombre_completo' => $nombre,
                                        'asistencia' => $r->asistencia,
                                    ];
                                });
                            } else {
                                $parts = $rows->map(function($r){
                                    $id = (int) $r->id_aprendiz;
                                    return [
                                        'id' => $id,
                                        'nombre_completo' => 'Aprendiz #'.$id,
                                        'asistencia' => $r->asistencia,
                                    ];
                                });
                            }
                        }

                        if ($parts->isEmpty() && $evento->relationLoaded('participantes')) {
                            $parts = collect($evento->participantes)->map(function($p) {
                                $nombre = '';
                                if (isset($p->nombre_completo)) {
                                    $nombre = $p->nombre_completo;
                                } else {
                                    $nombre = trim(($p->nombres ?? '').' '.($p->apellidos ?? ''));
                                }
                                if ($nombre === '') {
                                    $nombre = 'Aprendiz #'.$p->id_aprendiz;
                                }
                                return [
                                    'id' => $p->id_aprendiz,
                                    'nombre_completo' => $nombre,
                                    'asistencia' => 'pendiente',
                                ];
                            });
                        }

                        if ($parts->isEmpty() && !empty($evento->id_proyecto)) {
                            $aprIds = $this->getProjectAprendizIds((int)$evento->id_proyecto);
                            $aprIds = array_values(array_unique(array_filter(array_map('intval', $aprIds))));
                            if (!empty($aprIds) && Schema::hasTable('aprendices')) {
                                $aprHas = fn(string $c) => Schema::hasColumn('aprendices', $c);
                                $usrHas = fn(string $c) => Schema::hasColumn('users', $c);
                                $hasUsers = Schema::hasTable('users');
                                $joinCol = $aprHas('user_id') ? 'user_id' : ($aprHas('id_usuario') ? 'id_usuario' : null);
                                $joinUser = $hasUsers && $joinCol !== null;
                                $aprHasNombres = $aprHas('nombres');
                                $aprHasApellidos = $aprHas('apellidos');
                                $usrNameCol = $usrHas('name') ? 'name' : ($usrHas('nombre') ? 'nombre' : null);
                                $usrLastCol = $usrHas('apellidos') ? 'apellidos' : ($usrHas('apellido') ? 'apellido' : null);
                                if (!($aprHasNombres && $aprHasApellidos) && !($usrNameCol || $usrLastCol)) {
                                    $joinUser = false;
                                }
                                $baseConcat = $aprHasNombres && $aprHasApellidos
                                    ? "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))"
                                    : (($usrNameCol || $usrLastCol)
                                        ? "CONCAT(COALESCE(u.`".($usrNameCol??'')."`,''),' ',COALESCE(u.`".($usrLastCol??'')."`,''))"
                                        : "''");
                                $emailExpr = $aprHas('correo_institucional') ? 'aprendices.correo_institucional' : ($usrHas('email') ? 'u.email' : "''");
                                $nameExpr = "COALESCE(NULLIF(TRIM($baseConcat),''), $emailExpr)";

                                $aprQ = DB::table('aprendices');
                                if ($joinUser) {
                                    $aprQ->leftJoin('users as u','u.id','=',DB::raw('aprendices.'.$joinCol));
                                }
                                $rows = $aprQ
                                    ->whereIn('aprendices.id_aprendiz', $aprIds)
                                    ->select(DB::raw('aprendices.id_aprendiz as id_aprendiz'), DB::raw($nameExpr.' as nombre'))
                                    ->get();
                                $parts = $rows->map(function($r){
                                    $nombre = trim((string)($r->nombre ?? ''));
                                    if ($nombre === '') {
                                        $nombre = 'Aprendiz #'.$r->id_aprendiz;
                                    }
                                    return [
                                        'id' => $r->id_aprendiz,
                                        'nombre_completo' => $nombre,
                                        'asistencia' => 'pendiente',
                                    ];
                                });
                            }
                        }
                    } catch (\Throwable $t) {
                        // dejar $parts vacío en caso de error
                    }

                    return [
                        'id' => $evento->id_evento,
                        'leader_id' => $evento->{$leaderCol} ?? null,
                        'titulo' => $evento->titulo,
                        'descripcion' => $evento->descripcion,
                        'linea_investigacion' => $evento->linea_investigacion,
                        'fecha_hora' => $fechaLocal,
                        'duracion' => $evento->duracion,
                        'tipo' => $evento->tipo,
                        'ubicacion' => $evento->ubicacion,
                        'estado' => $evento->estado ?? null,
                        'link_virtual' => $evento->link_virtual,
                        'codigo_reunion' => $evento->codigo_reunion,
                        'recordatorio' => $evento->recordatorio,
                        'proyecto' => $evento->proyecto ? $evento->proyecto->nombre_proyecto : null,
                        'participantes' => $parts
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener eventos (CalendarioLiderController): ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'eventos' => []
            ]);
        }
    }

    // Crear nuevo evento
    public function crearEvento(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'tipo' => 'required|string',
                'linea_investigacion' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_hora' => 'required|date',
                'duracion' => 'required|integer|min:15',
                'ubicacion' => 'required|string',
                'link_virtual' => 'nullable|string|max:1000',
                'recordatorio' => 'nullable|string',
                'id_proyecto' => 'nullable|exists:proyectos,id_proyecto',
                'participantes' => 'nullable|array',
                'participantes.*' => 'exists:aprendices,id_aprendiz',
                'generar_enlace' => 'nullable|string|in:teams,meet,personalizado'
            ]);

            if (!$this->fechaHoraPermitida(new \DateTime($validated['fecha_hora']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha/hora no está permitida. No se pueden agendar fines de semana/feriados, ni fuera de 08:00-16:50, ni en almuerzo (12:00-13:55).'
                ], 422);
            }

            $inicio = new \DateTime($validated['fecha_hora']);
            $duracion = (int) $validated['duracion'];
            if ($this->hayConflicto($inicio, $duracion, null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una reunión programada que se solapa con ese horario. Selecciona otra hora disponible.'
                ], 422);
            }
            if ($this->cruzaAlmuerzo($inicio, $duracion)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reunión no puede cruzar el horario de almuerzo (12:00 a 13:55).'
                ], 422);
            }

            $leaderCol = Schema::hasColumn('eventos','id_lider_semi')
                ? 'id_lider_semi'
                : (Schema::hasColumn('eventos','id_lider_usuario') ? 'id_lider_usuario' : 'id_lider');
            $exists = Evento::where($leaderCol, Auth::id())
                ->where('fecha_hora', $validated['fecha_hora'])
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una reunión programada para ese horario.'
                ], 422);
            }

            $linkVirtual = $validated['link_virtual'] ?? null;
            $codigoReunion = null;

            if (($validated['ubicacion'] === 'virtual' || $validated['ubicacion'] === 'hibrido') && empty($linkVirtual)) {
                $plataforma = $validated['generar_enlace'] ?? 'teams';
                $linkVirtual = $this->generarEnlaceReunion($plataforma, $validated['titulo']);
                $codigoReunion = \Illuminate\Support\Str::random(10);
            }

            $nextId = (int) (DB::table('eventos')->max('id_evento') ?? 0) + 1;

            $evento = Evento::create([
                'id_evento' => $nextId,
                $leaderCol => Auth::id(),
                'id_proyecto' => $validated['id_proyecto'] ?? null,
                'titulo' => $validated['titulo'],
                'tipo' => $validated['tipo'],
                'linea_investigacion' => $validated['linea_investigacion'] ?? '',
                'descripcion' => $validated['descripcion'] ?? null,
                'fecha_hora' => $validated['fecha_hora'],
                'duracion' => $validated['duracion'],
                'ubicacion' => $validated['ubicacion'],
                'link_virtual' => $linkVirtual,
                'codigo_reunion' => $codigoReunion,
                'recordatorio' => is_numeric($validated['recordatorio'] ?? null) ? (int)$validated['recordatorio'] : 0
            ]);

            if (Schema::hasTable('evento_participantes')) {
                DB::table('evento_participantes')->where('id_evento', $evento->id_evento)->delete();

                $insert = [];
                $now = now();
                $hasAsistenciaCol = Schema::hasColumn('evento_participantes','asistencia');

                $aprendizIdsProyecto = [];
                if (!empty($validated['id_proyecto'])) {
                    $aprendizIdsProyecto = $this->getProjectAprendizIds((int)$validated['id_proyecto']);
                }

                $idsBase = [];
                if (!empty($aprendizIdsProyecto)) {
                    $idsBase = $aprendizIdsProyecto;
                } elseif (!empty($validated['participantes'])) {
                    $idsBase = $validated['participantes'];
                }

                foreach ($idsBase as $aid) {
                    $aid = (int) $aid;
                    if ($aid <= 0) continue;
                    $row = [
                        'id_evento' => $evento->id_evento,
                        'id_aprendiz' => $aid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    if ($hasAsistenciaCol) {
                        $row['asistencia'] = 'pendiente';
                    }
                    $insert[] = $row;
                }

                if (!empty($insert)) {
                    DB::table('evento_participantes')->insert($insert);
                }
            }

            $nameExpr = Schema::hasColumn('aprendices','nombre_completo')
                ? "COALESCE(aprendices.nombre_completo, CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')))"
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";
            $evento->load([
                'participantes' => function($q) use ($nameExpr){
                    $q->select(
                        DB::raw('aprendices.id_aprendiz as id_aprendiz'),
                        DB::raw($nameExpr.' as nombre_completo')
                    );
                    if (Schema::hasColumn('aprendices','nombres'))   { $q->addSelect('aprendices.nombres'); }
                    if (Schema::hasColumn('aprendices','apellidos')) { $q->addSelect('aprendices.apellidos'); }
                },
                'proyecto:id_proyecto,nombre_proyecto'
            ]);

            $tz = config('app.timezone', 'America/Bogota');
            $dt = $evento->fecha_hora instanceof \DateTimeInterface
                ? Carbon::instance($evento->fecha_hora)
                : Carbon::parse($evento->fecha_hora);
            $fechaLocal = $dt->setTimezone($tz)->format('Y-m-d H:i:s');

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'evento' => [
                    'id' => $evento->id_evento,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'linea_investigacion' => $evento->linea_investigacion,
                    'fecha_hora' => $fechaLocal,
                    'duracion' => $evento->duracion,
                    'tipo' => $evento->tipo,
                    'ubicacion' => $evento->ubicacion,
                    'link_virtual' => $evento->link_virtual,
                    'codigo_reunion' => $evento->codigo_reunion,
                    'proyecto' => $evento->proyecto ? $evento->proyecto->nombre_proyecto : null,
                    'participantes' => $evento->participantes->map(function($p) {
                        $nombre = trim((string)($p->nombre_completo ?? ''));
                        if ($nombre === '') {
                            $nombre = trim(trim((string)($p->nombres ?? '')) . ' ' . trim((string)($p->apellidos ?? '')));
                        }
                        if ($nombre === '') {
                            $nombre = 'Aprendiz #'.$p->id_aprendiz;
                        }
                        return [
                            'id' => $p->id_aprendiz,
                            'nombre' => $nombre
                        ];
                    })
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el evento: ' . ($e->getMessage()),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear evento (CalendarioLiderController): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el evento: ' . $e->getMessage()
            ], 500);
        }
    }

    // Actualizar evento existente (incluye actualización de enlace)
    public function actualizarEvento(Request $request, $id)
    {
        try {
            $leaderCol = null;
            if (Schema::hasColumn('eventos','id_lider_semi')) $leaderCol = 'id_lider_semi';
            elseif (Schema::hasColumn('eventos','id_lider_usuario')) $leaderCol = 'id_lider_usuario';
            elseif (Schema::hasColumn('eventos','id_lider')) $leaderCol = 'id_lider';

            $eventoQ = Evento::where('id_evento', $id);
            if ($leaderCol) { $eventoQ->where($leaderCol, Auth::id()); }
            $evento = $eventoQ->first();
            if (!$evento) {
                $evento = Evento::where('id_evento', $id)->firstOrFail();
            }

            $validated = $request->validate([
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_hora' => 'sometimes|required|date',
                'duracion' => 'sometimes|required|integer|min:15',
                'tipo' => 'nullable|string',
                'linea_investigacion' => 'nullable|string|max:255',
                'ubicacion' => 'nullable|string',
                'link_virtual' => 'nullable|string|max:1000',
                'id_proyecto' => 'nullable|exists:proyectos,id_proyecto',
                'participantes' => 'nullable|array',
                'participantes.*' => 'exists:aprendices,id_aprendiz',
                'generar_enlace' => 'nullable|string|in:teams,meet,personalizado'
            ]);

            $onlyLinkUpdate = array_key_exists('link_virtual', $validated)
                && !array_key_exists('fecha_hora', $validated)
                && !array_key_exists('duracion', $validated)
                && !array_key_exists('titulo', $validated)
                && !array_key_exists('ubicacion', $validated)
                && !array_key_exists('tipo', $validated)
                && !array_key_exists('linea_investigacion', $validated)
                && !array_key_exists('id_proyecto', $validated)
                && !array_key_exists('participantes', $validated);

            if (!$onlyLinkUpdate) {
                $inicioStr = isset($validated['fecha_hora'])
                    ? $validated['fecha_hora']
                    : ($evento->fecha_hora instanceof \DateTimeInterface ? $evento->fecha_hora->format('Y-m-d H:i:s') : (string)$evento->fecha_hora);
                $inicioValidar = new \DateTime($inicioStr);
                $duracionValidar = (int) ($validated['duracion'] ?? $evento->duracion);
                if ($this->hayConflicto($inicioValidar, $duracionValidar, (int)$evento->id_evento)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El horario elegido se solapa con otra reunión existente. Selecciona otra hora disponible.'
                    ], 422);
                }
                if ($this->cruzaAlmuerzo($inicioValidar, $duracionValidar)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La reunión no puede cruzar el horario de almuerzo (12:00 a 13:55).'
                    ], 422);
                }
            }

            $updateData = [];
            if (isset($validated['id_proyecto'])) {
                $updateData['id_proyecto'] = $validated['id_proyecto'];
            }
            if (isset($validated['titulo'])) {
                $updateData['titulo'] = $validated['titulo'];
            }
            if (isset($validated['descripcion'])) {
                $updateData['descripcion'] = $validated['descripcion'];
            }
            if (isset($validated['fecha_hora'])) {
                $updateData['fecha_hora'] = $validated['fecha_hora'];
            }
            if (isset($validated['duracion'])) {
                $updateData['duracion'] = $validated['duracion'];
            }
            if (isset($validated['tipo'])) {
                $updateData['tipo'] = $validated['tipo'];
            }
            if (isset($validated['linea_investigacion'])) {
                $updateData['linea_investigacion'] = $validated['linea_investigacion'];
            }
            if (isset($validated['ubicacion'])) {
                $updateData['ubicacion'] = $validated['ubicacion'];
            }

            if (array_key_exists('link_virtual', $validated)) {
                $link = $validated['link_virtual'];
                if (is_string($link)) {
                    $trim = trim($link);
                    if ($trim !== '' && !preg_match('/^https?:\/\//i', $trim)) {
                        $trim = 'https://' . $trim;
                    }
                    $updateData['link_virtual'] = $trim === '' ? null : $trim;
                } else {
                    $updateData['link_virtual'] = $link;
                }
            } elseif (($validated['ubicacion'] ?? $evento->ubicacion) === 'virtual' && empty($evento->link_virtual)) {
                $plataforma = $validated['generar_enlace'] ?? 'teams';
                $updateData['link_virtual'] = $this->generarEnlaceReunion($plataforma, $validated['titulo'] ?? $evento->titulo);
                $updateData['codigo_reunion'] = $evento->codigo_reunion ?: \Illuminate\Support\Str::random(10);
            }

            $evento->update($updateData);

            if (Schema::hasTable('evento_participantes')) {
                DB::table('evento_participantes')->where('id_evento', $evento->id_evento)->delete();

                $now = now();
                $insert = [];
                $hasAsistenciaCol = Schema::hasColumn('evento_participantes','asistencia');

                $pid = $updateData['id_proyecto'] ?? $evento->id_proyecto ?? null;
                $idsBase = [];
                if (!empty($pid)) {
                    $aprendizIdsProyecto = $this->getProjectAprendizIds((int)$pid);
                    if (!empty($aprendizIdsProyecto)) {
                        $idsBase = $aprendizIdsProyecto;
                    }
                }

                if (empty($idsBase) && isset($validated['participantes']) && is_array($validated['participantes'])) {
                    $idsBase = $validated['participantes'];
                }

                foreach ($idsBase as $aid) {
                    $aid = (int) $aid;
                    if ($aid <= 0) continue;
                    $row = [
                        'id_evento' => $evento->id_evento,
                        'id_aprendiz' => $aid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    if ($hasAsistenciaCol) {
                        $row['asistencia'] = 'pendiente';
                    }
                    $insert[] = $row;
                }

                if (!empty($insert)) {
                    DB::table('evento_participantes')->insert($insert);
                }
            }

            $nameExpr = Schema::hasColumn('aprendices','nombre_completo')
                ? "COALESCE(aprendices.nombre_completo, CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')))"
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";
            $evento->load([
                'participantes' => function($q) use ($nameExpr){
                    $q->select(
                        DB::raw('aprendices.id_aprendiz as id_aprendiz'),
                        DB::raw($nameExpr.' as nombre_completo')
                    );
                    if (Schema::hasColumn('aprendices','nombres'))   { $q->addSelect('aprendices.nombres'); }
                    if (Schema::hasColumn('aprendices','apellidos')) { $q->addSelect('aprendices.apellidos'); }
                },
                'proyecto:id_proyecto,nombre_proyecto'
            ]);

            $tz = config('app.timezone', 'America/Bogota');
            $dt = $evento->fecha_hora instanceof \DateTimeInterface
                ? Carbon::instance($evento->fecha_hora)
                : Carbon::parse($evento->fecha_hora);
            $fechaLocal = $dt->setTimezone($tz)->format('Y-m-d H:i:s');

            return response()->json([
                'success' => true,
                'message' => 'Evento actualizado correctamente',
                'evento' => [
                    'id' => $evento->id_evento,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'linea_investigacion' => $evento->linea_investigacion,
                    'fecha_hora' => $fechaLocal,
                    'duracion' => $evento->duracion,
                    'tipo' => $evento->tipo,
                    'ubicacion' => $evento->ubicacion,
                    'link_virtual' => $evento->link_virtual,
                    'codigo_reunion' => $evento->codigo_reunion,
                    'proyecto' => $evento->proyecto ? $evento->proyecto->nombre_proyecto : null,
                    'participantes' => $evento->participantes->map(function($p) {
                        $nombre = trim((string)($p->nombre_completo ?? ''));
                        if ($nombre === '') {
                            $nombre = trim(trim((string)($p->nombres ?? '')) . ' ' . trim((string)($p->apellidos ?? '')));
                        }
                        if ($nombre === '') {
                            $nombre = 'Aprendiz #'.$p->id_aprendiz;
                        }
                        return [
                            'id' => $p->id_aprendiz,
                            'nombre' => $nombre
                        ];
                    })
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento: ' . ($e->getMessage()),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar evento (CalendarioLiderController): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento'
            ], 500);
        }
    }

    // Helpers de reglas de fecha/horario (copiados)
    private function fechaHoraPermitida(\DateTime $fechaHora): bool
    {
        $diaSemana = (int)$fechaHora->format('w');
        if ($diaSemana === 0 || $diaSemana === 6) return false;

        $fechaStr = $fechaHora->format('Y-m-d');
        $feriados = config('app.feriados', []);
        if (in_array($fechaStr, $feriados, true)) return false;

        $horaMinutos = (int)$fechaHora->format('Hi');
        if ($horaMinutos < 800 || $horaMinutos > 1650) return false;
        if ($horaMinutos >= 1200 && $horaMinutos <= 1355) return false;
        return true;
    }

    private function hayConflicto(\DateTime $inicio, int $duracionMinutos, ?int $idEventoExcluir = null): bool
    {
        if (!Schema::hasTable('eventos')) return false;

        $leaderCol = Schema::hasColumn('eventos','id_lider_semi')
            ? 'id_lider_semi'
            : (Schema::hasColumn('eventos','id_lider_usuario') ? 'id_lider_usuario' : 'id_lider');

        $fin = (clone $inicio)->modify("+{$duracionMinutos} minutes");

        $query = Evento::where($leaderCol, Auth::id())
            ->whereDate('fecha_hora', $inicio->format('Y-m-d'));

        if ($idEventoExcluir) {
            $query->where('id_evento', '!=', $idEventoExcluir);
        }

        $eventos = $query->get();

        foreach ($eventos as $ev) {
            $evInicio = $ev->fecha_hora instanceof \DateTimeInterface
                ? Carbon::instance($ev->fecha_hora)
                : Carbon::parse($ev->fecha_hora);
            $evFin = (clone $evInicio)->addMinutes((int)$ev->duracion);

            if ($inicio < $evFin && $fin > $evInicio) {
                return true;
            }
        }

        return false;
    }

    private function cruzaAlmuerzo(\DateTime $inicio, int $duracionMinutos): bool
    {
        $fin = (clone $inicio)->modify("+{$duracionMinutos} minutes");
        $fecha = $inicio->format('Y-m-d');
        $almuerzoInicio = new \DateTime($fecha.' 12:00:00');
        $almuerzoFin = new \DateTime($fecha.' 13:55:00');
        return ($inicio < $almuerzoFin) && ($fin > $almuerzoInicio);
    }

    private function getProjectAprendizIds(int $projectId): array
    {
        try {
            if (Schema::hasTable('aprendiz_proyecto')) {
                $ids = DB::table('aprendiz_proyecto')
                    ->where('id_proyecto', $projectId)
                    ->pluck('id_aprendiz')
                    ->map(fn($v)=> (int)$v)
                    ->all();
                if (!empty($ids)) return $ids;
            }
        } catch (\Throwable $e) {
        }
        return [];
    }

    private function generarEnlaceReunion($plataforma, $titulo)
    {
        $codigo = \Illuminate\Support\Str::random(10);
        $tituloCodificado = urlencode($titulo);

        switch ($plataforma) {
            case 'teams':
                $tenant = config('app.teams_tenant_id', 'public');
                $oid = Auth::id();
                return sprintf(
                    'https://teams.microsoft.com/l/meetup-join/19%%3Ameeting_%s%%40thread.v2/0?context=%%7B%%22Tid%%22%%3A%%22%s%%22%%2C%%22Oid%%22%%3A%%22%s%%22%%7D&subject=%s',
                    $codigo,
                    $tenant,
                    $oid,
                    $tituloCodificado
                );
            case 'meet':
                return "https://meet.google.com/{$codigo}";
            case 'personalizado':
                return "https://your-platform.com/meeting/{$codigo}";
            default:
                return null;
        }
    }
}
