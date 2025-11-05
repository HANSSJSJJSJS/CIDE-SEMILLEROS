<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Vista del dashboard
        return view('admin.dashboard.index');
    }

    // Tarjetas KPI
    public function stats()
    {
        // Ajusta nombres de tablas si difieren
        $semilleros = DB::table('semilleros')->count();
        $lideres    = DB::table('users')->where('role', 'LIDER')->count();       // si usas otra forma de roles, ajusta
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

    // Datos para las 4 gráficas
    public function charts()
    {
        // a) Aprendices por semillero (asume columna aprendices.semillero_id)
        // Si usas tabla pivote, cambia a join con la pivote.
        $aprPorSem = DB::table('semilleros as s')
            ->leftJoin('aprendices as a', 'a.semillero_id', '=', 's.id')
            ->select('s.nombre as semillero', DB::raw('COUNT(a.id) as total'))
            ->groupBy('s.id', 's.nombre')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // b) Proyectos por estado
        $projEstado = DB::table('proyectos')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        // c) Proyectos creados por mes (últimos 12 meses)
        $desde = now()->startOfMonth()->subMonths(11);
        $projMes = DB::table('proyectos')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as total')
            ->where('created_at', '>=', $desde)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->pluck('total', 'ym')
            ->all();

        // completar meses faltantes con 0
        $labelsMes = [];
        $dataMes   = [];
        for ($i = 0; $i < 12; $i++) {
            $key = now()->startOfMonth()->subMonths(11 - $i)->format('Y-m');
            $labelsMes[] = $key;
            $dataMes[]   = $projMes[$key] ?? 0;
        }

        // d) Recursos por categoría
        $recCat = DB::table('recursos')
            ->select('categoria', DB::raw('COUNT(*) as total'))
            ->groupBy('categoria')
            ->get();

        return response()->json([
            'aprendicesPorSemillero' => [
                'labels' => $aprPorSem->pluck('semillero'),
                'data'   => $aprPorSem->pluck('total'),
            ],
            'proyectosPorEstado' => [
                'labels' => $projEstado->pluck('estado'),
                'data'   => $projEstado->pluck('total'),
            ],
            'proyectosPorMes' => [
                'labels' => $labelsMes, // YYYY-MM
                'data'   => $dataMes,
            ],
            'recursosPorCategoria' => [
                'labels' => $recCat->pluck('categoria'),
                'data'   => $recCat->pluck('total'),
            ],
        ]);
    }
}
