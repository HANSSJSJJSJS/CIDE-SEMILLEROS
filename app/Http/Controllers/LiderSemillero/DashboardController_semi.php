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

class DashboardController_semi extends Controller
{
    public function index()
    {
        // Usuario autenticado como "líder" para el dashboard
        $lider = auth()->user();
        $userId = auth()->id();

        // Construir consulta de semilleros de forma compatible con diferentes esquemas
        $query = Semillero::query();
        if (Schema::hasColumn('semilleros', 'lider_id')) {
            $query->where('lider_id', $userId);
        }
        if (Schema::hasColumn('semilleros', 'estado')) {
            $query->whereIn('estado', ['Activo', 'ACTIVO']);
        }

        // Evitar withCount si la relación no existe en el modelo o la tabla correspondiente
        $semilleros = $query->get();

        // Totales seguros con validaciones de tablas/columnas
        $totalAprendices = 0;
        if (Schema::hasTable('grupo_aprendices') && Schema::hasTable('grupos') && Schema::hasTable('proyectos')) {
            // Intento de conteo si existe el esquema de grupos/proyectos
            $proyectoIds = [];
            if (Schema::hasColumn('proyectos', 'id_semillero') && $semilleros->isNotEmpty()) {
                $ids = $semilleros->pluck('id')->all();
                $proyectoIds = DB::table('proyectos')
                    ->whereIn('id_semillero', $ids)
                    ->pluck('id_proyecto')
                    ->all();
            }
            if (!empty($proyectoIds)) {
                $q = DB::table('grupo_aprendices')
                    ->join('grupos', 'grupo_aprendices.id_grupo', '=', 'grupos.id_grupo')
                    ->whereIn('grupos.id_proyecto', $proyectoIds)
                    ->where('grupo_aprendices.activo', 1);
                // Si existe id_usuario distinto/único
                $totalAprendices = Schema::hasColumn('grupo_aprendices', 'id_usuario')
                    ? $q->distinct('grupo_aprendices.id_usuario')->count('grupo_aprendices.id_usuario')
                    : $q->count();
            }
        }

        // Documentos pendientes si existe el esquema
        $documentosPendientes = 0;
        if (class_exists(Documento::class) && Schema::hasTable('documentos')) {
            $documentosQuery = Documento::query();
            if (Schema::hasColumn('documentos', 'estado')) {
                $documentosQuery->where(function($q) {
                    $q->whereNull('estado')->orWhere('estado', 'PENDIENTE');
                });
            }
            $documentosPendientes = $documentosQuery->count();
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
            'totalAprendices',
            'documentosPendientes',
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
