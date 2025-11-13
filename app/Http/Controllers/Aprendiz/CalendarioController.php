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

        // Determinar posibles IDs del aprendiz en la pivote (user id o id_aprendiz)
        $aprendizIdsPivot = [$uid];
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

        $hasEventoUsuario = Schema::hasColumn('eventos','id_usuario');
        $hasEventoLider = Schema::hasColumn('eventos','id_lider');
        $query->where(function ($q) use ($uid, $proyectosIds, $hasEventoUsuario, $hasEventoLider) {
            // iniciar grupo seguro
            $q->whereRaw('1=0');
            if ($hasEventoUsuario) { $q->orWhere('id_usuario', $uid); }
            if ($hasEventoLider) { $q->orWhere('id_lider', $uid); }
            if ($proyectosIds->isNotEmpty()) { $q->orWhereIn('id_proyecto', $proyectosIds); }
        })->orWhereExists(function ($sub) use ($aprendizIdsPivot) {
            $sub->from('evento_participantes as ep')
                ->whereColumn('ep.id_evento', 'eventos.id_evento')
                ->whereIn('ep.id_aprendiz', $aprendizIdsPivot);
        });

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
        $query->where(function ($q) use ($uid, $proyectosIds, $hasEventoUsuario, $hasEventoLider) {
            $q->whereRaw('1=0');
            if ($hasEventoUsuario) { $q->orWhere('id_usuario', $uid); }
            if ($hasEventoLider) { $q->orWhere('id_lider', $uid); }
            if ($proyectosIds->isNotEmpty()) { $q->orWhereIn('id_proyecto', $proyectosIds); }
        })->orWhereExists(function ($sub) use ($uid) {
            $aprendizIdsPivot = [$uid];
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
            $sub->from('evento_participantes as ep')
                ->whereColumn('ep.id_evento', 'eventos.id_evento')
                ->whereIn('ep.id_aprendiz', $aprendizIdsPivot);
        });

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
                    return DB::table($tbl)
                        ->where($ucol, $userId)
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

            // Posibles IDs del aprendiz usados en ep.id_aprendiz (puede ser user_id o id_aprendiz)
            $aprIds = [$uid];
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
                    // continuar con $aprIds por defecto
                }
                if (!is_null($aprId) && $aprId != $uid) { $aprIds[] = $aprId; }
            }

            // Construir consulta evitando columnas inexistentes en eventos
            $query = Evento::query()->with(['proyecto:id_proyecto,nombre_proyecto','lider:id,name'])
                ->whereDate('fecha_hora', '>=', now()->startOfDay());

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
        $participantesPorEvento = collect();
        try {
            if (!Schema::hasTable('evento_participantes') || $eventos->isEmpty()) {
                return collect();
            }
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

            // Preparar expresiones de nombre para aprendices
            $hasNombreCompletoApr = Schema::hasColumn('aprendices', 'nombre_completo');
            $aprNameExpr = $hasNombreCompletoApr
                ? 'aprendices.nombre_completo'
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";

            // Si el pivote referencia aprendices
            if ($epCol && in_array($epCol, ['id_aprendiz','aprendiz_id'])) {
                // Determinar PK en aprendices para unir contra ep.$epCol
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

            // Si el pivote referencia users
            if ($epCol && in_array($epCol, ['id_usuario','user_id'])) {
                if (Schema::hasTable('users')) {
                    $rows = DB::table('evento_participantes')
                        ->join('users', 'users.id', '=', DB::raw('evento_participantes.' . $epCol))
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', 'users.name as nombre')
                        ->get();
                    return $rows->groupBy('id_evento')->map(fn($g)=> $g->pluck('nombre')->filter()->values()->all());
                }
            }

            // Si el pivote guarda email
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
