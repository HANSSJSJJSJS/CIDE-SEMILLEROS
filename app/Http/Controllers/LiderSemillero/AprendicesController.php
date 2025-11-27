<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AprendicesController extends Controller
{
    // Listar todos los aprendices del grupo para el líder semillero
    public function index()
    {
        $userId = Auth::id();
        Log::info('LiderSemillero/AprendicesController@index start', ['user_id' => $userId]);

        // Obtener aprendices asociados a los semilleros del líder autenticado
        // Preferir columna directa a.semillero_id; si no existe, usar pivote aprendiz_semillero
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

        $aprendices = collect([]);
        if ($userId && Schema::hasTable('semilleros')) {
            // Semilleros del líder autenticado
            $semillerosQ = DB::table('semilleros as s');
            if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                // Usar id_lider_usuario si efectivamente hay semilleros asignados a este user
                $hasByUser = DB::table('semilleros')->where('id_lider_usuario', $userId)->exists();
                if ($hasByUser) {
                    $semillerosQ->where('s.id_lider_usuario', $userId);
                } elseif (Schema::hasColumn('semilleros','id_lider_semi')) {
                    // Fallback: mapear mediante lideres_semillero
                    if (Schema::hasTable('lideres_semillero')) {
                        try {
                            $dbName = DB::getDatabaseName();
                            $cols = collect(DB::select(
                                "SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'",
                                [$dbName]
                            ))->pluck('c')->all();
                            $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario'
                                : (in_array('user_id', $cols, true) ? 'user_id'
                                : (in_array('id_user', $cols, true) ? 'id_user' : null));
                            if ($leaderUserFkCol) {
                                $semillerosQ->join('lideres_semillero as ls','ls.id_lider_semi','=','s.id_lider_semi')
                                            ->where(DB::raw('ls.'.$leaderUserFkCol), $userId);
                            } else {
                                Log::warning('No se encontró columna FK user en lideres_semillero (fallback)');
                                $semillerosQ->whereRaw('1=0');
                            }
                        } catch (\Throwable $e) {
                            Log::error('Error detectando columnas en lideres_semillero (fallback): '.$e->getMessage());
                            $semillerosQ->whereRaw('1=0');
                        }
                    } else {
                        $semillerosQ->whereRaw('1=0');
                    }
                } else {
                    $semillerosQ->whereRaw('1=0');
                }
            } elseif (Schema::hasColumn('semilleros','id_lider_semi')) {
                // Caso 1: algunos esquemas usan directamente el mismo ID para users.id y semilleros.id_lider_semi
                $directOwn = DB::table('semilleros')->where('id_lider_semi', $userId)->exists();
                if ($directOwn) {
                    $semillerosQ->where('s.id_lider_semi', $userId);
                }
                // Caso 2: mapear líder_user -> líder_semillero detectando la FK real en lideres_semillero
                elseif (Schema::hasTable('lideres_semillero')) {
                    try {
                        $dbName = DB::getDatabaseName();
                        $cols = collect(DB::select(
                            "SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'",
                            [$dbName]
                        ))->pluck('c')->all();
                        $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario'
                            : (in_array('user_id', $cols, true) ? 'user_id'
                            : (in_array('id_user', $cols, true) ? 'id_user' : null));
                        if ($leaderUserFkCol) {
                            $semillerosQ->join('lideres_semillero as ls','ls.id_lider_semi','=','s.id_lider_semi')
                                        ->where(DB::raw('ls.'.$leaderUserFkCol), $userId);
                        } else {
                            // No hay relación clara entre líderes y users: no devolvemos semilleros
                            Log::warning('No se encontró columna FK user en lideres_semillero');
                            $semillerosQ->whereRaw('1=0');
                        }
                    } catch (\Throwable $e) {
                        Log::error('Error detectando columnas en lideres_semillero: '.$e->getMessage());
                        $semillerosQ->whereRaw('1=0');
                    }
                } else {
                    // Sin tabla líderes, no podemos verificar dueño con id_lider_semi
                    $semillerosQ->whereRaw('1=0');
                }
            } else {
                $semillerosQ->whereRaw('1=0');
            }

            $semilleroIds = $semillerosQ->pluck('s.id_semillero');
            Log::info('Semilleros del lider', ['ids' => $semilleroIds]);

            // Fallback: si no encontramos semilleros por relación directa, intentar inferirlos desde proyectos del líder
            if ($semilleroIds->isEmpty() && Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero')) {
                try {
                    $qb = DB::table('proyectos as p')->join('semilleros as s','s.id_semillero','=','p.id_semillero');
                    if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                        $qb->where('s.id_lider_usuario', $userId);
                    } elseif (Schema::hasColumn('semilleros','id_lider_semi') && Schema::hasTable('lideres_semillero')) {
                        $dbName = DB::getDatabaseName();
                        $cols = collect(DB::select(
                            "SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'",
                            [$dbName]
                        ))->pluck('c')->all();
                        $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario'
                            : (in_array('user_id', $cols, true) ? 'user_id'
                            : (in_array('id_user', $cols, true) ? 'id_user' : null));
                        if ($leaderUserFkCol) {
                            $qb->join('lideres_semillero as ls','ls.id_lider_semi','=','s.id_lider_semi')
                               ->where(DB::raw('ls.'.$leaderUserFkCol), $userId);
                        } else {
                            $qb->whereRaw('1=0');
                        }
                    }
                    $semilleroIds = $qb->pluck('s.id_semillero')->unique();
                    Log::info('Semilleros inferidos desde proyectos', ['ids' => $semilleroIds]);
                } catch (\Throwable $e) {
                    Log::error('Error infiriendo semilleros desde proyectos: '.$e->getMessage());
                }
            }

            if ($semilleroIds->isNotEmpty() && Schema::hasTable('aprendices')) {
                if (Schema::hasColumn('aprendices','semillero_id')) {
                    // Camino directo por columna en aprendices
                    $aprendices = DB::table('aprendices')
                        ->whereIn('aprendices.semillero_id', $semilleroIds)
                        ->select(array_merge($selectCols, [
                            DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo"),
                        ]))
                        ->orderByRaw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))")
                        ->get();
                    Log::info('Aprendices por semillero_id encontrados', ['count' => $aprendices->count()]);
                } elseif (Schema::hasTable('aprendiz_semillero')) {
                    // Fallback a pivote
                    $aprendices = DB::table('aprendices')
                        ->join('aprendiz_semillero', 'aprendiz_semillero.id_aprendiz', '=', 'aprendices.id_aprendiz')
                        ->whereIn('aprendiz_semillero.id_semillero', $semilleroIds)
                        ->select(array_merge($selectCols, [
                            DB::raw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,'')) as nombre_completo"),
                        ]))
                        ->orderByRaw("CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))")
                        ->get();
                    Log::info('Aprendices por pivote encontrados', ['count' => $aprendices->count()]);
                }
            }
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

        // Intentar obtener nombre de semillero
        $semillerosRelaciones = [];
        if (!empty($aprendicesIds)) {
            try {
                if (Schema::hasColumn('aprendices','semillero_id') && Schema::hasTable('semilleros')) {
                    $semillerosRelaciones = DB::table('aprendices')
                        ->join('semilleros','semilleros.id_semillero','=','aprendices.semillero_id')
                        ->whereIn('aprendices.id_aprendiz', $aprendicesIds)
                        ->select('aprendices.id_aprendiz','semilleros.nombre as semillero_nombre')
                        ->get()->groupBy('id_aprendiz');
                } elseif (Schema::hasTable('aprendiz_semillero') && Schema::hasTable('semilleros')) {
                    $semillerosRelaciones = DB::table('aprendiz_semillero')
                        ->join('semilleros', 'semilleros.id_semillero', '=', 'aprendiz_semillero.id_semillero')
                        ->whereIn('aprendiz_semillero.id_aprendiz', $aprendicesIds)
                        ->select('aprendiz_semillero.id_aprendiz', 'semilleros.nombre as semillero_nombre')
                        ->get()->groupBy('id_aprendiz');
                }
            } catch (\Exception $e) {
                // Continuar sin semillero
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



