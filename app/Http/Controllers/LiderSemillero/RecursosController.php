<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class RecursosController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        // Construir query de proyectos de forma compatible con distintos esquemas
        $qb = DB::table('proyectos as p');
        $joinedSemilleros = false;
        if (Schema::hasTable('semilleros') && Schema::hasColumn('proyectos','id_semillero')) {
            $qb->join('semilleros as s', 's.id_semillero', '=', 'p.id_semillero');
            $joinedSemilleros = true;
            if (Schema::hasColumn('semilleros','id_lider_usuario')) {
                $qb->where('s.id_lider_usuario', $userId);
            } elseif (Schema::hasColumn('semilleros','id_lider_semi')) {
                $qb->where('s.id_lider_semi', $userId);
            }
        }
        // Si no se pudo filtrar por semilleros, intentar en proyectos directamente
        if (!$joinedSemilleros) {
            if (Schema::hasColumn('proyectos','id_lider_usuario')) {
                $qb->where('p.id_lider_usuario', $userId);
            } elseif (Schema::hasColumn('proyectos','id_lider_semi')) {
                $qb->where('p.id_lider_semi', $userId);
            }
        }

        $proyectos = $qb
            ->select('p.id_proyecto','p.nombre_proyecto','p.estado','p.descripcion')
            ->when(true, function($q){
                // Determinar columna de ordenamiento disponible
                if (Schema::hasColumn('proyectos','creado_en')) {
                    return $q->orderByDesc('p.creado_en');
                }
                if (Schema::hasColumn('proyectos','created_at')) {
                    return $q->orderByDesc('p.created_at');
                }
                if (Schema::hasColumn('proyectos','id_proyecto')) {
                    return $q->orderByDesc('p.id_proyecto');
                }
                return $q; // sin orden
            })
            ->get();

        $ids = $proyectos->pluck('id_proyecto');
        $total = collect();
        $pend  = collect();
        $aprob = collect();
        if ($ids->isNotEmpty() && Schema::hasTable('archivos')) {
            $total = DB::table('archivos')
                ->select('proyecto_id', DB::raw('COUNT(*) as c'))
                ->whereIn('proyecto_id', $ids)
                ->groupBy('proyecto_id')
                ->pluck('c','proyecto_id');
            $pend = DB::table('archivos')
                ->select('proyecto_id', DB::raw("SUM(CASE WHEN estado='pendiente' THEN 1 ELSE 0 END) as c"))
                ->whereIn('proyecto_id', $ids)
                ->groupBy('proyecto_id')
                ->pluck('c','proyecto_id');
            $aprob = DB::table('archivos')
                ->select('proyecto_id', DB::raw("SUM(CASE WHEN estado='aprobado' THEN 1 ELSE 0 END) as c"))
                ->whereIn('proyecto_id', $ids)
                ->groupBy('proyecto_id')
                ->pluck('c','proyecto_id');
        }

        $rows = $proyectos->map(function($p) use ($total,$pend,$aprob){
            $pid = $p->id_proyecto;
            return [
                'id' => $pid,
                'nombre' => $p->nombre_proyecto,
                'estado' => strtoupper((string)$p->estado),
                'descripcion' => $p->descripcion,
                'entregas' => (int)($total[$pid] ?? 0),
                'pendientes' => (int)($pend[$pid] ?? 0),
                'aprobadas' => (int)($aprob[$pid] ?? 0),
            ];
        });

        $activos = $rows->filter(fn($r)=> $r['estado'] === 'ACTIVO');
        $completados = $rows->filter(fn($r)=> in_array($r['estado'], ['COMPLETADO','COMPLETADA','FINALIZADO','FINALIZADA']));

        return view('lider_semi.recursos', compact('activos','completados'));
    }

    public function create()
    {
        // Usamos una sola vista combinada
        return redirect()->route('lider_semi.recursos.index');
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'nombre_archivo' => 'nullable|string|max:255',
            'archivo'        => 'required|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg',
            'categoria'      => 'required|in:plantillas,manuales,otros',
            'descripcion'    => 'nullable|string',
        ]);

        $file     = $request->file('archivo');
        $original = $file->getClientOriginalName();
        $putName  = time().'_'.$userId.'_'.$original;
        $path     = $file->storeAs('recursos', $putName, 'public');

        DB::table('recursos')->insert([
            'nombre_archivo' => $request->input('nombre_archivo') ?: $original,
            'archivo'        => $path,
            'categoria'      => $request->input('categoria'),
            'descripcion'    => $request->input('descripcion'),
            'user_id'        => $userId,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('lider_semi.recursos.index')->with('success', 'Recurso subido correctamente');
    }

    public function download($id)
    {
        $userId = Auth::id();
        $rec = DB::table('recursos')->where('id', $id)->where('user_id', $userId)->first();
        if (!$rec) return back()->with('error', 'Recurso no encontrado');

        if (!Storage::disk('public')->exists($rec->archivo)) {
            return back()->with('error', 'Archivo no existe');
        }

        $abs = storage_path('app/public/'.$rec->archivo);
        return response()->download($abs, basename($rec->archivo));
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $rec = DB::table('recursos')->where('id', $id)->where('user_id', $userId)->first();
        if (!$rec) return back()->with('error', 'Recurso no encontrado');

        if ($rec->archivo && Storage::disk('public')->exists($rec->archivo)) {
            Storage::disk('public')->delete($rec->archivo);
        }
        DB::table('recursos')->where('id', $id)->delete();

        return back()->with('success', 'Recurso eliminado');
    }
}