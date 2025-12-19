<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use App\Models\Semillero;
use App\Models\Aprendiz;
use App\Models\Documento;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class DashboardController_semi extends Controller
{
    public function index()
    {
        // Usuario autenticado como "líder" para el dashboard
        $lider = Auth::user();
        // Mantener compat si alguna vista/fragmento espera $lideruser
        $lideruser = $lider;
        $userId = Auth::id();

        // Construir consulta de semilleros de forma compatible con diferentes esquemas
        $query = Semillero::query();

        $applyActivoSemillero = function ($q) {
            if (Schema::hasColumn('semilleros', 'estado')) {
                // Considerar activo todo lo que NO sea finalizado/inactivo/cancelado (soporta variantes)
                $q->whereNotIn(DB::raw('TRIM(UPPER(estado))'), ['FINALIZADO', 'INACTIVO', 'CANCELADO']);
            }
            return $q;
        };
        // Detectar columna de líder en semilleros: id_lider_usuario | id_lider_semi | lider_id
        $hasLeaderFilter = false;
        if (Schema::hasColumn('semilleros', 'id_lider_usuario')) {
            $query->where('id_lider_usuario', $userId);
            $hasLeaderFilter = true;
        } elseif (Schema::hasColumn('semilleros', 'id_lider_semi')) {
            $query->where('id_lider_semi', $userId);
            $hasLeaderFilter = true;
        } elseif (Schema::hasColumn('semilleros', 'lider_id')) {
            $query->where('lider_id', $userId);
            $hasLeaderFilter = true;
        }
        $applyActivoSemillero($query);

        // Evitar traer todos los semilleros si el esquema no tiene una columna directa de líder
        $semilleros = $hasLeaderFilter ? $query->get() : collect();

        // Resolver IDs de semilleros del líder (según PK real).
        // Si no hay semilleros asignados directamente, intentar usar la tabla lideres_semillero.
        $semilleroIds = [];
        if ($semilleros->isNotEmpty()) {
            $semPk = Schema::hasColumn('semilleros', 'id_semillero') ? 'id_semillero' : 'id';
            $semilleroIds = $semilleros->pluck($semPk)->all();
        } elseif (Schema::hasTable('lideres_semillero') && Schema::hasColumn('lideres_semillero', 'id_semillero')) {
            $rawIds = DB::table('lideres_semillero')
                ->where('id_lider_semi', $userId)
                ->whereNotNull('id_semillero')
                ->pluck('id_semillero')
                ->all();

            if (!empty($rawIds)) {
                $semPk = Schema::hasColumn('semilleros', 'id_semillero') ? 'id_semillero' : 'id';
                $semQ = DB::table('semilleros')->whereIn($semPk, $rawIds);
                if (Schema::hasColumn('semilleros', 'estado')) {
                    $semQ->whereNotIn(DB::raw('TRIM(UPPER(estado))'), ['FINALIZADO', 'INACTIVO', 'CANCELADO']);
                }
                $semilleros = $semQ->get();
                $semilleroIds = $semilleros->pluck($semPk)->all();
            }
        }

        // Proyectos activos del líder: en esta vista se interpretan como semilleros activos asignados al líder
        $proyectosActivos = 0;
        if (!empty($semilleroIds)) {
            $proyectosActivos = count($semilleroIds);
        }

        // Total de aprendices: usar relación directa aprendices.semillero_id
        $totalAprendices = 0;
        if (!empty($semilleroIds) && Schema::hasTable('aprendices') && Schema::hasColumn('aprendices', 'semillero_id')) {
            $totalAprendices = DB::table('aprendices')
                ->whereIn('semillero_id', $semilleroIds)
                ->count();
        }

        // Documentos pendientes y revisados (toda la tabla 'documentos')
        $documentosPendientes = 0;
        $documentosRevisados = 0;
        if (Schema::hasTable('documentos')) {
            $docsBase = DB::table('documentos');

            if (Schema::hasColumn('documentos', 'estado')) {
                // Pendientes: estado NULL, vacío o 'pendiente' (ignorando espacios y mayúsculas)
                $documentosPendientes = (clone $docsBase)
                    ->where(function ($q) {
                        $q->whereNull('estado')
                          ->orWhere('estado', '=','')
                          ->orWhere(DB::raw("TRIM(UPPER(estado))"), 'PENDIENTE');
                    })
                    ->count();

                // Revisados: 'aprobado' o 'rechazado' (ignorando espacios y mayúsculas)
                $documentosRevisados = (clone $docsBase)
                    ->whereIn(DB::raw("TRIM(UPPER(estado))"), ['APROBADO', 'RECHAZADO'])
                    ->count();
            } else {
                // Si no hay columna estado, considerar todos como "revisados" para no dejar en cero
                $totalDocs = $docsBase->count();
                $documentosRevisados = $totalDocs;
                $documentosPendientes = 0;
            }
        }

        // Progreso promedio: calcular desde semilleros si existe la columna
        $progresoPromedio = 0;
        if ($semilleros->isNotEmpty() && Schema::hasColumn('semilleros', 'progreso')) {
            $progresoPromedio = (int) round($semilleros->avg('progreso'));
        }

        // Actividad reciente (mock)
        $actividadReciente = $this->obtenerActividadReciente();

        return view('lider_semi.dashboard_lider_semi', compact(
            'lider',
            'semilleros',
            'proyectosActivos',
            'totalAprendices',
            'documentosPendientes',
            'documentosRevisados',
            'progresoPromedio',
            'actividadReciente'
        ));
    }

    private function obtenerActividadReciente()
    {
        return [
            [
                'tipo' => 'nuevo_aprendiz',
                'titulo' => 'María González se unió al Semillero de IA',
                'descripcion' => 'Nuevo aprendiz registrado en el grupo',
                'tiempo' => 'Hace 5 minutos'
            ],
            [
                'tipo' => 'documento',
                'titulo' => 'Carlos Pérez subió un documento',
                'descripcion' => 'Proyecto Final - Desarrollo Web.pdf',
                'tiempo' => 'Hace 1 hora'
            ],
        ];
    }
}
