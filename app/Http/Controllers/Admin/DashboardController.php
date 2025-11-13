<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        // Ajusta nombres de tablas si difieren
        $semilleros = DB::table('semilleros')->count();

        // FIX: role correcto para líderes
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

    // ===== Datos para las 4 gráficas ========================================
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
        //    - nombre del líder (users)
        //    - línea asignada (semilleros.linea_investigacion)
        //    - última actividad (sessions.last_activity)
        // ============================================================
        $lideresRaw = DB::table('users as u')
            ->where('u.role', 'LIDER_SEMILLERO')
            ->leftJoin('semilleros as s', 's.id_lider_semi', '=', 'u.id')
            ->leftJoin('sessions as se', 'se.user_id', '=', 'u.id')
            ->select(
                'u.id',
                'u.name',
                'u.apellidos',
                's.linea_investigacion',
                DB::raw('MAX(se.last_activity) as last_activity')
            )
            ->groupBy('u.id', 'u.name', 'u.apellidos', 's.linea_investigacion')
            ->orderBy('u.name')
            ->get();

        $actividadLideres = $lideresRaw->map(function ($l) {
            $last = $l->last_activity
                ? Carbon::createFromTimestamp($l->last_activity)
                : null;

            return [
                'lider'             => trim($l->name . ' ' . ($l->apellidos ?? '')),
                'linea'             => $l->linea_investigacion,
                'last_login'        => $last ? $last->format('Y-m-d H:i') : null,
                'last_login_humano' => $last ? $last->diffForHumans() : 'Sin actividad registrada',
            ];
        })->values(); // reindex

        return response()->json([
            'tablaAprendicesSem' => $tablaAprendicesSem,
            'tablaProyectosSem'  => $tablaProyectosSem,
            'actividadLideres'   => $actividadLideres,
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'error'   => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}



}
