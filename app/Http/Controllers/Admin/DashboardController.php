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
            // ---- Helpers de columnas (detecta PK/nombre dinámicamente)
            $semPK   = Schema::hasColumn('semilleros', 'id') ? 'id' :
                       (Schema::hasColumn('semilleros', 'id_semillero') ? 'id_semillero' : 'id');
            $semName = Schema::hasColumn('semilleros', 'nombre') ? 'nombre' :
                       (Schema::hasColumn('semilleros', 'name') ? 'name' : 'nombre');

            $apPK    = Schema::hasColumn('aprendices', 'id') ? 'id' :
                       (Schema::hasColumn('aprendices', 'id_aprendiz') ? 'id_aprendiz' : 'id');

            // ---- a) Aprendices por semillero (Top 10)
            // Usa pivote si existe; si no, intenta con FK directa aprendices.semillero_id
            if (Schema::hasTable('aprendiz_semillero')) {
                // Asumo columnas estándar de la pivote: id_semillero, id_aprendiz
                $aprPorSem = DB::table('semilleros as s')
                    ->leftJoin('aprendiz_semillero as ps', "ps.id_semillero", '=', DB::raw("s.$semPK"))
                    ->leftJoin('aprendices as a', 'a.' . $apPK, '=', 'ps.id_aprendiz')
                    ->select("s.$semName as semillero", DB::raw("COUNT(a.$apPK) as total"))
                    ->groupBy("s.$semPK", "s.$semName")
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get();
            } else {
                // FK directa
                $fkSemillero = Schema::hasColumn('aprendices', 'semillero_id') ? 'semillero_id' :
                               (Schema::hasColumn('aprendices', 'id_semillero') ? 'id_semillero' : null);

                if (!$fkSemillero) {
                    throw new \RuntimeException('No existe pivote "aprendiz_semillero" ni FK en aprendices hacia semilleros.');
                }

                $aprPorSem = DB::table('semilleros as s')
                    ->leftJoin('aprendices as a', "a.$fkSemillero", '=', DB::raw("s.$semPK"))
                    ->select("s.$semName as semillero", DB::raw("COUNT(a.$apPK) as total"))
                    ->groupBy("s.$semPK", "s.$semName")
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get();
            }

            // ---- b) Proyectos por estado (con fallback de nombre de columna)
            $colEstado = Schema::hasColumn('proyectos', 'estado') ? 'estado' :
                         (Schema::hasColumn('proyectos', 'status') ? 'status' : null);

            if ($colEstado) {
                $projEstado = DB::table('proyectos')
                    ->select($colEstado . ' as estado', DB::raw('COUNT(*) as total'))
                    ->groupBy($colEstado)
                    ->get();
            } else {
                $projEstado = collect([ (object)['estado' => 'N/A', 'total' => DB::table('proyectos')->count()] ]);
            }

            // ---- c) Proyectos creados por mes (últimos 12)
            $desde = Carbon::now()->startOfMonth()->subMonths(11);
            $crCol = Schema::hasColumn('proyectos', 'created_at') ? 'created_at' :
                     (Schema::hasColumn('proyectos', 'fecha_creacion') ? 'fecha_creacion' : null);
            if (!$crCol) {
                throw new \RuntimeException('No encuentro columna de fecha en proyectos (created_at/fecha_creacion).');
            }

            $projMesRaw = DB::table('proyectos')
                ->selectRaw("DATE_FORMAT($crCol, '%Y-%m') as ym, COUNT(*) as total")
                ->where($crCol, '>=', $desde)
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->pluck('total', 'ym')
                ->all();

            $labelsMes = [];
            $dataMes   = [];
            for ($i = 0; $i < 12; $i++) {
                $key = Carbon::now()->startOfMonth()->subMonths(11 - $i)->format('Y-m');
                $labelsMes[] = $key;
                $dataMes[]   = $projMesRaw[$key] ?? 0;
            }

            // ---- d) Recursos por categoría (fallback)
            $colCat = Schema::hasColumn('recursos', 'categoria') ? 'categoria' :
                      (Schema::hasColumn('recursos', 'category') ? 'category' : null);

            if ($colCat) {
                $recCat = DB::table('recursos')
                    ->select($colCat . ' as categoria', DB::raw('COUNT(*) as total'))
                    ->groupBy($colCat)
                    ->get();
            } else {
                $recCat = collect([]);
            }

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

        } catch (\Throwable $e) {
            Log::error('Dashboard charts error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Devolver SIEMPRE JSON para que el front no intente parsear HTML
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
