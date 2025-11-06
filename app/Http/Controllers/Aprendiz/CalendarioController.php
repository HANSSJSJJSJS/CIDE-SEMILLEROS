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
            return view('aprendiz.calendario.index', compact('reuniones'));
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

        // Obtener participantes de todos los eventos en una sola consulta
        $participantesPorEvento = collect();
        if (Schema::hasTable('evento_participantes') && Schema::hasTable('aprendices') && $eventos->isNotEmpty()) {
            $ids = $eventos->pluck('id_evento')->filter()->values();
            // Construir expresión de nombre segura según columnas disponibles
            $hasNombreCompleto = Schema::hasColumn('aprendices', 'nombre_completo');
            $nameExpr = $hasNombreCompleto
                ? 'aprendices.nombre_completo'
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";

            // Determinar columna de unión disponible
            $joinCol = null;
            foreach (['id_usuario','user_id','id_aprendiz','id'] as $cand) {
                if (Schema::hasColumn('aprendices', $cand)) { $joinCol = $cand; break; }
            }
            $rows = DB::table('evento_participantes')
                ->join('aprendices', DB::raw('aprendices.' . $joinCol), '=', 'evento_participantes.id_aprendiz')
                ->whereIn('evento_participantes.id_evento', $ids)
                ->select('evento_participantes.id_evento', DB::raw($nameExpr.' as nombre'))
                ->get();
            $participantesPorEvento = $rows->groupBy('id_evento')->map(function($g){
                return $g->pluck('nombre')->filter()->values()->all();
            });
        }

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
                'lider' => optional($e->lider)->name,
                'proyecto' => $e->proyecto,
                'participantes' => $participantesPorEvento->get($eid, []),
            ];
        });

        return view('aprendiz.calendario.index', compact('reuniones'));
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
                if (!is_null($aprId) && $aprId != $uid) { $aprIds[] = $aprId; }
            }

            $hasEventoUsuario = Schema::hasColumn('eventos','id_usuario');
            $hasEventoLider   = Schema::hasColumn('eventos','id_lider');

            $query = Evento::query()
                ->with(['proyecto:id_proyecto,nombre_proyecto','lider:id,name'])
                ->whereDate('fecha_hora', '>=', now()->startOfDay())
                ->where(function ($q) use ($uid, $hasEventoUsuario, $hasEventoLider, $aprIds) {
                    $q->whereRaw('1=0');
                    if ($hasEventoUsuario) { $q->orWhere('id_usuario', $uid); }
                    if ($hasEventoLider)   { $q->orWhere('id_lider', $uid); }
                    $q->orWhereExists(function ($sub) use ($aprIds) {
                        $sub->from('evento_participantes as ep')
                            ->whereColumn('ep.id_evento', 'eventos.id_evento')
                            ->whereIn('ep.id_aprendiz', $aprIds);
                    });
                })
                ->orderBy('fecha_hora', 'asc')
                ->select('eventos.*')
                ->take(10);

            $eventos = $query->get()->unique('id_evento')->values();

            // Participantes por evento (nombres) con join dinámico
            $participantesPorEvento = collect();
            if (Schema::hasTable('evento_participantes') && Schema::hasTable('aprendices') && $eventos->isNotEmpty()) {
                $ids = $eventos->pluck('id_evento')->filter()->values();
                $hasNombreCompleto = Schema::hasColumn('aprendices', 'nombre_completo');
                $nameExpr = $hasNombreCompleto
                    ? 'aprendices.nombre_completo'
                    : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";

                $joinCol = null;
                foreach (['id_usuario','user_id','id_aprendiz','id'] as $cand) {
                    if (Schema::hasColumn('aprendices', $cand)) { $joinCol = $cand; break; }
                }
                if ($joinCol) {
                    $rows = DB::table('evento_participantes')
                        ->join('aprendices', DB::raw('aprendices.' . $joinCol), '=', 'evento_participantes.id_aprendiz')
                        ->whereIn('evento_participantes.id_evento', $ids)
                        ->select('evento_participantes.id_evento', DB::raw($nameExpr.' as nombre'))
                        ->get();
                    $participantesPorEvento = $rows->groupBy('id_evento')->map(fn($g) => $g->pluck('nombre')->filter()->values()->all());
                }
            }

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
                    'lider' => optional($e->lider)->name,
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
}
