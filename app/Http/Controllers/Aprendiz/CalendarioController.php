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

        // Proyectos asignados al usuario (relación ya usada en otros módulos)
        $proyectos = method_exists($user, 'proyectos') ? ($user->proyectos ?? collect()) : collect();
        $proyectosIds = $proyectos->pluck('id_proyecto')->filter()->values();

        // Traer eventos:
        // - donde el evento registre id_usuario = usuario actual
        // - o el usuario sea participante vía aprendices.id_usuario (o, si existe, por id_aprendiz)
        // - o el evento esté asociado a alguno de sus proyectos
        $query = Evento::query()->with([
            'proyecto:id_proyecto,nombre_proyecto',
            'lider:id,name'
        ]);

        $uid = $user->id;

        $query->where(function ($q) use ($uid, $proyectosIds) {
            $q->where('id_usuario', $uid);
            if ($proyectosIds->isNotEmpty()) {
                $q->orWhereIn('id_proyecto', $proyectosIds);
            }
        })->orWhereHas('participantes', function ($p) use ($uid) {
            // Filtrar por el valor en la pivote: evento_participantes.id_aprendiz (almacenamos id_usuario del aprendiz)
            $p->where('evento_participantes.id_aprendiz', $uid);
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

            $rows = DB::table('evento_participantes')
                ->join('aprendices', 'aprendices.id_usuario', '=', 'evento_participantes.id_aprendiz')
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

    // Próximas 5 reuniones del aprendiz para el dashboard
    public function proximasReuniones()
    {
        try {
            $user = Auth::user();
            if (!$user || !Schema::hasTable('eventos')) {
                return response()->json(['proximas' => []]);
            }

            $uid = $user->id;
            // Determinar posibles IDs almacenados en la pivote (id_usuario o id_aprendiz)
            $aprIds = [$uid];
            if (Schema::hasTable('aprendices')) {
                try {
                    $aprRow = DB::table('aprendices')->where('id_usuario', $uid)->first();
                    if ($aprRow && property_exists($aprRow, 'id_aprendiz') && !empty($aprRow->id_aprendiz) && $aprRow->id_aprendiz != $uid) {
                        $aprIds[] = $aprRow->id_aprendiz;
                    }
                } catch (\Throwable $ex) {
                    // continuar con $aprIds por defecto
                }
            }

            $query = Evento::query()
                ->with(['proyecto:id_proyecto,nombre_proyecto','lider:id,name'])
                ->whereDate('fecha_hora', '>=', now()->startOfDay())
                ->where(function ($q) use ($uid) {
                    $q->where('id_usuario', $uid)
                      ->orWhere('id_lider', $uid)
                      ->orWhereExists(function ($sub) use ($uid) {
                          $sub->from('evento_participantes as ep')
                              ->whereColumn('ep.id_evento', 'eventos.id_evento')
                              ->where('ep.id_aprendiz', $uid);
                      });
                })
                ->orderBy('fecha_hora', 'asc')
                ->select('eventos.*')
                ->take(10);

            $eventos = $query->get()->unique('id_evento')->values();

            // Participantes por evento (nombres)
            $participantesPorEvento = collect();
            if (Schema::hasTable('evento_participantes') && Schema::hasTable('aprendices') && $eventos->isNotEmpty()) {
                $ids = $eventos->pluck('id_evento')->filter()->values();
                $hasNombreCompleto = Schema::hasColumn('aprendices', 'nombre_completo');
                $nameExpr = $hasNombreCompleto
                    ? 'aprendices.nombre_completo'
                    : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";
                $rows = DB::table('evento_participantes')
                    ->join('aprendices', 'aprendices.id_usuario', '=', 'evento_participantes.id_aprendiz')
                    ->whereIn('evento_participantes.id_evento', $ids)
                    ->select('evento_participantes.id_evento', DB::raw($nameExpr.' as nombre'))
                    ->get();
                $participantesPorEvento = $rows->groupBy('id_evento')->map(fn($g) => $g->pluck('nombre')->filter()->values()->all());
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
