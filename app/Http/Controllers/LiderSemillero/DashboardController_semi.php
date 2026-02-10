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
use Carbon\Carbon;

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

        // Actividad reciente real basada en evidencias/documentos de los proyectos
        $actividadReciente = $this->obtenerActividadReciente($semilleroIds);

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

    private function obtenerActividadReciente(array $semilleroIds): array
    {
        // Si no hay semilleros asociados o no existe la tabla de documentos, no mostramos actividad
        if (empty($semilleroIds) || !Schema::hasTable('documentos')) {
            return [];
        }

        // Campos presentes en tu dump: documentos.fecha_subida, documentos.id_aprendiz, documentos.id_proyecto
        // Proyectos -> semilleros.id_semillero para filtrar solo lo del líder actual.
        $query = DB::table('documentos as d')
            ->join('proyectos as p', 'p.id_proyecto', '=', 'd.id_proyecto')
            ->join('aprendices as a', 'a.id_aprendiz', '=', 'd.id_aprendiz')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->whereIn('p.id_semillero', $semilleroIds)
            // Solo considerar entregas reales: que tengan archivo o enlace asociado
            ->where(function ($q) {
                $q->whereNotNull('d.documento')->where('d.documento', '<>', '')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('d.ruta_archivo')->where('d.ruta_archivo', '<>', '');
                  })
                  ->orWhere(function ($q3) {
                      if (Schema::hasColumn('documentos', 'enlace_evidencia')) {
                          $q3->whereNotNull('d.enlace_evidencia')->where('d.enlace_evidencia', '<>', '');
                      }
                  });
            })
            ->select([
                'd.id_documento',
                'd.titulo_avance',
                'd.descripcion_avance',
                'd.documento',
                'd.ruta_archivo',
                'd.tipo_archivo',
                'd.fecha_subida',
                'd.fecha_subido',
                'p.nombre_proyecto',
                'a.id_aprendiz',
                'a.nombres as apr_nombres',
                'a.apellidos as apr_apellidos',
                'a.correo_institucional as apr_correo',
                'u.nombre as usr_nombre',
                'u.apellidos as usr_apellidos',
            ])
            ->orderByDesc(DB::raw('COALESCE(d.fecha_subida, d.fecha_subido, NOW())'))
            ->limit(6);

        $rows = $query->get();

        return $rows->map(function ($row) {
            // Construir nombre del aprendiz: primero desde aprendices, luego desde users, por último correo
            $nombre = trim((string)($row->apr_nombres ?? '').' '.(string)($row->apr_apellidos ?? ''));
            if ($nombre === '') {
                $nombre = trim((string)($row->usr_nombre ?? '').' '.(string)($row->usr_apellidos ?? ''));
            }
            if ($nombre === '') {
                $nombre = $row->apr_correo ?? 'Aprendiz';
            }

            // Texto principal
            $titulo = $nombre.' subió un documento';

            // Descripción: nombre del proyecto + nombre del archivo o título de avance
            $archivo = $row->documento ?: ($row->titulo_avance ?: 'Evidencia subida');
            $descripcion = ($row->nombre_proyecto ?? 'Proyecto')." - ".$archivo;

            // Tiempo relativo usando Carbon
            $fechaRaw = $row->fecha_subida ?? $row->fecha_subido ?? null;
            if ($fechaRaw) {
                $tiempo = Carbon::parse($fechaRaw)->locale('es')->diffForHumans(now(), true).' atrás';
            } else {
                $tiempo = 'Hace poco';
            }

            return [
                'tipo' => 'documento',
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'tiempo' => $tiempo,
            ];
        })->toArray();
    }
}
