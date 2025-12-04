<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProyectoController extends Controller
{
    // Listado JSON de proyectos gestionados por el líder de semillero autenticado
    public function listadoJson(Request $request)
    {
        try {
            $uid = Auth::id();
            if (!$uid) {
                return response()->json(['proyectos' => []]);
            }

            // Proyectos cuyos semilleros pertenecen al líder (esquema tolerante)
            $base = DB::table('proyectos as p')->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero');
            if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                $base->where('s.id_lider_usuario', $uid);
            } elseif (Schema::hasColumn('semilleros','id_lider_semi') || Schema::hasColumn('semilleros','id_lider')) {
                $semCol = Schema::hasColumn('semilleros','id_lider_semi') ? 'id_lider_semi' : 'id_lider';
                // Detectar columnas en lideres_semillero
                $dbName = DB::getDatabaseName();
                $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'", [$dbName]))->pluck('c')->all();
                $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('user_id', $cols, true) ? 'user_id' : (in_array('id_user', $cols, true) ? 'id_user' : null));
                $leaderIdCol = in_array('id_lider_semi', $cols, true) ? 'id_lider_semi' : (in_array('id_lider', $cols, true) ? 'id_lider' : null);
                if ($leaderUserFkCol && $leaderIdCol) {
                    $base->join('lideres_semillero as ls', DB::raw('ls.'.$leaderIdCol), '=', DB::raw('s.'.$semCol))
                         ->where(DB::raw('ls.'.$leaderUserFkCol), $uid);
                }
            } else {
                // No hay columna de líder reconocible: no filtramos para no bloquear
            }
            $proyectos = $base->select(
                    'p.id_proyecto',
                    'p.nombre_proyecto',
                    'p.estado',
                    'p.fecha_inicio',
                    'p.fecha_fin',
                    'p.descripcion'
                )
                ->orderBy('p.creado_en', 'desc')
                ->get();

            // Conteos y KPIs por proyecto
            $ids = $proyectos->pluck('id_proyecto');
            $apxProyecto = collect();
            if ($ids->isNotEmpty()) {
                $apxProyecto = DB::table('proyecto_user')
                    ->select('id_proyecto', DB::raw('COUNT(*) as total'))
                    ->whereIn('id_proyecto', $ids)
                    ->groupBy('id_proyecto')
                    ->pluck('total', 'id_proyecto');

                $docsPend = DB::table('archivos')
                    ->select('proyecto_id as id_proyecto', DB::raw("SUM(CASE WHEN estado='pendiente' THEN 1 ELSE 0 END) as pendientes"))
                    ->whereIn('proyecto_id', $ids)
                    ->groupBy('proyecto_id')
                    ->pluck('pendientes', 'id_proyecto');

                $docsAprob = DB::table('archivos')
                    ->select('proyecto_id as id_proyecto', DB::raw("SUM(CASE WHEN estado='aprobado' THEN 1 ELSE 0 END) as aprobados"))
                    ->whereIn('proyecto_id', $ids)
                    ->groupBy('proyecto_id')
                    ->pluck('aprobados', 'id_proyecto');
            }

            $out = $proyectos->map(function ($p) use ($apxProyecto, $docsPend, $docsAprob) {
                $pid = $p->id_proyecto;
                return [
                    'id' => $pid,
                    'nombre' => $p->nombre_proyecto,
                    'estado' => $p->estado,
                    'fecha_inicio' => $p->fecha_inicio,
                    'fecha_fin' => $p->fecha_fin,
                    'descripcion' => $p->descripcion,
                    'aprendices' => (int)($apxProyecto[$pid] ?? 0),
                    'docs_pendientes' => (int)($docsPend[$pid] ?? 0),
                    'docs_aprobados' => (int)($docsAprob[$pid] ?? 0),
                ];
            });

            return response()->json(['proyectos' => $out]);
        } catch (\Throwable $e) {
            return response()->json(['proyectos' => [], 'error' => $e->getMessage()]);
        }
    }

    // Compat: búsqueda para modal semilleros.blade (respuesta: array plano)
    public function searchAprendices(Request $request, $id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json([], 401);

            $tipo = trim((string)$request->query('tipo', ''));
            // Canonizar tipo (acepta texto completo o códigos)
            $tipoCanon = $tipo;
            if ($tipoCanon !== '') {
                $tn = strtoupper(trim($tipoCanon));
                $tn = strtr($tn, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
                $tn = preg_replace('/[^A-Z0-9 ]+/', ' ', $tn);
                $tn = preg_replace('/\s+/', ' ', $tn);
                $map = [
                    'CC' => ['CC','CEDULA','CEDULA DE CIUDADANIA','CEDULA CIUDADANIA'],
                    'TI' => ['TI','TARJETA','TARJETA DE IDENTIDAD'],
                    'CE' => ['CE','CEDULA DE EXTRANJERIA','CEDULA EXTRANJERIA'],
                    'PAS'=> ['PAS','PASAPORTE'],
                    'PEP'=> ['PEP','PERMISO ESPECIAL','PERMISO'],
                    'RC' => ['RC','REGISTRO CIVIL','REGISTRO'],
                ];
                foreach ($map as $code=>$list) { if (in_array($tn,$list,true)) { $tipoCanon = $code; break; } }
                if ($tipoCanon !== 'CC' && $tipoCanon !== 'TI' && $tipoCanon !== 'CE' && $tipoCanon !== 'PAS' && $tipoCanon !== 'PEP' && $tipoCanon !== 'RC') {
                    if (strpos($tn,'CIUDADAN') !== false) $tipoCanon = 'CC';
                    elseif (strpos($tn,'EXTRANJER') !== false) $tipoCanon = 'CE';
                    elseif (strpos($tn,'PASAPOR') !== false) $tipoCanon = 'PAS';
                    elseif (strpos($tn,'PERMISO') !== false) $tipoCanon = 'PEP';
                    elseif (strpos($tn,'REGISTRO') !== false) $tipoCanon = 'RC';
                    elseif ($tn === 'CEDULA') $tipoCanon = 'CC';
                }
            }
            $num = trim((string)$request->query('num', ''));
            $q = trim((string)$request->query('q', ''));
            // Detectar columna FK hacia users (id_usuario, id_user o user_id) usando information_schema para evitar falsos negativos
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
            $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));
            if (!$userFkCol) return response()->json(['error'=>'Columna de relación usuario no encontrada en aprendices'], 500);

            // Determinar el semillero al que pertenece el proyecto
            $semilleroId = null;
            if (Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero')) {
                $semilleroId = DB::table('proyectos')->where('id_proyecto', $id)->value('id_semillero');
            }

            // Si no hay semillero asociado al proyecto, no ofrecer aprendices
            if (!$semilleroId) {
                return response()->json([]);
            }

            $hasAprSemilleroCol = Schema::hasColumn('aprendices','semillero_id');

            if (Schema::hasTable('proyecto_aprendiz')) {
                $sub = DB::table('proyecto_aprendiz')->select('id_aprendiz')->where('id_proyecto', $id);
                $rows = DB::table('aprendices as a')
                    ->join('users as u', DB::raw('u.id'), '=', DB::raw('a.'.$userFkCol))
                    ->when($hasAprSemilleroCol, function($w) use ($semilleroId){
                        $w->where('a.semillero_id', $semilleroId);
                    }, function($w) use ($semilleroId){
                        $w->join('aprendiz_semillero as aps', 'aps.id_aprendiz', '=', 'a.id_aprendiz')
                          ->where('aps.id_semillero', $semilleroId);
                    })
                    ->where('u.role','APRENDIZ')
                    ->whereNotIn('a.id_aprendiz', $sub);
            } elseif (Schema::hasTable('aprendiz_proyecto')) {
                $sub = DB::table('aprendiz_proyecto')->select('id_aprendiz')->where('id_proyecto', $id);
                $rows = DB::table('aprendices as a')
                    ->join('users as u', DB::raw('u.id'), '=', DB::raw('a.'.$userFkCol))
                    ->when($hasAprSemilleroCol, function($w) use ($semilleroId){
                        $w->where('a.semillero_id', $semilleroId);
                    }, function($w) use ($semilleroId){
                        $w->join('aprendiz_semillero as aps', 'aps.id_aprendiz', '=', 'a.id_aprendiz')
                          ->where('aps.id_semillero', $semilleroId);
                    })
                    ->where('u.role','APRENDIZ')
                    ->whereNotIn('a.id_aprendiz', $sub);
            } else {
                $sub = DB::table('proyecto_user')->select('user_id')->where('id_proyecto', $id);
                $rows = DB::table('users as u')
                    ->join('aprendices as a', DB::raw('a.'.$userFkCol), '=', DB::raw('u.id'))
                    ->when($hasAprSemilleroCol, function($w) use ($semilleroId){
                        $w->where('a.semillero_id', $semilleroId);
                    }, function($w) use ($semilleroId){
                        $w->join('aprendiz_semillero as aps', 'aps.id_aprendiz', '=', 'a.id_aprendiz')
                          ->where('aps.id_semillero', $semilleroId);
                    })
                    ->where('u.role','APRENDIZ')
                    ->whereNotIn('u.id', $sub);
            }
            $rows = $rows
                ->when($tipoCanon !== '' && Schema::hasColumn('aprendices','tipo_documento'), function($w) use ($tipoCanon){ $w->where('a.tipo_documento', $tipoCanon); })
                ->when($num !== '' && Schema::hasColumn('aprendices','documento'), function($w) use ($num){ $w->where('a.documento', 'like', "%$num%"); })
                ->when($q !== '', function($w) use ($q){
                    $like = "%$q%";
                    $w->where(function($q2) use ($like){
                        $q2->where('u.name','like',$like)->orWhere('u.email','like',$like);
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','nombres')) { $q2->orWhere('a.nombres','like',$like); }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','apellidos')) { $q2->orWhere('a.apellidos','like',$like); }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','documento')) { $q2->orWhere('a.documento','like',$like); }
                    });
                })
                ->select('u.id as id_usuario','u.name','a.nombres','a.apellidos','a.programa','a.id_aprendiz')
                ->orderBy('u.name')
                ->limit(20)
                ->get();

            $out = $rows->map(function($r){
                $nombre = $r->name ?: trim(($r->nombres ?? '').' '.($r->apellidos ?? ''));
                return [
                    'id_aprendiz' => (int)($r->id_aprendiz ?? $r->id_usuario),
                    'nombre_completo' => $nombre ?: 'Aprendiz',
                    'programa' => $r->programa,
                ];
            });
            return response()->json($out);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Compat: sincronizar lista completa de participantes del proyecto
    public function updateParticipants(Request $request, $id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['ok'=>false], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['ok'=>false], 403);

            $ids = (array)$request->input('aprendices_ids', []);
            $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

            // Detectar FK aprendices -> users para mapear en caso de recibir user_ids
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
            $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));

            $now = now();

            if (Schema::hasTable('aprendiz_proyecto')) {
                // Determinar si los ids recibidos son de aprendices o usuarios
                $areAprendizIds = DB::table('aprendices')->whereIn('id_aprendiz', $ids)->count() === count($ids);
                $aprendizIds = $areAprendizIds
                    ? $ids
                    : DB::table('aprendices')->whereIn($userFkCol, $ids)->pluck('id_aprendiz')->all();

                $exist = DB::table('aprendiz_proyecto')->where('id_proyecto', $id)->pluck('id_aprendiz')->all();
                // borrar los que ya no están
                if (!empty($exist)) {
                    DB::table('aprendiz_proyecto')->where('id_proyecto', $id)->whereNotIn('id_aprendiz', $aprendizIds)->delete();
                }
                // insertar los nuevos faltantes
                $toAdd = array_diff($aprendizIds, $exist);
                foreach ($toAdd as $aprId) {
                    if (DB::table('aprendices')->where('id_aprendiz', $aprId)->exists()) {
                        $data = ['id_proyecto'=>$id,'id_aprendiz'=>$aprId];
                        if (Schema::hasColumn('aprendiz_proyecto','created_at')) { $data['created_at'] = $now; }
                        if (Schema::hasColumn('aprendiz_proyecto','updated_at')) { $data['updated_at'] = $now; }
                        DB::table('aprendiz_proyecto')->insert($data);
                    }
                }
                // Espejo en proyecto_user
                if (Schema::hasTable('proyecto_user')) {
                    $userIds = DB::table('aprendices')->whereIn('id_aprendiz', $aprendizIds)->pluck($userFkCol)->filter()->all();
                    $existPU = DB::table('proyecto_user')->where('id_proyecto',$id)->pluck('user_id')->all();
                    // borrar los que ya no están
                    if (!empty($existPU)) {
                        DB::table('proyecto_user')->where('id_proyecto',$id)->whereNotIn('user_id', $userIds)->delete();
                    }
                    // insertar faltantes
                    foreach (array_diff($userIds, $existPU) as $uidAdd) {
                        DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$uidAdd,'created_at'=>$now,'updated_at'=>$now]);
                    }
                }
                return response()->json(['ok'=>true]);
            }

            if (Schema::hasTable('proyecto_aprendiz')) {
                $areAprendizIds = DB::table('aprendices')->whereIn('id_aprendiz', $ids)->count() === count($ids);
                $aprendizIds = $areAprendizIds
                    ? $ids
                    : DB::table('aprendices')->whereIn($userFkCol, $ids)->pluck('id_aprendiz')->all();

                $exist = DB::table('proyecto_aprendiz')->where('id_proyecto', $id)->pluck('id_aprendiz')->all();
                if (!empty($exist)) {
                    DB::table('proyecto_aprendiz')->where('id_proyecto', $id)->whereNotIn('id_aprendiz', $aprendizIds)->delete();
                }
                $toAdd = array_diff($aprendizIds, $exist);
                foreach ($toAdd as $aprId) {
                    if (DB::table('aprendices')->where('id_aprendiz', $aprId)->exists()) {
                        $data = ['id_proyecto'=>$id,'id_aprendiz'=>$aprId];
                        if (Schema::hasColumn('proyecto_aprendiz','created_at')) { $data['created_at'] = $now; }
                        if (Schema::hasColumn('proyecto_aprendiz','updated_at')) { $data['updated_at'] = $now; }
                        DB::table('proyecto_aprendiz')->insert($data);
                    }
                }
                // Espejo en proyecto_user
                if (Schema::hasTable('proyecto_user')) {
                    $userIds = DB::table('aprendices')->whereIn('id_aprendiz', $aprendizIds)->pluck($userFkCol)->filter()->all();
                    $existPU = DB::table('proyecto_user')->where('id_proyecto',$id)->pluck('user_id')->all();
                    if (!empty($existPU)) {
                        DB::table('proyecto_user')->where('id_proyecto',$id)->whereNotIn('user_id', $userIds)->delete();
                    }
                    foreach (array_diff($userIds, $existPU) as $uidAdd) {
                        DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$uidAdd,'created_at'=>$now,'updated_at'=>$now]);
                    }
                }
                return response()->json(['ok'=>true]);
            }

            // Fallback a proyecto_user si no existen las pivotes específicas de aprendices
            $exist = DB::table('proyecto_user')->where('id_proyecto', $id)->pluck('user_id')->all();
            if (!empty($exist)) {
                DB::table('proyecto_user')->where('id_proyecto', $id)->whereNotIn('user_id', $ids)->delete();
            }
            $toAdd = array_diff($ids, $exist);
            foreach ($toAdd as $uidAdd) {
                $isApr = DB::table('users')->where('id',$uidAdd)->where('role','APRENDIZ')->exists();
                if ($isApr) {
                    DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$uidAdd,'created_at'=>$now,'updated_at'=>$now]);
                }
            }
            return response()->json(['ok'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }

    // Detalle JSON de un proyecto del líder con KPIs
    public function showJson($id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);

            $qb = DB::table('proyectos as p')
                ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
                ->where('p.id_proyecto', $id);
            if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                $qb->where('s.id_lider_usuario', $uid);
            } elseif (Schema::hasColumn('semilleros','id_lider_semi') || Schema::hasColumn('semilleros','id_lider')) {
                $semCol = Schema::hasColumn('semilleros','id_lider_semi') ? 'id_lider_semi' : 'id_lider';
                // Detectar columnas en lideres_semillero
                $dbName = DB::getDatabaseName();
                $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'", [$dbName]))->pluck('c')->all();
                $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('user_id', $cols, true) ? 'user_id' : (in_array('id_user', $cols, true) ? 'id_user' : null));
                $leaderIdCol = in_array('id_lider_semi', $cols, true) ? 'id_lider_semi' : (in_array('id_lider', $cols, true) ? 'id_lider' : null);
                if ($leaderUserFkCol && $leaderIdCol) {
                    $qb->join('lideres_semillero as ls', DB::raw('ls.'.$leaderIdCol), '=', DB::raw('s.'.$semCol))
                       ->where(DB::raw('ls.'.$leaderUserFkCol), $uid);
                }
            }
            $p = $qb->select('p.*')->first();
            if (!$p) return response()->json(['error' => 'not_found'], 404);

            $apCount = (int) DB::table('proyecto_user')->where('id_proyecto', $id)->count();
            $docsAprob = (int) DB::table('archivos')->where('proyecto_id', $id)->where('estado', 'aprobado')->count();
            $docsPend = (int) DB::table('archivos')->where('proyecto_id', $id)->where('estado', 'pendiente')->count();

            return response()->json([
                'proyecto' => [
                    'id' => $p->id_proyecto,
                    'nombre' => $p->nombre_proyecto,
                    'estado' => $p->estado,
                    'fecha_inicio' => $p->fecha_inicio,
                    'fecha_fin' => $p->fecha_fin,
                    'descripcion' => $p->descripcion,
                ],
                'kpi' => [
                    'aprendices' => $apCount,
                    'docs_aprobados' => $docsAprob,
                    'docs_pendientes' => $docsPend,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Lista de participantes (aprendices) asignados al proyecto
    public function participantesJson($id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);

            // Detectar FK de aprendices -> users
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
            $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));

            if (Schema::hasTable('proyecto_aprendiz')) {
                $rows = DB::table('proyecto_aprendiz as pa')
                    ->join('aprendices as a', 'a.id_aprendiz', '=', 'pa.id_aprendiz')
                    ->join('users as u', DB::raw('u.id'), '=', DB::raw('a.' . $userFkCol))
                    ->where('pa.id_proyecto', $id)
                    ->select('u.id','u.name','u.email','a.ficha','a.programa')
                    ->orderBy('u.name')
                    ->get();
            } elseif (Schema::hasTable('aprendiz_proyecto')) {
                $rows = DB::table('aprendiz_proyecto as ap')
                    ->join('aprendices as a', 'a.id_aprendiz', '=', 'ap.id_aprendiz')
                    ->join('users as u', DB::raw('u.id'), '=', DB::raw('a.' . $userFkCol))
                    ->where('ap.id_proyecto', $id)
                    ->select('u.id','u.name','u.email','a.ficha','a.programa')
                    ->orderBy('u.name')
                    ->get();
            } else {
                $rows = DB::table('proyecto_user as pu')
                    ->join('users as u', 'u.id', '=', 'pu.user_id')
                    ->leftJoin('aprendices as a', DB::raw('a.' . $userFkCol), '=', DB::raw('u.id'))
                    ->where('pu.id_proyecto', $id)
                    ->select('u.id','u.name','u.email','a.ficha','a.programa')
                    ->orderBy('u.name')
                    ->get();
            }
            return response()->json(['participantes' => $rows]);
        } catch (\Throwable $e) {
            return response()->json(['participantes' => [], 'error' => $e->getMessage()], 500);
        }
    }

    // Asignar aprendiz (user_id o aprendiz_id) a proyecto
    public function assignParticipant(Request $request, $id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);
            // compat: aceptar user_id o aprendiz_id
            // Mapear múltiples nombres de campos que pueden venir desde el front
            $userId = (int)($request->input('user_id')
                        ?? $request->input('id_usuario')
                        ?? $request->input('id')
                        ?? 0);
            $aprendizId = (int)($request->input('aprendiz_id')
                          ?? $request->input('id_aprendiz')
                          ?? $request->input('aprendiz')
                          ?? 0);

            // Si viene un arreglo (guardar lista completa), delegar al método de sincronización
            $bulk = $request->input('aprendices_ids')
                 ?? $request->input('ids')
                 ?? $request->input('participantes');
            if (is_array($bulk)) {
                // Normalizar nombre esperado por updateParticipants
                $request->merge(['aprendices_ids' => $bulk]);
                return $this->updateParticipants($request, $id);
            }

            // Detectar FK de aprendices -> users
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
            $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));

            if (Schema::hasTable('proyecto_aprendiz')) {
                if (!$aprendizId && $userId) {
                    $aprendizId = (int) DB::table('aprendices')->where($userFkCol, $userId)->value('id_aprendiz');
                }
                if (!$aprendizId) {
                    return response()->json(['ok'=>false,'error'=>'aprendiz_id requerido'], 422);
                }
                $existsApr = DB::table('aprendices')->where('id_aprendiz', $aprendizId)->exists();
                if (!$existsApr) return response()->json(['ok'=>false,'error'=>'Aprendiz no existe'], 422);
                $exists = DB::table('proyecto_aprendiz')->where(['id_proyecto'=>$id,'id_aprendiz'=>$aprendizId])->exists();
                if (!$exists){
                    $data = ['id_proyecto'=>$id,'id_aprendiz'=>$aprendizId];
                    if (Schema::hasColumn('proyecto_aprendiz','created_at')) { $data['created_at'] = now(); }
                    if (Schema::hasColumn('proyecto_aprendiz','updated_at')) { $data['updated_at'] = now(); }
                    DB::table('proyecto_aprendiz')->insert($data);
                }
                // Espejo en proyecto_user para compatibilidad con vistas que leen esa pivote
                if (Schema::hasTable('proyecto_user')) {
                    $uidMap = (int) DB::table('aprendices')->where('id_aprendiz', $aprendizId)->value($userFkCol);
                    if ($uidMap) {
                        $existsPU = DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$uidMap])->exists();
                        if (!$existsPU) {
                            DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$uidMap,'created_at'=>now(),'updated_at'=>now()]);
                        }
                    }
                }
                return response()->json(['ok' => true]);
            } elseif (Schema::hasTable('aprendiz_proyecto')) {
                if (!$aprendizId && $userId) {
                    $aprendizId = (int) DB::table('aprendices')->where($userFkCol, $userId)->value('id_aprendiz');
                }
                if (!$aprendizId) {
                    return response()->json(['ok'=>false,'error'=>'aprendiz_id requerido'], 422);
                }
                $existsApr = DB::table('aprendices')->where('id_aprendiz', $aprendizId)->exists();
                if (!$existsApr) return response()->json(['ok'=>false,'error'=>'Aprendiz no existe'], 422);
                $exists = DB::table('aprendiz_proyecto')->where(['id_proyecto'=>$id,'id_aprendiz'=>$aprendizId])->exists();
                if (!$exists){
                    $data = ['id_proyecto'=>$id,'id_aprendiz'=>$aprendizId];
                    if (Schema::hasColumn('aprendiz_proyecto','created_at')) { $data['created_at'] = now(); }
                    if (Schema::hasColumn('aprendiz_proyecto','updated_at')) { $data['updated_at'] = now(); }
                    DB::table('aprendiz_proyecto')->insert($data);
                }
                // Espejo en proyecto_user para compatibilidad con vistas que leen esa pivote
                if (Schema::hasTable('proyecto_user')) {
                    $uidMap = (int) DB::table('aprendices')->where('id_aprendiz', $aprendizId)->value($userFkCol);
                    if ($uidMap) {
                        $existsPU = DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$uidMap])->exists();
                        if (!$existsPU) {
                            DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$uidMap,'created_at'=>now(),'updated_at'=>now()]);
                        }
                    }
                }
                return response()->json(['ok' => true]);
            } else {
                if (!$userId) {
                    return response()->json(['ok'=>false,'error'=>'user_id requerido'], 422);
                }
                $existsInUsers = DB::table('users')->where('id',$userId)->exists();
                if (!$existsInUsers) return response()->json(['ok'=>false,'error'=>'Usuario no existe'], 422);
                $exists = DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$userId])->exists();
                if (!$exists){
                    DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$userId,'created_at'=>now(),'updated_at'=>now()]);
                }
                return response()->json(['ok' => true]);
            }
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Quitar aprendiz del proyecto
    public function removeParticipant($id, $targetId)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);
            // Compat: intentar ambas pivotes
            // Detectar FK de aprendices -> users
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
            $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));

            if (Schema::hasTable('proyecto_aprendiz')) {
                // Primero intentar como id_aprendiz directo
                DB::table('proyecto_aprendiz')->where(['id_proyecto'=>$id,'id_aprendiz'=>$targetId])->delete();
                // Luego, si venía un user_id, mapear a id_aprendiz (solo si conocemos la FK)
                if ($userFkCol) {
                    $aprId = DB::table('aprendices')->where($userFkCol, $targetId)->value('id_aprendiz');
                    if ($aprId) {
                        DB::table('proyecto_aprendiz')->where(['id_proyecto'=>$id,'id_aprendiz'=>$aprId])->delete();
                    }
                }
            } elseif (Schema::hasTable('aprendiz_proyecto')) {
                DB::table('aprendiz_proyecto')->where(['id_proyecto'=>$id,'id_aprendiz'=>$targetId])->delete();
                if ($userFkCol) {
                    $aprId = DB::table('aprendices')->where($userFkCol, $targetId)->value('id_aprendiz');
                    if ($aprId) {
                        DB::table('aprendiz_proyecto')->where(['id_proyecto'=>$id,'id_aprendiz'=>$aprId])->delete();
                    }
                }
            }
            // Fallback: solo intentar en proyecto_user si la tabla existe
            if (Schema::hasTable('proyecto_user')) {
                DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$targetId])->delete();
            }
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function canManage($leaderUserId, $projectId): bool
    {
        $sid = DB::table('proyectos')->where('id_proyecto', $projectId)->value('id_semillero');
        if (!$sid) {
            // No tiene semillero asociado
            return true;
        }
        $q = DB::table('semilleros')->where('id_semillero', $sid);
        if (!$q->exists()) {
            // Semillero no registrado aún: permitir para no bloquear pruebas
            return true;
        }
        if (Schema::hasColumn('semilleros','id_lider_usuario')) {
            return DB::table('semilleros')->where('id_semillero', $sid)->where('id_lider_usuario', $leaderUserId)->exists();
        }
        if (Schema::hasColumn('semilleros','id_lider_semi') || Schema::hasColumn('semilleros','id_lider')) {
            $semCol = Schema::hasColumn('semilleros','id_lider_semi') ? 'id_lider_semi' : 'id_lider';
            // Detectar columnas en lideres_semillero
            $dbName = DB::getDatabaseName();
            $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'lideres_semillero'", [$dbName]))->pluck('c')->all();
            $leaderUserFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('user_id', $cols, true) ? 'user_id' : (in_array('id_user', $cols, true) ? 'id_user' : null));
            $leaderIdCol = in_array('id_lider_semi', $cols, true) ? 'id_lider_semi' : (in_array('id_lider', $cols, true) ? 'id_lider' : null);
            if ($leaderUserFkCol && $leaderIdCol) {
                return DB::table('semilleros as s')
                    ->join('lideres_semillero as ls', DB::raw('ls.'.$leaderIdCol), '=', DB::raw('s.'.$semCol))
                    ->where('s.id_semillero', $sid)
                    ->where(DB::raw('ls.'.$leaderUserFkCol), $leaderUserId)
                    ->exists();
            }
        }
        // Si no podemos verificar el dueño, no bloquear
        return true;
    }

    // Buscar candidatos (aprendices no asignados) por nombre/correo
    public function candidatosJson(Request $request, $id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);
            $q = trim((string)$request->query('q', ''));
            $num = trim((string)$request->query('num', ''));
            $tipo = trim((string)$request->query('tipo', ''));

            if (Schema::hasTable('proyecto_aprendiz')) {
                $sub = DB::table('proyecto_aprendiz')->select('id_aprendiz')->where('id_proyecto', $id);
                // Detectar FK aprendices->users
                $dbName = DB::getDatabaseName();
                $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
                $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));
                $hasAprSemilleroCol = Schema::hasColumn('aprendices','semillero_id');
                // Filtrar por semillero del proyecto si conocemos su id
                $rows = DB::table('aprendices as a')
                    ->join('users as u', DB::raw('u.id'), '=', DB::raw('a.'.$userFkCol))
                    ->when($hasAprSemilleroCol && Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero'), function($w) use ($id){
                        $sid = DB::table('proyectos')->where('id_proyecto',$id)->value('id_semillero');
                        if ($sid) { $w->where('a.semillero_id', $sid); }
                    })
                    ->where('u.role', 'APRENDIZ')
                    ->whereNotIn('a.id_aprendiz', $sub);
            } else {
                $sub = DB::table('proyecto_user')->select('user_id')->where('id_proyecto', $id);
                // Detectar FK aprendices->users
                $dbName = DB::getDatabaseName();
                $cols = collect(DB::select("SELECT COLUMN_NAME as c FROM information_schema.columns WHERE table_schema = ? AND table_name = 'aprendices' AND COLUMN_NAME IN ('id_usuario','id_user','user_id')", [$dbName]))->pluck('c')->all();
                $userFkCol = in_array('id_usuario', $cols, true) ? 'id_usuario' : (in_array('id_user', $cols, true) ? 'id_user' : (in_array('user_id', $cols, true) ? 'user_id' : null));
                $hasAprSemilleroCol = Schema::hasColumn('aprendices','semillero_id');
                $rows = DB::table('users as u')
                    ->join('aprendices as a', DB::raw('a.'.$userFkCol), '=', DB::raw('u.id'))
                    ->when($hasAprSemilleroCol && Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero'), function($w) use ($id){
                        $sid = DB::table('proyectos')->where('id_proyecto',$id)->value('id_semillero');
                        if ($sid) { $w->where('a.semillero_id', $sid); }
                    })
                    ->where('u.role', 'APRENDIZ')
                    ->whereNotIn('u.id', $sub);
            }
            $rows = $rows
                ->when($q !== '', function($w) use ($q){
                    $like = "%".$q."%";
                    $w->where(function($w2) use ($like){
                        $w2->where('u.name', 'like', $like)
                           ->orWhere('u.email', 'like', $like);
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','nombres')) { $w2->orWhere('a.nombres', 'like', $like); }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','apellidos')) { $w2->orWhere('a.apellidos', 'like', $like); }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','documento')) { $w2->orWhere('a.documento', 'like', $like); }
                    });
                })
                ->when($num !== '' && Schema::hasColumn('aprendices','documento'), function($w) use ($num){
                    $like = "%".$num."%";
                    $w->where('a.documento', 'like', $like);
                })
                ->when($tipo !== '' && Schema::hasColumn('aprendices','tipo_documento'), function($w) use ($tipo){
                    $w->where('a.tipo_documento', $tipo);
                })
                ->select('u.id','u.name','u.email','a.ficha','a.programa','a.documento','a.tipo_documento')
                ->orderBy('u.name')
                ->limit(10)
                ->get();
            return response()->json(['candidatos' => $rows]);
        } catch (\Throwable $e) {
            return response()->json(['candidatos' => [], 'error' => $e->getMessage()], 500);
        }
    }
}
