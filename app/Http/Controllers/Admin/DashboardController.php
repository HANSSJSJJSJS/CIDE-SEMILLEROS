<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index');
    }

    // ===== Tarjetas KPI ======================================================
    public function stats()
    {
        $semilleros = DB::table('semilleros')->count();
        $lideres    = DB::table('users')->where('role', 'LIDER_SEMILLERO')->count();
        $aprendices = DB::table('aprendices')->count();
        $proyectos  = DB::table('proyectos')->count();
        $recursos   = DB::table('recursos')->count();

        return response()->json([
            'semilleros' => $semilleros,
            'lideres'    => $lideres,
            'aprendices' => $aprendices,
            'proyectos'  => $proyectos,
            'recursos'   => $recursos,
        ]);
    }

    // ===== Datos para las tablas / gráficas ==================================
    public function charts()
    {
        try {

            // ============================================================
            // 1) TABLA — Aprendices por semillero
            // ============================================================
            $tablaAprendicesSem = DB::table('semilleros as s')
                ->leftJoin('aprendices as a', 'a.semillero_id', '=', 's.id_semillero')
                ->select(
                    's.id_semillero',
                    's.nombre as semillero',
                    DB::raw('COUNT(a.id_aprendiz) as total_aprendices')
                )
                ->groupBy('s.id_semillero', 's.nombre')
                ->orderBy('s.nombre', 'asc')
                ->get();

            // ============================================================
            // 2) TABLA — Proyectos por semillero (últimos 5)
            // ============================================================
            $semilleros = DB::table('semilleros')
                ->orderBy('nombre')
                ->get();

            $tablaProyectosSem = [];

            foreach ($semilleros as $s) {
                $proyectos = DB::table('proyectos')
                    ->where('id_semillero', $s->id_semillero)
                    ->orderBy('creado_en', 'desc')
                    ->limit(5)
                    ->get();

                $tablaProyectosSem[] = [
                    'semillero' => $s->nombre,
                    'proyectos' => $proyectos->pluck('nombre_proyecto'),
                ];
            }

            // ============================================================
            // 3) TABLA — Actividad de líderes
            // ============================================================
            $lideresRaw = DB::table('users as u')
                ->where('u.role', 'LIDER_SEMILLERO')
                ->leftJoin('semilleros as s', 's.id_lider_semi', '=', 'u.id')
                ->select(
                    'u.id',
                    'u.name',
                    'u.apellidos',
                    's.linea_investigacion',
                    'u.last_login_at'
                )
                ->orderBy('u.name')
                ->get();

            $actividadLideres = $lideresRaw->map(function ($l) {
                $last = $l->last_login_at ? Carbon::parse($l->last_login_at) : null;

                return [
                    'lider'             => trim($l->name . ' ' . ($l->apellidos ?? '')),
                    'linea'             => $l->linea_investigacion,
                    'last_login'        => $last ? $last->format('d/m/Y H:i') : null,
                    'last_login_humano' => $last ? $last->diffForHumans() : 'Sin registro',
                ];
            })->values();

            // ============================================================
            // 🔥 4) ESTADO DE PROYECTOS (para gráfica DONA)
            // ============================================================
            $proyectosEstado = DB::table('proyectos')
                ->select('estado', DB::raw('COUNT(*) as total'))
                ->groupBy('estado')
                ->get();

            // ============================================================
            // 🔥 5) TOP 5 SEMILLEROS CON MÁS PROYECTOS
            // ============================================================
            $topSemilleros = DB::table('semilleros as s')
                ->leftJoin('proyectos as p', 'p.id_semillero', '=', 's.id_semillero')
                ->select('s.nombre', DB::raw('COUNT(p.id_proyecto) as total_proyectos'))
                ->groupBy('s.id_semillero', 's.nombre')
                ->orderByDesc('total_proyectos')
                ->limit(5)
                ->get();

            return response()->json([
                'tablaAprendicesSem' => $tablaAprendicesSem,
                'tablaProyectosSem'  => $tablaProyectosSem,
                'actividadLideres'   => $actividadLideres,

                'proyectosEstado'    => $proyectosEstado,   
                'topSemilleros'      => $topSemilleros,     
            ]);

        } catch (\Throwable $e) {
            Log::error('Error en DashboardController@charts: ' . $e->getMessage());

            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
