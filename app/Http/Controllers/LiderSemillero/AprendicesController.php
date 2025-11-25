<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AprendicesController extends Controller
{
    // Listar todos los aprendices del grupo para el líder semillero
    public function index()
    {
        $userId = Auth::id();

        // Obtener aprendices asociados a los semilleros del líder autenticado
        // usando la tabla pivote aprendiz_semillero y la columna semilleros.id_lider_semi
        $selectCols = [
            'aprendices.id_aprendiz',
            'aprendices.tipo_documento',
            'aprendices.documento',
            'aprendices.celular',
            'aprendices.correo_institucional',
            'aprendices.correo_personal',
            'aprendices.programa',
            'aprendices.ficha',
            'aprendices.contacto_nombre',
            'aprendices.contacto_celular',
        ];

        if (Schema::hasColumn('aprendices', 'id_usuario')) {
            $selectCols[] = 'aprendices.id_usuario';
        }

        // Si no tenemos la estructura mínima, no listamos aprendices
        if (!Schema::hasTable('aprendiz_semillero') || !Schema::hasTable('semilleros') || !$userId) {
            $aprendices = collect([]);
        } else {
            // Obtener solo aprendices vinculados a semilleros del líder
            $queryAprendices = DB::table('aprendices')
                ->join('aprendiz_semillero', 'aprendiz_semillero.id_aprendiz', '=', 'aprendices.id_aprendiz')
                ->join('semilleros', 'semilleros.id_semillero', '=', 'aprendiz_semillero.id_semillero')
                ->where(function ($q) use ($userId) {
                    if (Schema::hasColumn('semilleros', 'id_lider_semi')) {
                        $q->orWhere('semilleros.id_lider_semi', $userId);
                    }
                    if (Schema::hasColumn('semilleros', 'id_lider_usuario')) {
                        $q->orWhere('semilleros.id_lider_usuario', $userId);
                    }
                })
                ->select(array_merge($selectCols, [
                    DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo"),
                ]))
                ->orderByRaw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))");

            $aprendices = $queryAprendices->get();
        }

        $aprendicesIds = $aprendices->pluck('id_aprendiz')->toArray();

        // Intentar obtener proyectos asignados
        $proyectosRelaciones = [];
        if (Schema::hasTable('proyectos') && !empty($aprendicesIds)) {
            // Detección dinámica de la tabla pivote proyecto-aprendiz
            $pivotTable = null;
            $pivotProjCol = 'id_proyecto';
            $pivotAprCol = 'id_aprendiz';

            if (Schema::hasTable('proyecto_aprendiz')) {
                $pivotTable = 'proyecto_aprendiz';
            } elseif (Schema::hasTable('proyecto_user')) {
                $pivotTable = 'proyecto_user';
                $pivotAprCol = 'user_id';
            }

            if ($pivotTable) {
                try {
                    $aprColJoin = Schema::hasColumn('aprendices', 'id_usuario') ? 'id_usuario' : 'id_aprendiz';

                    $proyectosRelaciones = DB::table($pivotTable)
                        ->join('proyectos', 'proyectos.id_proyecto', '=', DB::raw($pivotTable.'.'.$pivotProjCol))
                        ->join('aprendices', function ($join) use ($pivotTable, $pivotAprCol, $aprColJoin) {
                            $join->on(DB::raw('aprendices.'.$aprColJoin), '=', DB::raw($pivotTable.'.'.$pivotAprCol));
                        })
                        ->whereIn(DB::raw('aprendices.'.$aprColJoin), $aprendicesIds)
                        ->select(
                            DB::raw('aprendices.'.$aprColJoin.' as id_aprendiz'),
                            DB::raw('COALESCE(proyectos.nombre_proyecto, "Proyecto") as proyecto_nombre')
                        )
                        ->get()
                        ->groupBy('id_aprendiz');
                } catch (\Exception $e) {
                    // Si falla, continuar sin proyectos
                }
            }
        }

        // Intentar obtener semilleros si existe la tabla pivote
        $semillerosRelaciones = [];
        if (Schema::hasTable('aprendiz_semillero') && Schema::hasTable('semilleros') && !empty($aprendicesIds)) {
            try {
                $semillerosRelaciones = DB::table('aprendiz_semillero')
                    ->join('semilleros', 'semilleros.id_semillero', '=', 'aprendiz_semillero.id_semillero')
                    ->where('semilleros.id_lider_semi', $userId)
                    ->whereIn('aprendiz_semillero.id_aprendiz', $aprendicesIds)
                    ->select('aprendiz_semillero.id_aprendiz', 'semilleros.nombre as semillero_nombre')
                    ->get()
                    ->groupBy('id_aprendiz');
            } catch (\Exception $e) {
                // Si falla, continuar sin semilleros
            }
        }

        // Si no se encontró ningún aprendiz por la relación pivote, intentar un fallback
        if ($aprendices->isEmpty()) {
            // Fallback 1: usar la FK semillero_id en aprendices si existe y hay semilleros del líder
            if (Schema::hasTable('semilleros') && Schema::hasColumn('aprendices', 'semillero_id')) {
                $semillerosLider = DB::table('semilleros')
                    ->when(Schema::hasColumn('semilleros', 'id_lider_semi'), function ($q) use ($userId) {
                        $q->orWhere('id_lider_semi', $userId);
                    })
                    ->when(Schema::hasColumn('semilleros', 'id_lider_usuario'), function ($q) use ($userId) {
                        $q->orWhere('id_lider_usuario', $userId);
                    })
                    ->pluck('id_semillero')
                    ->all();

                if (!empty($semillerosLider)) {
                    $aprendices = DB::table('aprendices')
                        ->whereIn('semillero_id', $semillerosLider)
                        ->select(array_merge($selectCols, [
                            DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo"),
                        ]))
                        ->orderByRaw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))")
                        ->get();
                    $aprendicesIds = $aprendices->pluck('id_aprendiz')->toArray();
                }
            }

            // Fallback 2: si aún no hay aprendices, listar algunos sin filtro para no dejar el módulo vacío
            if ($aprendices->isEmpty() && Schema::hasTable('aprendices')) {
                $aprendices = DB::table('aprendices')
                    ->select(array_merge($selectCols, [
                        DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo"),
                    ]))
                    ->orderByRaw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))")
                    ->limit(50)
                    ->get();
                $aprendicesIds = $aprendices->pluck('id_aprendiz')->toArray();
            }
        }

        // Asignar proyectos y semilleros a cada aprendiz
        $aprendices->transform(function ($ap) use ($proyectosRelaciones, $semillerosRelaciones) {
            // Asignar proyecto
            if (isset($proyectosRelaciones[$ap->id_aprendiz]) && $proyectosRelaciones[$ap->id_aprendiz]->isNotEmpty()) {
                $ap->proyecto_nombre = $proyectosRelaciones[$ap->id_aprendiz]->first()->proyecto_nombre;
            } else {
                $ap->proyecto_nombre = 'Sin asignar';
            }

            // Asignar semillero
            if (isset($semillerosRelaciones[$ap->id_aprendiz]) && $semillerosRelaciones[$ap->id_aprendiz]->isNotEmpty()) {
                $ap->semillero_nombre = $semillerosRelaciones[$ap->id_aprendiz]->first()->semillero_nombre;
            } else {
                $ap->semillero_nombre = 'Sin asignar';
            }

            $ap->estado = 'Activo';
            return $ap;
        });

        return view('lider_semi.aprendices', compact('aprendices'));
    }
}
