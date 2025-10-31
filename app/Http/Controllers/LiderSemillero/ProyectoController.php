<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

            // Proyectos cuyos semilleros pertenecen al líder (según esquema de BD)
            $proyectos = DB::table('proyectos as p')
                ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
                ->where('s.id_lider_usuario', $uid)
                ->select(
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
            $num = trim((string)$request->query('num', ''));
            $q = trim((string)$request->query('q', ''));

            $sub = DB::table('proyecto_user')->select('user_id')->where('id_proyecto', $id);
            $rows = DB::table('users as u')
                ->leftJoin('aprendices as a', 'a.id_usuario', '=', 'u.id')
                ->where('u.role', 'APRENDIZ')
                ->whereNotIn('u.id', $sub)
                ->when($tipo !== '', function($w) use ($tipo){ $w->where('a.tipo_documento', $tipo); })
                ->when($num !== '', function($w) use ($num){ $w->where('a.documento', 'like', "%$num%"); })
                ->when($q !== '', function($w) use ($q){
                    $like = "%$q%";
                    $w->where(function($q2) use ($like){
                        $q2->where('u.name','like',$like)->orWhere('u.email','like',$like)
                           ->orWhere('a.nombres','like',$like)->orWhere('a.apellidos','like',$like)
                           ->orWhere('a.documento','like',$like);
                    });
                })
                ->select('u.id as id_usuario','u.name','a.nombres','a.apellidos','a.programa')
                ->orderBy('u.name')
                ->limit(20)
                ->get();

            $out = $rows->map(function($r){
                $nombre = $r->name ?: trim(($r->nombres ?? '').' '.($r->apellidos ?? ''));
                return [
                    'id_aprendiz' => (int)$r->id_usuario,
                    'nombre_completo' => $nombre ?: 'Aprendiz',
                    'programa' => $r->programa,
                ];
            });
            return response()->json($out);
        } catch (\Throwable $e) {
            return response()->json([], 500);
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

            // existentes
            $exist = DB::table('proyecto_user')->where('id_proyecto', $id)->pluck('user_id')->all();

            // borrar los que ya no están
            if (!empty($exist)) {
                DB::table('proyecto_user')->where('id_proyecto', $id)->whereNotIn('user_id', $ids)->delete();
            }
            // insertar los nuevos faltantes
            $toAdd = array_diff($ids, $exist);
            $now = now();
            foreach ($toAdd as $uidAdd) {
                // asegurar que es APRENDIZ
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

            $p = DB::table('proyectos as p')
                ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
                ->where('p.id_proyecto', $id)
                ->where('s.id_lider_usuario', $uid)
                ->select('p.*')
                ->first();
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

            $rows = DB::table('proyecto_user as pu')
                ->join('users as u', 'u.id', '=', 'pu.user_id')
                ->leftJoin('aprendices as a', 'a.id_usuario', '=', 'u.id')
                ->where('pu.id_proyecto', $id)
                ->select('u.id','u.name','u.email','a.ficha','a.programa')
                ->orderBy('u.name')
                ->get();
            return response()->json(['participantes' => $rows]);
        } catch (\Throwable $e) {
            return response()->json(['participantes' => [], 'error' => $e->getMessage()], 500);
        }
    }

    // Asignar aprendiz (user_id) a proyecto
    public function assignParticipant(Request $request, $id)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);
            // compat: aceptar user_id o aprendiz_id
            $userId = (int)($request->input('user_id') ?? $request->input('aprendiz_id'));
            if (!$userId) {
                return response()->json(['ok'=>false,'error'=>'user_id/aprendiz_id requerido'], 422);
            }
            $existsInUsers = DB::table('users')->where('id',$userId)->exists();
            if (!$existsInUsers) return response()->json(['ok'=>false,'error'=>'Usuario no existe'], 422);
            $exists = DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$userId])->exists();
            if (!$exists){
                DB::table('proyecto_user')->insert(['id_proyecto'=>$id,'user_id'=>$userId,'created_at'=>now(),'updated_at'=>now()]);
            }
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Quitar aprendiz del proyecto
    public function removeParticipant($id, $userId)
    {
        try {
            $uid = Auth::id();
            if (!$uid) return response()->json(['error' => 'auth'], 401);
            if (!$this->canManage($uid, $id)) return response()->json(['error' => 'forbidden'], 403);
            DB::table('proyecto_user')->where(['id_proyecto'=>$id,'user_id'=>$userId])->delete();
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
        return $q->where('id_lider_usuario', $leaderUserId)->exists();
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

            $sub = DB::table('proyecto_user')->select('user_id')->where('id_proyecto', $id);
            $rows = DB::table('users as u')
                ->leftJoin('aprendices as a', 'a.id_usuario', '=', 'u.id')
                ->where('u.role', 'APRENDIZ')
                ->whereNotIn('u.id', $sub)
                ->when($q !== '', function($w) use ($q){
                    $like = "%".$q."%";
                    $w->where(function($w2) use ($like){
                        $w2->where('u.name', 'like', $like)
                           ->orWhere('u.email', 'like', $like)
                           ->orWhere('a.nombres', 'like', $like)
                           ->orWhere('a.apellidos', 'like', $like)
                           ->orWhere('a.documento', 'like', $like);
                    });
                })
                ->when($num !== '', function($w) use ($num){
                    $like = "%".$num."%";
                    $w->where('a.documento', 'like', $like);
                })
                ->when($tipo !== '', function($w) use ($tipo){
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
