<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SemilleroAprendizController extends Controller
{
    private function resolveProyectoId(int $semilleroId, int $leaderUserId): ?int
    {
        $pid = DB::table('proyectos as p')
            ->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero')
            ->where('p.id_semillero', $semilleroId)
            ->where('s.id_lider_usuario', $leaderUserId)
            ->orderByDesc('p.creado_en')
            ->value('p.id_proyecto');
        return $pid ? (int)$pid : null;
    }

    public function search(Request $request, int $semillero)
    {
        $uid = Auth::id();
        if (!$uid) return response()->json([], 401);
        $pid = $this->resolveProyectoId($semillero, $uid);
        if (!$pid) return response()->json([], 404);

        $tipo = trim((string)$request->query('tipo', ''));
        $num = trim((string)$request->query('num', ''));
        $q = trim((string)$request->query('q', ''));

        $sub = DB::table('proyecto_user')->select('user_id')->where('id_proyecto', $pid);
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
    }

    public function attach(Request $request, int $semillero)
    {
        $uid = Auth::id();
        if (!$uid) return response()->json(['ok'=>false], 401);
        $pid = $this->resolveProyectoId($semillero, $uid);
        if (!$pid) return response()->json(['ok'=>false], 404);
        $userId = (int)($request->input('user_id') ?? $request->input('aprendiz_id'));
        if (!$userId) return response()->json(['ok'=>false,'error'=>'user_id/aprendiz_id requerido'], 422);
        $existsUser = DB::table('users')->where('id',$userId)->where('role','APRENDIZ')->exists();
        if (!$existsUser) return response()->json(['ok'=>false,'error'=>'No es aprendiz'], 422);
        $exists = DB::table('proyecto_user')->where(['id_proyecto'=>$pid,'user_id'=>$userId])->exists();
        if (!$exists){
            DB::table('proyecto_user')->insert(['id_proyecto'=>$pid,'user_id'=>$userId,'created_at'=>now(),'updated_at'=>now()]);
        }
        return response()->json(['ok'=>true,'aprendiz'=>['id_aprendiz'=>$userId]]);
    }

    public function detach(int $semillero, int $aprendiz)
    {
        $uid = Auth::id();
        if (!$uid) return response()->json(['ok'=>false], 401);
        $pid = $this->resolveProyectoId($semillero, $uid);
        if (!$pid) return response()->json(['ok'=>false], 404);
        DB::table('proyecto_user')->where(['id_proyecto'=>$pid,'user_id'=>$aprendiz])->delete();
        return response()->json(['ok'=>true]);
    }

    public function update(Request $request, int $semillero)
    {
        $uid = Auth::id();
        if (!$uid) return response()->json(['ok'=>false], 401);
        $pid = $this->resolveProyectoId($semillero, $uid);
        if (!$pid) return response()->json(['ok'=>false], 404);
        $ids = (array)$request->input('aprendices_ids', []);
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        $exist = DB::table('proyecto_user')->where('id_proyecto', $pid)->pluck('user_id')->all();
        if (!empty($exist)) {
            DB::table('proyecto_user')->where('id_proyecto', $pid)->whereNotIn('user_id', $ids)->delete();
        }
        $toAdd = array_diff($ids, $exist);
        $now = now();
        foreach ($toAdd as $uidAdd) {
            $isApr = DB::table('users')->where('id',$uidAdd)->where('role','APRENDIZ')->exists();
            if ($isApr) {
                DB::table('proyecto_user')->insert(['id_proyecto'=>$pid,'user_id'=>$uidAdd,'created_at'=>$now,'updated_at'=>$now]);
            }
        }
        return response()->json(['ok'=>true]);
    }
}
