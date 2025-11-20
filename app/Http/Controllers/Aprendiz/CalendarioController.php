<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarioController extends Controller
{
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
                $name = DB::table('users')->where('id', $leaderId)->value('name');
                if ($name) return $name;
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
                    $rows = DB::table('evento_participantes')
                        ->join('aprendices', DB::raw('aprendices.' . $aprPk), '=', DB::raw('evento_participantes.' . $epCol))
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', DB::raw($aprNameExpr.' as nombre'))
                        ->get();
                    return $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
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
