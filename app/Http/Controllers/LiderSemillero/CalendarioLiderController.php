<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Proyecto;

class CalendarioLiderController extends Controller
{
    private const EVENTO_TIPOS = ['REUNION','CAPACITACION','SEGUIMIENTO','ENTREGA','OTRO'];

    private function validarFinJornada(?Carbon $inicio, int $duracionMin): ?array
    {
        if (!$inicio) {
            return null;
        }
        if ($duracionMin <= 0) {
            return null;
        }

        $limite = $inicio->copy()->setTime(17, 0, 0);
        if ($inicio->gte($limite)) {
            return [
                'message' => 'No se puede agendar una reunión a las 5:00 PM o después.',
                'errors' => [
                    'fecha_hora' => ['No se puede agendar una reunión a las 5:00 PM o después.'],
                ],
            ];
        }

        $fin = $inicio->copy()->addMinutes($duracionMin);
        if ($fin->gt($limite)) {
            return [
                'message' => 'La reunión no puede terminar después de las 5:00 PM.',
                'errors' => [
                    'duracion' => ['La duración seleccionada hace que la reunión termine después de las 5:00 PM.'],
                ],
            ];
        }

        return null;
    }

    public function calendario()
    {
        $user = Auth::user();
        $uid  = $user ? (int)$user->id : null;
        $proyectos = collect();
        $aprendices = collect();

        // Proyectos asociados al/los semilleros del líder
        if (Schema::hasTable('proyectos')) {
            $semilleroIds = [];
            if ($uid && Schema::hasTable('lideres_semillero')) {
                $semilleroIds = DB::table('lideres_semillero')
                    ->where('id_lider_semi', $uid)
                    ->pluck('id_semillero')
                    ->filter()->values()->all();
            }
            $pq = Proyecto::query();
            if (!empty($semilleroIds)) { $pq->whereIn('id_semillero', $semilleroIds); }
            else { $pq->whereRaw('1=0'); }
            $proyectos = $pq->with(['aprendices:id_aprendiz'])->get();
        }

        // Catálogo de aprendices (para listado/filtrado del modal)
        if (Schema::hasTable('aprendices')) {
            try {
                $userCols = ['id'];
                if (Schema::hasTable('users')) {
                    foreach (['nombre','apellidos','name','email'] as $c) {
                        if (Schema::hasColumn('users', $c)) { $userCols[] = $c; }
                    }
                }

                $aq = Aprendiz::query()->with(['user' => function ($q) use ($userCols) {
                    $q->select(array_values(array_unique($userCols)));
                }]);
                // Filtrar por semillero(s) del líder si es posible
                if (isset($semilleroIds) && !empty($semilleroIds) && Schema::hasColumn('aprendices', 'semillero_id')) {
                    $aq->whereIn('semillero_id', $semilleroIds);
                }
                // Solo activos si existe la columna
                if (Schema::hasColumn('aprendices', 'estado')) {
                    $aq->where('estado', 'Activo');
                }
                $aprendices = $aq->get();
            } catch (\Throwable $e) {
                $aprendices = collect();
            }
        }

        return view('lider_semi.calendario_scml', compact('proyectos','aprendices'));
    }
    public function index()
    {
        $user = Auth::user();

        if (!Schema::hasTable('eventos')) {
            $reuniones = collect();
            return view('aprendiz.calendario.calendario_aprendiz', compact('reuniones'));
        }

        // Obtener IDs de proyectos del usuario sin asumir pivote fija
        $ids = $this->proyectoIdsUsuario((int)$user->id);
        $proyectosIds = collect($ids);

        // Traer eventos:
        // - donde el evento registre id_usuario = usuario actual
        // - o el usuario sea participante vía aprendices.id_usuario (o, si existe, por id_aprendiz)
        // - o el evento esté asociado a alguno de sus proyectos
        $query = Evento::query()->with([
            'proyecto:id_proyecto,nombre_proyecto',
            'lider:id,name'
        ]);

        $uid = $user->id;

        // Determinar IDs reales del aprendiz para usar en evento_participantes (id_aprendiz)
        $aprendizIdsPivot = [];
        if (Schema::hasTable('aprendices')) {
            $aprPkCols = [];
            if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
            if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
            $aprId = null;
            if (Schema::hasColumn('aprendices','id_usuario')) {
                foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $uid)->value($pk); if (!is_null($aprId)) break; }
            } elseif (Schema::hasColumn('aprendices','user_id')) {
                foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $uid)->value($pk); if (!is_null($aprId)) break; }
            } elseif (Schema::hasColumn('aprendices','email')) {
                $email = DB::table('users')->where('id', $uid)->value('email');
                if ($email) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('email', $email)->value($pk); if (!is_null($aprId)) break; }
                }
            }
            if (!is_null($aprId)) { $aprendizIdsPivot[] = $aprId; }
        }

        // Si no pudimos resolver ningún id_aprendiz, usar un valor imposible para que no traiga eventos ajenos
        if (empty($aprendizIdsPivot)) { $aprendizIdsPivot[] = -1; }

        $hasEventoUsuario = Schema::hasColumn('eventos','id_usuario');
        $hasEventoLider = Schema::hasColumn('eventos','id_lider');
        $query->where(function ($q) use ($uid, $hasEventoUsuario, $hasEventoLider) {
            // iniciar grupo seguro: solo eventos creados/asignados directamente al usuario
            $q->whereRaw('1=0');
            if ($hasEventoUsuario) { $q->orWhere('id_usuario', $uid); }
            if ($hasEventoLider) { $q->orWhere('id_lider', $uid); }
        })->orWhereExists(function ($sub) use ($aprendizIdsPivot) {
            $sub->from('evento_participantes as ep')
                ->whereColumn('ep.id_evento', 'eventos.id_evento')
                ->whereIn('ep.id_aprendiz', $aprendizIdsPivot);
        });

        // Mostrar solo reuniones futuras o vigentes
        $query->where('fecha_hora', '>=', now());

        // Excluir reuniones canceladas si existe la columna estado
        if (Schema::hasColumn('eventos','estado')) {
            $query->where(function ($q) {
                $q->whereNull('estado')
                  ->orWhere('estado', '<>', 'cancelado');
            });
        }

        // Evitar duplicados y ordenar
        $eventos = $query->select('eventos.*')->orderBy('fecha_hora', 'asc')->get()->unique('id_evento')->values();

        // Obtener participantes de todos los eventos en una sola consulta (robusto a distintos esquemas)
        $participantesPorEvento = $this->obtenerParticipantesPorEvento($eventos);

        $reuniones = $eventos->map(function ($e) use ($participantesPorEvento) {
            $eid = $e->id_evento ?? $e->id ?? null;
            // Normalizar ubicación
            $ubic = $e->ubicacion ?? null;
            $ubicNorm = $ubic ? strtolower(trim($ubic)) : null;
            // Limpiar valores "no aplica" de enlace/código
            $rawLink = trim((string)($e->link_virtual ?? ''));
            $rawCode = trim((string)($e->codigo_reunion ?? ''));
            $placeholders = ['n/a','na','no','no aplica','no aplica.','no aplica,','no aplica ','s/n','sn','-'];
            $link = $rawLink !== '' && !in_array(strtolower($rawLink), $placeholders, true) ? $rawLink : null;
            $code = $rawCode !== '' && !in_array(strtolower($rawCode), $placeholders, true) ? $rawCode : null;
            return (object) [
                'id' => $eid,
                'titulo' => $e->titulo ?? 'Reunión',
                'descripcion' => $e->descripcion ?? null,
                // FullCalendar interpreta cadenas sin zona como hora local
                'inicio' => $e->fecha_hora ? $e->fecha_hora->format('Y-m-d\TH:i:s') : null,
                'fecha_inicio' => $e->fecha_hora ? $e->fecha_hora->format('Y-m-d\TH:i:s') : null,
                'fecha_fin' => null,
                'tipo' => $e->tipo ?? null,
                'ubicacion' => $ubicNorm,
                'link_virtual' => $link,
                'codigo_reunion' => $code,
                'lider' => $this->leaderNameForEvent($e),
                'proyecto' => $e->proyecto,
                'participantes' => $participantesPorEvento->get($eid, []),
            ];
        });

        return view('aprendiz.calendario.calendario_aprendiz', compact('reuniones'));
    }

    public function events()
    {
        $user = Auth::user();
        if (!$user || !Schema::hasTable('eventos')) {
            return response()->json([]);
        }

        // Obtener IDs de proyectos del usuario
        $ids = $this->proyectoIdsUsuario((int)$user->id);
        $proyectosIds = collect($ids);

        $query = Evento::query()->with([
            'proyecto:id_proyecto,nombre_proyecto',
            'lider:id,name'
        ]);

        $uid = $user->id;
        $hasEventoUsuario = Schema::hasColumn('eventos','id_usuario');
        $hasEventoLider = Schema::hasColumn('eventos','id_lider');
        $query->where(function ($q) use ($uid, $hasEventoUsuario, $hasEventoLider) {
            $q->whereRaw('1=0');
            if ($hasEventoUsuario) { $q->orWhere('id_usuario', $uid); }
            if ($hasEventoLider) { $q->orWhere('id_lider', $uid); }
        })->orWhereExists(function ($sub) use ($uid) {
            // Resolver id_aprendiz del usuario para filtrar en evento_participantes
            $aprendizIdsPivot = [];
            if (Schema::hasTable('aprendices')) {
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                $aprId = null;
                if (Schema::hasColumn('aprendices','id_usuario')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $uid)->value($pk); if (!is_null($aprId)) break; }
                } elseif (Schema::hasColumn('aprendices','user_id')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $uid)->value($pk); if (!is_null($aprId)) break; }
                }
                if (!is_null($aprId)) { $aprendizIdsPivot[] = $aprId; }
            }
            if (empty($aprendizIdsPivot)) { $aprendizIdsPivot[] = -1; }
            $sub->from('evento_participantes as ep')
                ->whereColumn('ep.id_evento', 'eventos.id_evento')
                ->whereIn('ep.id_aprendiz', $aprendizIdsPivot);
        });

        // Solo eventos futuros o vigentes en el calendario
        $query->where('fecha_hora', '>=', now());

        // Excluir eventos cancelados
        if (Schema::hasColumn('eventos','estado')) {
            $query->where(function ($q) {
                $q->whereNull('estado')
                  ->orWhere('estado', '<>', 'cancelado');
            });
        }

        $eventos = $query->select('eventos.*')->orderBy('fecha_hora', 'asc')->get()->unique('id_evento')->values();

        // Participantes (nombres)
        $participantesPorEvento = $this->obtenerParticipantesPorEvento($eventos);

        // Mapear a formato FullCalendar
        $events = $eventos->map(function($e) use ($participantesPorEvento){
            $tipo = $e->tipo ?? null;
            $colorMap = [
                'planificacion' => '#2563eb',
                'seguimiento'   => '#16a34a',
                'revision'      => '#9333ea',
                'capacitacion'  => '#fb923c',
                'general'       => '#0ea5e9',
            ];
            $bg = $colorMap[strtolower((string)$tipo)] ?? '#0ea5e9';
            $eid = $e->id_evento ?? $e->id ?? null;
            $rawLink = trim((string)($e->link_virtual ?? ''));
            $placeholders = ['n/a','na','no','no aplica','no aplica.','no aplica,','no aplica ','s/n','sn','-'];
            $link = $rawLink !== '' && !in_array(strtolower($rawLink), $placeholders, true) ? $rawLink : null;
            return [
                'id'    => $eid,
                'title' => ($e->titulo ?? 'Reunión') . (isset($e->proyecto) ? ' · ' . (optional($e->proyecto)->nombre_proyecto ?? '') : ''),
                'start' => $e->fecha_hora ? $e->fecha_hora->format('Y-m-d\TH:i:s') : null,
                'end'   => null,
                'backgroundColor' => $bg,
                'borderColor' => $bg,
                'textColor' => '#fff',
                'extendedProps' => [
                    'lider'      => $this->leaderNameForEvent($e),
                    'proyecto'   => optional($e->proyecto)->nombre_proyecto,
                    'descripcion'=> $e->descripcion ?? '',
                    'tipo'       => $tipo,
                    'ubicacion'  => $e->ubicacion ?? null,
                    'link'       => $link,
                    'codigo'     => $e->codigo_reunion ?? null,
                    'participantes' => $participantesPorEvento->get($eid, []),
                ],
            ];
        })->values();

        return response()->json($events);
    }

    public function obtenerEventos(Request $request)
    {
        $mes  = (int) $request->query('mes');
        $anio = (int) $request->query('anio');
        if (!$mes || !$anio) {
            $now = now();
            $mes  = $mes  ?: (int) $now->format('m');
            $anio = $anio ?: (int) $now->format('Y');
        }

        $q = Evento::query()->with(['participantes' => function ($p) {
            $p->select('aprendices.id_aprendiz', 'aprendices.user_id', 'aprendices.nombres', 'aprendices.apellidos');
            if (Schema::hasColumn('aprendices', 'id_aprendiz')) {
                $p->addSelect('aprendices.id_aprendiz');
            }
            $p->withPivot(['asistencia', 'created_at', 'updated_at']);
            // Cargar usuario para usar sus nombres si el aprendiz no los tiene
            $userCols = ['id'];
            foreach (['nombre','apellidos','name','email'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $userCols[] = $col;
                }
            }
            $p->with(['user' => function ($u) use ($userCols) {
                $u->select(array_values(array_unique($userCols)));
            }]);
        }]);
        if (Schema::hasColumn('eventos','fecha_hora')) {
            $q->whereMonth('fecha_hora', $mes)->whereYear('fecha_hora', $anio);
        } elseif (Schema::hasColumn('eventos','fecha')) {
            $start = Carbon::create($anio, $mes, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth();
            $q->whereBetween('fecha', [$start->toDateString(), $end->toDateString()]);
        }

        $uid = Auth::id();
        $assignedIds = collect();
        if ($uid && Schema::hasTable('evento_asignaciones')) {
            try {
                $assignedIds = DB::table('evento_asignaciones')
                    ->where('tipo_destino', 'LIDER_SEMILLERO')
                    ->where('destino_id', $uid)
                    ->pluck('evento_id')
                    ->filter()
                    ->values();
            } catch (\Throwable $e) {
                $assignedIds = collect();
            }
        }

        $q->where(function ($w) use ($uid, $assignedIds) {
            $w->whereRaw('1=0');
            if ($uid) {
                if (Schema::hasColumn('eventos','id_lider')) { $w->orWhere('id_lider', $uid); }
                if (Schema::hasColumn('eventos','id_lider_semi')) { $w->orWhere('id_lider_semi', $uid); }
                if (Schema::hasColumn('eventos','id_lider_usuario')) { $w->orWhere('id_lider_usuario', $uid); }
                if (Schema::hasColumn('eventos','id_usuario')) { $w->orWhere('id_usuario', $uid); }
            }
            if ($assignedIds->isNotEmpty()) {
                $w->orWhereIn('id_evento', $assignedIds);
            }
        });

        if (Schema::hasColumn('eventos','estado')) {
            $q->where(function ($w) {
                $w->whereNull('estado')->orWhere('estado','<>','cancelado');
            });
        }

        $eventos = $q->get();

        // Adjuntar participantes con asistencia (para el drawer)
        try {
            $eventoIds = $eventos->pluck('id_evento')->filter()->values();
            if ($eventoIds->isNotEmpty() && Schema::hasTable('evento_participantes')) {
                $userCols = [];
                if (Schema::hasTable('users')) {
                    if (Schema::hasColumn('users', 'name')) { $userCols[] = 'u.name'; }
                    if (Schema::hasColumn('users', 'nombre')) { $userCols[] = 'u.nombre'; }
                    if (Schema::hasColumn('users', 'apellidos')) { $userCols[] = 'u.apellidos'; }
                    if (Schema::hasColumn('users', 'email')) { $userCols[] = 'u.email'; }
                }
                if (empty($userCols)) {
                    $userCols = ['u.email'];
                }

                $rows = DB::table('evento_participantes as ep')
                    ->leftJoin('aprendices as a', 'a.id_aprendiz', '=', 'ep.id_aprendiz')
                    ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                    ->whereIn('ep.id_evento', $eventoIds)
                    ->select(array_merge(['ep.id_evento', 'ep.id_aprendiz', 'ep.asistencia', 'a.nombres', 'a.apellidos'], $userCols))
                    ->get()
                    ->groupBy('id_evento');

                // Fallback: si el evento no tiene filas en evento_participantes, usar aprendices del proyecto
                $projRows = collect();
                if (Schema::hasTable('aprendiz_proyecto')) {
                    $proyectoIds = $eventos->pluck('id_proyecto')->filter()->unique()->values();
                    if ($proyectoIds->isNotEmpty()) {
                        $projRows = DB::table('aprendiz_proyecto as ap')
                            ->leftJoin('aprendices as a', 'a.id_aprendiz', '=', 'ap.id_aprendiz')
                            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                            ->whereIn('ap.id_proyecto', $proyectoIds)
                            ->select(array_merge(['ap.id_proyecto', 'ap.id_aprendiz', 'a.nombres', 'a.apellidos'], $userCols))
                            ->get()
                            ->groupBy('id_proyecto');
                    }
                }

                $eventos = $eventos->map(function ($ev) use ($rows, $projRows) {
                    $eid = $ev->id_evento ?? null;
                    $grp = $eid ? ($rows->get($eid, collect())) : collect();

                    // Base: aprendices del proyecto (si existe). Luego sobreescribir/mezclar con pivote.
                    $base = collect();
                    if (!empty($ev->id_proyecto)) {
                        $base = $projRows->get($ev->id_proyecto, collect())->map(function ($r) {
                            return (object)[
                                'id_aprendiz' => $r->id_aprendiz,
                                'asistencia' => 'PENDIENTE',
                                'name' => $r->name,
                                'nombre' => $r->nombre,
                                'apellidos' => $r->apellidos,
                                'email' => $r->email,
                            ];
                        });
                    }

                    if ($base->isNotEmpty()) {
                        $byApr = $base->keyBy(function ($r) { return (int)($r->id_aprendiz ?? 0); });
                        foreach ($grp as $r) {
                            $aid = (int)($r->id_aprendiz ?? 0);
                            if ($aid <= 0) continue;
                            // Si existe en base, conservar nombre/email de base pero tomar asistencia del pivote
                            if ($byApr->has($aid)) {
                                $b = $byApr->get($aid);
                                $b->asistencia = $r->asistencia ?? $b->asistencia;
                                // Si el pivote trae mejor nombre, reemplazar
                                if (isset($r->nombre) || isset($r->apellidos) || isset($r->name) || isset($r->email)) {
                                    $b->name = $r->name ?? $b->name;
                                    $b->nombre = $r->nombre ?? $b->nombre;
                                    $b->apellidos = $r->apellidos ?? $b->apellidos;
                                    $b->email = $r->email ?? $b->email;
                                }
                                $byApr->put($aid, $b);
                            } else {
                                $byApr->put($aid, $r);
                            }
                        }
                        $grp = $byApr->values();
                    } elseif ($grp->isEmpty() && !empty($ev->id_proyecto)) {
                        // Por compatibilidad, si no pudimos armar base pero existe proyecto, mantener lista vacía.
                        $grp = collect();
                    }

                    $ev->participantes = $grp->map(function ($r) {
                        $full = trim(((string)($r->nombres ?? '')) . ' ' . ((string)($r->apellidos ?? '')));
                        if ($full === '') {
                            $full = trim(((string)($r->nombre ?? '')) . ' ' . ((string)($r->apellidos ?? '')));
                        }
                        $name = $full !== '' ? $full : (trim((string)($r->name ?? '')) !== '' ? (string)$r->name : (string)($r->email ?? ''));
                        if ($name === '') { $name = 'Aprendiz #' . (string)($r->id_aprendiz ?? ''); }

                        $st = strtoupper(trim((string)($r->asistencia ?? 'PENDIENTE')));
                        $asistencia = $st === 'ASISTIO' ? 'asistio' : ($st === 'NO_ASISTIO' ? 'no_asistio' : 'pendiente');

                        return [
                            'id_aprendiz' => (int)($r->id_aprendiz ?? 0),
                            'nombre' => $name,
                            'asistencia' => $asistencia,
                        ];
                    })->values();
                    return $ev;
                });
            }
        } catch (\Throwable $e) {
            // noop
        }

        // Combinar relación cargada (si existe) con lo obtenido por consulta directa a pivote, sin perder entradas
        $eventos = $eventos->map(function ($ev) {
            if ($ev->relationLoaded('participantes') && $ev->participantes->count()) {
                $fromRelation = $ev->participantes->map(function ($p) {
                    $full = trim(((string)($p->nombres ?? '')) . ' ' . ((string)($p->apellidos ?? '')));
                    if ($full === '' && $p->user) {
                        $userName = trim(((string)($p->user->nombre ?? '')) . ' ' . ((string)($p->user->apellidos ?? '')));
                        if ($userName === '' && isset($p->user->name)) {
                            $userName = trim((string)$p->user->name);
                        }
                        if ($userName === '' && isset($p->user->email)) {
                            $userName = trim((string)$p->user->email);
                        }
                        $full = $userName;
                    }
                    if ($full === '') {
                        $full = 'Aprendiz #' . (string)($p->id_aprendiz ?? '');
                    }
                    $st = strtoupper(trim((string)($p->pivot->asistencia ?? 'PENDIENTE')));
                    $asistencia = $st === 'ASISTIO' ? 'asistio' : ($st === 'NO_ASISTIO' ? 'no_asistio' : 'pendiente');
                    return [
                        'id_aprendiz' => (int)($p->id_aprendiz ?? 0),
                        'nombre' => $full,
                        'asistencia' => $asistencia,
                    ];
                })->values()->all();

                // Unir con los ya calculados (si los hay) sin perder elementos
                $existing = collect($ev->participantes ?? [])->map(function ($p) {
                    return is_array($p) ? $p : (array)$p;
                });
                $merged = $existing->concat($fromRelation)->filter(function ($p) {
                    return isset($p['id_aprendiz']) && (int)$p['id_aprendiz'] > 0;
                })->unique('id_aprendiz')->values();
                $ev->participantes = $merged;
            }
            return $ev;
        });

        // Asegurar que el payload JSON incluya los participantes en múltiples claves para el front
        $eventos = $eventos->map(function ($ev) {
            $parts = collect($ev->participantes ?? [])->map(function ($p) {
                return is_array($p) ? $p : (array)$p;
            })->values()->all();
            $ev->participantes = $parts;
            // Claves alternas para compatibilidad con el JS del calendario
            $ev->participantes_detalle = $parts;
            $ev->participantes_detalles = $parts;
            $ev->aprendices_asignados = $parts;
            return $ev;
        });

        // Fallback defensivo: si algún evento llegó sin participantes y tiene id_proyecto,
        // cargarlos directo desde aprendiz_proyecto para mostrarlos en el detalle.
        $eventos = $eventos->map(function ($ev) {
            if (isset($ev->participantes) && is_iterable($ev->participantes) && count($ev->participantes)) {
                return $ev;
            }
            $list = collect();
            try {
                if (!empty($ev->id_proyecto) && Schema::hasTable('aprendiz_proyecto') && Schema::hasTable('aprendices')) {
                    $userCols = [];
                    if (Schema::hasTable('users')) {
                        if (Schema::hasColumn('users','name')) { $userCols[] = 'u.name'; }
                        if (Schema::hasColumn('users','nombre')) { $userCols[] = 'u.nombre'; }
                        if (Schema::hasColumn('users','apellidos')) { $userCols[] = 'u.apellidos'; }
                        if (Schema::hasColumn('users','email')) { $userCols[] = 'u.email'; }
                    }
                    if (empty($userCols)) { $userCols[] = 'u.email'; }

                    $rows = DB::table('aprendiz_proyecto as ap')
                        ->leftJoin('aprendices as a', 'a.id_aprendiz', '=', 'ap.id_aprendiz')
                        ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                        ->where('ap.id_proyecto', $ev->id_proyecto)
                        ->select(array_merge(['a.id_aprendiz', 'a.nombres', 'a.apellidos'], $userCols))
                        ->get();
                    $list = $rows->map(function ($r) {
                        $name = trim(((string)($r->nombres ?? '')) . ' ' . ((string)($r->apellidos ?? '')));
                        if ($name === '') {
                            $userName = trim(((string)($r->nombre ?? '')) . ' ' . ((string)($r->apellidos ?? '')));
                            if ($userName === '' && isset($r->name)) {
                                $userName = trim((string)$r->name);
                            }
                            if ($userName === '' && isset($r->email)) {
                                $userName = trim((string)$r->email);
                            }
                            $name = $userName;
                        }
                        if ($name === '') { $name = 'Aprendiz #' . (string)($r->id_aprendiz ?? ''); }
                        return [
                            'id_aprendiz' => (int)($r->id_aprendiz ?? 0),
                            'nombre' => $name,
                            'asistencia' => 'pendiente',
                        ];
                    })->filter(function ($p) {
                        return ($p['id_aprendiz'] ?? 0) > 0;
                    })->values();
                }
            } catch (\Throwable $e) {
                // noop
            }
            $ev->participantes = $list;
            return $ev;
        });

        return response()->json(['eventos' => $eventos]);
    }

    public function crearEvento(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'required|date',
            'duracion' => 'required|integer|min:15',
            'ubicacion' => 'nullable|string|max:255',
            'link_virtual' => 'nullable|url',
            'id_proyecto' => 'nullable|integer',
            'tipo' => 'nullable|string|in:REUNION,CAPACITACION,SEGUIMIENTO,ENTREGA,OTRO',
            'linea_investigacion' => 'nullable|string|max:255',
            'recordatorio' => 'nullable|string|max:50',
            'participantes' => 'nullable|array',
            'participantes.*' => 'integer',
        ]);

        if (empty($data['tipo'])) {
            $data['tipo'] = 'REUNION';
        }

        $uid = Auth::id();
        if (Schema::hasColumn('eventos','id_lider_semi')) { $data['id_lider_semi'] = $uid; }
        elseif (Schema::hasColumn('eventos','id_lider')) { $data['id_lider'] = $uid; }
        elseif (Schema::hasColumn('eventos','id_lider_usuario')) { $data['id_lider_usuario'] = $uid; }
        elseif (Schema::hasColumn('eventos','id_usuario')) { $data['id_usuario'] = $uid; }
        // Normalizar fecha a 'Y-m-d H:i:s' (evita problemas de parsing con 'T')
        $inicio = null;
        try {
            if (!empty($data['fecha_hora'])) {
                $inicio = Carbon::parse($data['fecha_hora']);
                $data['fecha_hora'] = $inicio->format('Y-m-d H:i:s');
            }
        } catch (\Throwable $e) {
            $inicio = null;
        }

        $duracionMin = (int)($data['duracion'] ?? 0);
        $err = $this->validarFinJornada($inicio, $duracionMin);
        if ($err) {
            return response()->json($err, 422);
        }

        $evento = Evento::create($data);
        try {
            $incomingParticipants = $request->exists('participantes') ? $request->input('participantes') : null;
            if (is_array($incomingParticipants) && method_exists($evento,'participantes')) {
                $participants = collect($incomingParticipants)
                    ->map(fn($v)=> (int)$v)
                    ->filter(fn($v)=> $v>0)
                    ->unique()
                    ->values()
                    ->all();
                $evento->participantes()->sync($participants);
            }
        } catch (\Throwable $e) {}

        // Recargar participantes para que el payload de respuesta ya los incluya
        $evento = $this->cargarParticipantesConNombres($evento);
        return response()->json(['success' => true, 'evento' => $evento]);
    }

    public function actualizarEvento(Request $request, $evento)
    {
        $ev = Evento::findOrFail($evento);
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'fecha_hora' => 'sometimes|required|date',
            'duracion' => 'sometimes|required|integer|min:15',
            'ubicacion' => 'sometimes|nullable|string|max:255',
            'link_virtual' => 'sometimes|nullable|url',
            'id_proyecto' => 'sometimes|nullable|integer',
            'tipo' => 'sometimes|nullable|string|in:REUNION,CAPACITACION,SEGUIMIENTO,ENTREGA,OTRO',
            'linea_investigacion' => 'sometimes|nullable|string|max:255',
            'recordatorio' => 'sometimes|nullable|string|max:50',
            'participantes' => 'sometimes|array',
            'participantes.*' => 'integer',
        ]);

        if (array_key_exists('tipo', $data) && empty($data['tipo'])) {
            $data['tipo'] = 'REUNION';
        }

        // Normalizar y redirigir la fecha a la columna vigente
        $inicio = null;
        if (array_key_exists('fecha_hora', $data)) {
            try {
                $dt = Carbon::parse($data['fecha_hora']);
                $inicio = $dt->copy();
                $norm = $dt->format('Y-m-d H:i:s');
                if (Schema::hasColumn('eventos','fecha_hora')) {
                    $data['fecha_hora'] = $norm;
                } elseif (Schema::hasColumn('eventos','fecha')) {
                    $data['fecha'] = $dt->toDateString();
                    unset($data['fecha_hora']);
                } elseif (Schema::hasColumn('eventos','fecha_inicio')) {
                    $data['fecha_inicio'] = $norm;
                    unset($data['fecha_hora']);
                }
            } catch (\Throwable $e) {}
        }

        if (!$inicio) {
            try {
                $current = $ev->getAttribute('fecha_hora') ?? $ev->getAttribute('fecha_inicio') ?? $ev->getAttribute('fecha') ?? null;
                if ($current) {
                    $inicio = Carbon::parse($current);
                }
            } catch (\Throwable $e) {
                $inicio = null;
            }
        }

        $duracionMin = array_key_exists('duracion', $data) ? (int)$data['duracion'] : (int)($ev->duracion ?? 0);
        $err = $this->validarFinJornada($inicio, $duracionMin);
        if ($err) {
            return response()->json($err, 422);
        }

        $ev->update($data);
        try {
            // Usar exists() para JSON:has() puede fallar con arreglos vacíos
            $incomingParticipants = $request->exists('participantes') ? $request->input('participantes') : null;
            if (is_array($incomingParticipants) && method_exists($ev,'participantes')) {
                $participants = collect($incomingParticipants)
                    ->map(fn($v)=> (int)$v)
                    ->filter(fn($v)=> $v>0)
                    ->unique()
                    ->values()
                    ->all();
                $ev->participantes()->sync($participants);
            }
        } catch (\Throwable $e) {}
        $ev = $this->cargarParticipantesConNombres($ev->fresh());
        return response()->json(['success' => true, 'evento' => $ev]);
    }

    /**
     * Recarga la relación participantes con nombres y asistencia normalizados,
     * devolviendo el modelo con claves alternativas para el front.
     */
    private function cargarParticipantesConNombres(Evento $evento): Evento
    {
        try {
            $evento->load(['participantes' => function ($p) {
                $p->select('aprendices.id_aprendiz', 'aprendices.user_id', 'aprendices.nombres', 'aprendices.apellidos')
                  ->withPivot(['asistencia', 'created_at', 'updated_at']);
                $userCols = ['id'];
                foreach (['nombre','apellidos','name','email'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $userCols[] = $col;
                    }
                }
                $p->with(['user' => function ($u) use ($userCols) {
                    $u->select(array_values(array_unique($userCols)));
                }]);
            }]);

            if ($evento->relationLoaded('participantes') && $evento->participantes->count()) {
                $parts = $evento->participantes->map(function ($p) {
                    $full = trim(((string)($p->nombres ?? '')) . ' ' . ((string)($p->apellidos ?? '')));
                    if ($full === '' && $p->user) {
                        $userName = trim(((string)($p->user->nombre ?? '')) . ' ' . ((string)($p->user->apellidos ?? '')));
                        if ($userName === '' && isset($p->user->name)) {
                            $userName = trim((string)$p->user->name);
                        }
                        if ($userName === '' && isset($p->user->email)) {
                            $userName = trim((string)$p->user->email);
                        }
                        $full = $userName;
                    }
                    if ($full === '') {
                        $full = 'Aprendiz #' . (string)($p->id_aprendiz ?? '');
                    }
                    $st = strtoupper(trim((string)($p->pivot->asistencia ?? 'PENDIENTE')));
                    $asistencia = $st === 'ASISTIO' ? 'asistio' : ($st === 'NO_ASISTIO' ? 'no_asistio' : 'pendiente');
                    return [
                        'id_aprendiz' => (int)($p->id_aprendiz ?? 0),
                        'nombre' => $full,
                        'asistencia' => $asistencia,
                    ];
                })->values()->all();

                $evento->setAttribute('participantes', $parts);
                $evento->setAttribute('participantes_detalle', $parts);
                $evento->setAttribute('participantes_detalles', $parts);
                $evento->setAttribute('aprendices_asignados', $parts);
            }
        } catch (\Throwable $e) {
            // noop
        }

        return $evento;
    }

    public function eliminarEvento($evento)
    {
        $ev = Evento::findOrFail($evento);
        $ev->delete();
        return response()->json(['success' => true]);
    }

    public function generarEnlace($evento)
    {
        $ev = Evento::findOrFail($evento);
        $ev->link_virtual = 'https://teams.microsoft.com/l/meeting/'.uniqid();
        $ev->save();
        return response()->json(['link' => $ev->link_virtual]);
    }

    public function getInfoReunion($evento)
    {
        $ev = Evento::with(['proyecto:id_proyecto,nombre_proyecto','lider:id,name'])->findOrFail($evento);
        return response()->json(['evento' => $ev]);
    }

    public function actualizarAsistencia($evento, $aprendiz, Request $request)
    {
        $data = $request->validate([
            'asistencia' => 'required|string|in:pendiente,asistio,no_asistio,PENDIENTE,ASISTIO,NO_ASISTIO',
        ]);
        if (!Schema::hasTable('evento_participantes')) {
            return response()->json(['ok' => false, 'message' => 'pivot missing'], 200);
        }
        $pivotCol = Schema::hasColumn('evento_participantes','id_aprendiz') ? 'id_aprendiz' : (Schema::hasColumn('evento_participantes','aprendiz_id') ? 'aprendiz_id' : null);
        if (!$pivotCol) { return response()->json(['ok' => false, 'message' => 'pivot col missing'], 200); }
        $raw = strtoupper(trim((string)$data['asistencia']));
        $payload = ['asistencia' => in_array($raw, ['PENDIENTE','ASISTIO','NO_ASISTIO'], true) ? $raw : 'PENDIENTE'];
        try {
            DB::table('evento_participantes')->updateOrInsert([
                'id_evento' => (int)$evento,
                $pivotCol   => (int)$aprendiz,
            ], $payload);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 200);
        }
    }

    private function proyectoIdsUsuario(int $userId): array
    {
        // Resolver id_aprendiz del usuario (si existe) para pivotes basadas en aprendices
        $aprId = null;
        if (Schema::hasTable('aprendices')) {
            try {
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                if (Schema::hasColumn('aprendices','id_usuario')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','user_id')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
            } catch (\Throwable $e) {
                $aprId = null;
            }
        }

        // Intentar con pivotes conocidas
        $pivotTables = ['proyecto_user', 'aprendiz_proyecto', 'aprendices_proyectos', 'aprendiz_proyectos', 'proyecto_aprendiz', 'proyectos_aprendices', 'proyecto_aprendices'];
        $projCols   = ['id_proyecto','proyecto_id','idProyecto'];
        $userCols   = ['user_id','id_usuario','id_aprendiz','aprendiz_id','idAprendiz'];

        foreach ($pivotTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $pcol = null; $ucol = null;
            foreach ($projCols as $c) { if (Schema::hasColumn($tbl, $c)) { $pcol = $c; break; } }
            foreach ($userCols as $c) { if (Schema::hasColumn($tbl, $c)) { $ucol = $c; break; } }
            if ($pcol && $ucol) {
                try {
                    $value = $userId;
                    // Si la pivote usa id_aprendiz/aprendiz_id, mapear el userId al id_aprendiz real
                    if (in_array($ucol, ['id_aprendiz','aprendiz_id','idAprendiz'], true)) {
                        if (is_null($aprId)) {
                            continue; // no podemos mapear, pasar a la siguiente pivote
                        }
                        $value = $aprId;
                    }

                    return DB::table($tbl)
                        ->where($ucol, $value)
                        ->distinct()
                        ->pluck($pcol)
                        ->map(fn($v)=> (int)$v)
                        ->all();
                } catch (\Exception $e) {}
            }
        }

        // Fallback: documentos como relación implícita
        if (Schema::hasTable('documentos')) {
            if (Schema::hasColumn('documentos','id_usuario') && Schema::hasColumn('documentos','id_proyecto')) {
                try {
                    return DB::table('documentos')
                        ->where('id_usuario', $userId)
                        ->distinct()
                        ->pluck('id_proyecto')
                        ->map(fn($v)=> (int)$v)
                        ->all();
                } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('documentos','id_aprendiz') && Schema::hasTable('aprendices')) {
                $aprId = null;
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                if (Schema::hasColumn('aprendices','id_usuario')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','user_id')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','email')) {
                    $email = DB::table('users')->where('id', $userId)->value('email');
                    if ($email) {
                        foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('email', $email)->value($pk); if (!is_null($aprId)) break; }
                    }
                }
                if ($aprId) {
                    try {
                        return DB::table('documentos')
                            ->where('id_aprendiz', $aprId)
                            ->distinct()
                            ->pluck('id_proyecto')
                            ->map(fn($v)=> (int)$v)
                            ->all();
                    } catch (\Exception $e) {}
                }
            }
        }

        return [];
    }

    // Próximas 5 reuniones del aprendiz para el dashboard (robusto a esquemas variables)
    public function proximasReuniones()
    {
        try {
            $user = Auth::user();
            if (!$user || !Schema::hasTable('eventos')) {
                return response()->json(['proximas' => []]);
            }

            $uid = $user->id;

            // IDs de aprendiz usados en ep.id_aprendiz (id_aprendiz real)
            $aprIds = [];
            $aprId = null;
            if (Schema::hasTable('aprendices')) {
                try {
                    $aprPkCols = [];
                    if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                    if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                    if (Schema::hasColumn('aprendices','id_usuario')) {
                        foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $uid)->value($pk); if (!is_null($aprId)) break; }
                    } elseif (Schema::hasColumn('aprendices','user_id')) {
                        foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $uid)->value($pk); if (!is_null($aprId)) break; }
                    } elseif (Schema::hasColumn('aprendices','email')) {
                        $email = DB::table('users')->where('id', $uid)->value('email');
                        if ($email) {
                            foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('email', $email)->value($pk); if (!is_null($aprId)) break; }
                        }
                    }
                } catch (\Throwable $ex) {
                    // continuar sin $aprId
                }
                if (!is_null($aprId)) { $aprIds[] = $aprId; }
            }

            if (empty($aprIds)) { $aprIds[] = -1; }

            // Construir consulta evitando columnas inexistentes en eventos
            $query = Evento::query()->with(['proyecto:id_proyecto,nombre_proyecto','lider:id,name'])
                ->whereDate('fecha_hora', '>=', now()->startOfDay());

            // Excluir eventos cancelados del dashboard
            if (Schema::hasColumn('eventos','estado')) {
                $query->where(function ($q) {
                    $q->whereNull('estado')
                      ->orWhere('estado', '<>', 'cancelado');
                });
            }

            $query->where(function ($q) use ($uid) {
                // arranque seguro
                $q->whereRaw('1=0');
                if (Schema::hasColumn('eventos', 'id_usuario')) { $q->orWhere('id_usuario', $uid); }
                // columnas alternativas para líder
                foreach (['id_lider','id_lider_semi','id_lider_usuario'] as $col) {
                    if (Schema::hasColumn('eventos', $col)) { $q->orWhere($col, $uid); break; }
                }
            })
            ->orWhereExists(function ($sub) use ($aprIds) {
                $sub->from('evento_participantes as ep')
                    ->whereColumn('ep.id_evento', 'eventos.id_evento')
                    ->whereIn('ep.id_aprendiz', $aprIds);
            })
            ->orderBy('fecha_hora', 'asc')
            ->select('eventos.*')
            ->take(10);

            $eventos = $query->get()->unique('id_evento')->values();

            // Participantes por evento (nombres) con join dinámico
            $participantesPorEvento = $this->obtenerParticipantesPorEvento($eventos);

            $proximas = $eventos->map(function ($e) use ($participantesPorEvento) {
                $eid = $e->id_evento ?? $e->id ?? null;
                $ubicNorm = $e->ubicacion ? strtolower(trim($e->ubicacion)) : null;
                $rawLink = trim((string)($e->link_virtual ?? ''));
                $placeholders = ['n/a','na','no','no aplica','no aplica.','no aplica,','no aplica ','s/n','sn','-'];
                $link = $rawLink !== '' && !in_array(strtolower($rawLink), $placeholders, true) ? $rawLink : null;
                $dt = null;
                try {
                    if ($e->fecha_hora instanceof Carbon) { $dt = $e->fecha_hora; }
                    elseif (!empty($e->fecha_hora)) { $dt = Carbon::parse($e->fecha_hora); }
                } catch (\Throwable $ex) { $dt = null; }
                return [
                    'id' => $eid,
                    'titulo' => $e->titulo ?? 'Reunión',
                    'proyecto' => optional($e->proyecto)->nombre_proyecto,
                    'tipo' => $e->tipo ?? null,
                    'fecha_hora' => $dt ? $dt->format('Y-m-d\\TH:i:s') : null,
                    'duracion' => $e->duracion,
                    'ubicacion' => $ubicNorm,
                    'link_virtual' => $link,
                    'lider' => $this->leaderNameForEvent($e),
                    'participantes' => $participantesPorEvento->get($eid, []),
                ];
            });

            return response()->json(['proximas' => $proximas]);
        } catch (\Throwable $e) {
            return response()->json([
                'proximas' => [],
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    private function leaderNameForEvent($e): ?string
    {
        try {
            // 1) Relación estándar si existe
            if (isset($e->relationLoaded) && $e->relationLoaded('lider') && $e->lider) {
                return $e->lider->name ?? null;
            }
            if (isset($e->lider) && !is_null($e->lider)) {
                return is_object($e->lider) ? ($e->lider->name ?? null) : null;
            }
            // 2) Resolver por columnas posibles en eventos
            $leaderId = $e->id_lider ?? $e->id_lider_usuario ?? $e->id_lider_semi ?? $e->id_usuario ?? null;
            if ($leaderId) {
                // Intentar nombre completo en users (nombre + apellidos) o name/email
                $row = DB::table('users')->where('id', $leaderId)->first();
                if ($row) {
                    $nombre = '';
                    if (isset($row->nombre) || isset($row->apellidos)) {
                        $nombre = trim(((string)($row->nombre ?? '')) . ' ' . ((string)($row->apellidos ?? '')));
                    }
                    if ($nombre !== '') { return $nombre; }
                    if (isset($row->name) && trim((string)$row->name) !== '') { return (string)$row->name; }
                    if (isset($row->email) && trim((string)$row->email) !== '') { return (string)$row->email; }
                }
                // Fallback: lideres_semillero por id_usuario
                if (\Illuminate\Support\Facades\Schema::hasTable('lideres_semillero')) {
                    $ls = DB::table('lideres_semillero')->where('id_usuario', $leaderId)->first();
                    if ($ls) {
                        $nombre = trim(((string)($ls->nombres ?? '')) . ' ' . ((string)($ls->apellidos ?? '')));
                        if ($nombre !== '') { return $nombre; }
                        if (isset($ls->correo_institucional) && trim((string)$ls->correo_institucional) !== '') { return (string)$ls->correo_institucional; }
                    }
                }
            }
        } catch (\Throwable $ex) {
            // noop
        }
        return null;
    }

    private function obtenerParticipantesPorEvento($eventos)
    {
        try {
            if ($eventos->isEmpty()) return collect();

            // Mapa evento -> proyecto
            $evToPid = $eventos->mapWithKeys(function($e){
                $eid = $e->id_evento ?? $e->id ?? null;
                $pid = $e->id_proyecto ?? null;
                return $eid ? [$eid => $pid] : [];
            });

            // 1) Priorizar grupos del proyecto si existen
            if (Schema::hasTable('grupos') && Schema::hasTable('grupo_aprendices')) {
                $pids = collect($evToPid->values())->filter()->unique()->values();
                if ($pids->isNotEmpty()) {
                    $grows = DB::table('grupos')
                        ->whereIn('id_proyecto', $pids)
                        ->select('id_grupo','id_proyecto')
                        ->get();
                    $pidToGids = $grows->groupBy('id_proyecto')->map(fn($g)=> $g->pluck('id_grupo')->all());

                    $allGids = $grows->pluck('id_grupo')->unique()->values();
                    if ($allGids->isNotEmpty()) {
                        $userCol = Schema::hasColumn('grupo_aprendices','id_aprendiz') ? 'id_aprendiz' : (Schema::hasColumn('grupo_aprendices','id_usuario') ? 'id_usuario' : (Schema::hasColumn('grupo_aprendices','user_id') ? 'user_id' : null));
                        if ($userCol) {
                            $q = DB::table('grupo_aprendices')->whereIn('id_grupo', $allGids);
                            if (Schema::hasColumn('grupo_aprendices','activo')) { $q->where('activo',1); }
                            $ga = $q->select('id_grupo', DB::raw($userCol.' as uid'))->get();

                            // Resolver nombres
                            $hasNombreCompletoApr = Schema::hasColumn('aprendices', 'nombre_completo');
                            $aprNameExpr = $hasNombreCompletoApr
                                ? 'aprendices.nombre_completo'
                                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";

                            $namesByUid = collect();
                            if ($userCol === 'id_aprendiz' && Schema::hasTable('aprendices')) {
                                $aprIds = $ga->pluck('uid')->unique()->values();
                                if ($aprIds->isNotEmpty()) {
                                    $rowsN = DB::table('aprendices')->whereIn('id_aprendiz', $aprIds)->select('id_aprendiz', DB::raw($aprNameExpr.' as nombre'))->get();
                                    $namesByUid = $rowsN->pluck('nombre','id_aprendiz');
                                }
                            } elseif (in_array($userCol, ['id_usuario','user_id']) && Schema::hasTable('aprendices')) {
                                $dbName = DB::getDatabaseName();
                                $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
                                $aprUserFk = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));
                                if ($aprUserFk) {
                                    $uids = $ga->pluck('uid')->unique()->values();
                                    if ($uids->isNotEmpty()) {
                                        $rowsN = DB::table('aprendices')->whereIn($aprUserFk, $uids)->select($aprUserFk.' as uid', DB::raw($aprNameExpr.' as nombre'))->get();
                                        $namesByUid = $rowsN->pluck('nombre','uid');
                                    }
                                }
                            }

                            // Armar salida por evento, con posible fallback por-evento a pivote si queda vacío
                            $out = collect();
                            $missing = [];
                            foreach ($evToPid as $eid => $pid) {
                                $gids = $pid ? ($pidToGids[$pid] ?? []) : [];
                                if (empty($gids)) { $out[$eid] = []; $missing[] = $eid; continue; }
                                $uids = $ga->filter(fn($r)=> in_array($r->id_grupo, $gids))->pluck('uid')->unique()->values();
                                $names = $uids->map(fn($u)=> (string)($namesByUid[$u] ?? ''))->filter()->values()->all();
                                if (empty($names)) { $missing[] = $eid; }
                                $out[$eid] = $names;
                            }

                            // Fallback por-evento usando evento_participantes para los que quedaron vacíos
                            if (!empty($missing) && Schema::hasTable('evento_participantes')) {
                                // Detectar columna en pivote y resolver nombres como en la sección fallback inferior
                                $epCols = ['id_aprendiz','aprendiz_id','id_usuario','user_id','email','nombre','nombre_completo'];
                                $epCol = null;
                                foreach ($epCols as $c) { if (Schema::hasColumn('evento_participantes', $c)) { $epCol = $c; break; } }
                                if ($epCol) {
                                    if (in_array($epCol, ['nombre','nombre_completo'])) {
                                        $rows = DB::table('evento_participantes')
                                            ->whereIn('id_evento', $missing)
                                            ->select('id_evento', DB::raw($epCol.' as nombre'))
                                            ->get()
                                            ->groupBy('id_evento');
                                        foreach ($rows as $eid => $grp) { $out[$eid] = $grp->pluck('nombre')->filter()->values()->all(); }
                                    } else {
                                        $hasNombreCompletoApr = Schema::hasColumn('aprendices', 'nombre_completo');
                                        $aprNameExpr = $hasNombreCompletoApr
                                            ? 'aprendices.nombre_completo'
                                            : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";
                                        if (in_array($epCol, ['id_aprendiz','aprendiz_id'])) {
                                            $aprPk = null;
                                            foreach (['id_aprendiz','id','id_usuario','user_id'] as $cand) { if (Schema::hasColumn('aprendices', $cand)) { $aprPk = $cand; break; } }
                                            if ($aprPk) {
                                                $rows = DB::table('evento_participantes')
                                                    ->join('aprendices', DB::raw('aprendices.' . $aprPk), '=', DB::raw('evento_participantes.' . $epCol))
                                                    ->whereIn('evento_participantes.id_evento', $missing)
                                                    ->select('evento_participantes.id_evento', DB::raw($aprNameExpr.' as nombre'))
                                                    ->get()
                                                    ->groupBy('id_evento');
                                                foreach ($rows as $eid => $grp) { $out[$eid] = $grp->pluck('nombre')->filter()->values()->all(); }
                                            }
                                        } elseif (in_array($epCol, ['id_usuario','user_id']) && Schema::hasTable('users')) {
                                            $rows = DB::table('evento_participantes')
                                                ->join('users', 'users.id', '=', DB::raw('evento_participantes.' . $epCol))
                                                ->whereIn('evento_participantes.id_evento', $missing)
                                                ->select('evento_participantes.id_evento', 'users.name as nombre')
                                                ->get()
                                                ->groupBy('id_evento');
                                            foreach ($rows as $eid => $grp) { $out[$eid] = $grp->pluck('nombre')->filter()->values()->all(); }
                                        } elseif ($epCol === 'email') {
                                            $rows = null;
                                            if (Schema::hasTable('aprendices') && Schema::hasColumn('aprendices','email')) {
                                                $rows = DB::table('evento_participantes')
                                                    ->leftJoin('aprendices', 'aprendices.email', '=', 'evento_participantes.email')
                                                    ->whereIn('evento_participantes.id_evento', $missing)
                                                    ->select('evento_participantes.id_evento', DB::raw($aprNameExpr.' as nombre'))
                                                    ->get();
                                            } elseif (Schema::hasTable('users') && Schema::hasColumn('users','email')) {
                                                $rows = DB::table('evento_participantes')
                                                    ->leftJoin('users', 'users.email', '=', 'evento_participantes.email')
                                                    ->whereIn('evento_participantes.id_evento', $missing)
                                                    ->select('evento_participantes.id_evento', 'users.name as nombre')
                                                    ->get();
                                            }
                                            if ($rows) {
                                                $grouped = $rows->groupBy('id_evento');
                                                foreach ($grouped as $eid => $grp) { $out[$eid] = $grp->pluck('nombre')->filter()->values()->all(); }
                                            }
                                        }
                                    }
                                }
                            }

                            return $out;
                        }
                    }
                }
            }

            // 2) Fallback: si existe evento_participantes, usarlo
            if (!Schema::hasTable('evento_participantes')) return collect();

            $ids = $eventos->pluck('id_evento')->filter()->values();
            if ($ids->isEmpty()) return collect();

            // Detectar columnas en pivote
            $epCols = ['id_aprendiz','aprendiz_id','id_usuario','user_id','email','nombre','nombre_completo'];
            $epCol = null;
            foreach ($epCols as $c) { if (Schema::hasColumn('evento_participantes', $c)) { $epCol = $c; break; } }

            // Si el pivote ya trae nombre directo
            if ($epCol && in_array($epCol, ['nombre','nombre_completo'])) {
                $rows = DB::table('evento_participantes')
                    ->whereIn('id_evento', $ids)
                    ->select('id_evento', DB::raw($epCol.' as nombre'))
                    ->get();
                return $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
            }

            $hasNombreCompletoApr = Schema::hasColumn('aprendices', 'nombre_completo');
            $aprNameExpr = $hasNombreCompletoApr
                ? 'aprendices.nombre_completo'
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";

            if ($epCol && in_array($epCol, ['id_aprendiz','aprendiz_id'])) {
                $aprPk = null;
                foreach (['id_aprendiz','id','id_usuario','user_id'] as $cand) { if (Schema::hasColumn('aprendices', $cand)) { $aprPk = $cand; break; } }
                if ($aprPk) {
                    // Primer intento: nombres desde aprendices
                    $rows = DB::table('evento_participantes')
                        ->join('aprendices', DB::raw('aprendices.' . $aprPk), '=', DB::raw('evento_participantes.' . $epCol))
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', DB::raw($aprNameExpr.' as nombre'))
                        ->get();
                    $grouped = $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());

                    // Si algún evento quedó sin nombres (por falta de columnas en aprendices), intentar vía users
                    $missingIds = $ids->diff($grouped->keys())->values();
                    if ($missingIds->isNotEmpty()) {
                        $aprUserFk = null; foreach (['id_usuario','user_id','id_user'] as $fk) { if (Schema::hasColumn('aprendices', $fk)) { $aprUserFk = $fk; break; } }
                        if ($aprUserFk && Schema::hasTable('users')) {
                            $rowsU = DB::table('evento_participantes')
                                ->join('aprendices', DB::raw('aprendices.' . $aprPk), '=', DB::raw('evento_participantes.' . $epCol))
                                ->join('users', 'users.id', '=', DB::raw('aprendices.' . $aprUserFk))
                                ->whereIn('evento_participantes.id_evento', $missingIds)
                                ->select(
                                    'evento_participantes.id_evento',
                                    DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(users.nombre,''),' ',COALESCE(users.apellidos,'')), ' '), NULLIF(users.name,''), users.email) as nombre")
                                )
                                ->get();
                            $g2 = $rowsU->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
                            // Unir resultados
                            foreach ($g2 as $k => $arr) { $grouped[$k] = $arr; }
                        }
                    }

                    return $grouped;
                }
            }

            if ($epCol && in_array($epCol, ['id_usuario','user_id']) && Schema::hasTable('users')) {
                $rows = DB::table('evento_participantes')
                    ->join('users', 'users.id', '=', DB::raw('evento_participantes.' . $epCol))
                    ->whereIn('evento_participantes.id_evento', $ids)
                    ->select('evento_participantes.id_evento', 'users.name as nombre')
                    ->get();
                return $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
            }

            if ($epCol === 'email') {
                $rows = null;
                if (Schema::hasTable('aprendices') && Schema::hasColumn('aprendices','email')) {
                    $rows = DB::table('evento_participantes')
                        ->leftJoin('aprendices', 'aprendices.email', '=', 'evento_participantes.email')
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', DB::raw($aprNameExpr.' as nombre'))
                        ->get();
                } elseif (Schema::hasTable('users') && Schema::hasColumn('users','email')) {
                    $rows = DB::table('evento_participantes')
                        ->leftJoin('users', 'users.email', '=', 'evento_participantes.email')
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', 'users.name as nombre')
                        ->get();
                }
                if ($rows) {
                    return $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
                }
            }

            return collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
