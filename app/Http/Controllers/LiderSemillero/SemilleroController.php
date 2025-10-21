<?php

namespace App\Http\Controllers\LiderSemillero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semillero;
use App\Models\Aprendiz;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SemilleroController extends Controller
{
    // Muestra los semilleros asociados al líder y la vista correspondiente
    public function semilleros()
    {
        $userId = auth()->id();

        // Si existe la tabla de proyectos, mostrar proyectos como "Mis Proyectos"
        if (Schema::hasTable('proyectos')) {
            $pcols = [];
            // id_proyecto para enlazar con pivote
            if (Schema::hasColumn('proyectos', 'id_proyecto')) {
                $pcols[] = 'id_proyecto';
            }
            // nombre
            if (Schema::hasColumn('proyectos', 'nombre_proyecto')) {
                $pcols[] = DB::raw('nombre_proyecto as nombre');
            } else {
                $pcols[] = DB::raw("'' as nombre");
            }
            // descripcion
            if (Schema::hasColumn('proyectos', 'descripcion')) {
                $pcols[] = 'descripcion';
            } else {
                $pcols[] = DB::raw("'' as descripcion");
            }
            // estado
            if (Schema::hasColumn('proyectos', 'estado')) {
                $pcols[] = 'estado';
            } else {
                $pcols[] = DB::raw("'EN_EJECUCION' as estado");
            }
            // progreso (placeholder)
            $pcols[] = DB::raw('0 as progreso');
            // aprendices (placeholder)
            $pcols[] = DB::raw('0 as aprendices');

            $proyectos = DB::table('proyectos')->select($pcols)->get();

            // Enriquecer con aprendices reales si existe la relación N:M proyecto-aprendiz
            if ($proyectos->isNotEmpty() && Schema::hasTable('aprendices')) {
                $pivot = $this->pivotProyectoAprendiz();
                if (!empty($pivot)) {
                    $ids = $proyectos->pluck('id_proyecto')->filter()->values()->all();
                    if (!empty($ids)) {
                        // JOIN correcto según si la pivote usa id_aprendiz o id_usuario
                        $joinCol = $pivot['aprCol'] === 'id_usuario' ? 'id_usuario' : 'id_aprendiz';
                        $rows = DB::table($pivot['table'])
                            ->join('aprendices', 'aprendices.'.$joinCol, '=', DB::raw($pivot['table'].'.'.$pivot['aprCol']))
                            ->whereIn(DB::raw($pivot['table'].'.'.$pivot['projCol']), $ids)
                            ->select(
                                DB::raw($pivot['table'].'.'.$pivot['projCol'].' as pid'),
                                'aprendices.id_aprendiz',
                                'aprendices.nombre_completo',
                                'aprendices.correo_institucional',
                                'aprendices.programa'
                            )->get();

                        $grouped = $rows->groupBy('pid');

                        $proyectos->transform(function ($p) use ($grouped) {
                            $items = [];
                            if (isset($p->id_proyecto) && $grouped->has($p->id_proyecto)) {
                                foreach ($grouped[$p->id_proyecto] as $r) {
                                    $items[] = [
                                        'id_aprendiz' => $r->id_aprendiz ?? null,
                                        'nombre' => $r->nombre_completo,
                                        'email'  => $r->correo_institucional,
                                        'programa' => $r->programa ?? 'Sin programa',
                                    ];
                                }
                            }
                            $p->aprendices = count($items);
                            $p->aprendices_items = $items;
                            return $p;
                        });
                    }
                }
            }

            // Reusar la misma vista, mapeando proyectos a la colección esperada
            $semilleros = $proyectos;
            return view('lider_semi.semilleros', compact('semilleros'));
        }

        $query = Semillero::query();

        // Selecciones seguras con alias para columnas que la vista usa (sin asumir PK 'id')
        $cols = [];
        // id del semillero (compatibilidad con id o id_semillero)
        if (Schema::hasColumn('semilleros', 'id_semillero')) {
            $cols[] = 'id_semillero';
        } elseif (Schema::hasColumn('semilleros', 'id')) {
            $cols[] = DB::raw('id as id_semillero');
        }
        if (Schema::hasColumn('semilleros', 'nombre')) {
            $cols[] = 'nombre';
        } else {
            $cols[] = DB::raw("'' as nombre");
        }
        if (Schema::hasColumn('semilleros', 'estado')) {
            $cols[] = 'estado';
        } else {
            // si no existe, alias como 'Activo'
            $cols[] = DB::raw("'Activo' as estado");
        }
        if (Schema::hasColumn('semilleros', 'descripcion')) {
            $cols[] = 'descripcion';
        } else {
            $cols[] = DB::raw("'' as descripcion");
        }
        if (Schema::hasColumn('semilleros', 'progreso')) {
            $cols[] = 'progreso';
        } else {
            $cols[] = DB::raw('0 as progreso');
        }
        if (Schema::hasColumn('semilleros', 'aprendices')) {
            $cols[] = 'aprendices';
        } else {
            $cols[] = DB::raw('0 as aprendices');
        }
        $query->select($cols);

        if (Schema::hasColumn('semilleros', 'id_lider_semi')) {
            $query->where('id_lider_semi', $userId);
        }

        if (Schema::hasColumn('semilleros', 'estado')) {
            $query->whereIn('estado', ['Activo', 'ACTIVO']);
        }

        $semilleros = $query->get();

        // enriquecer con aprendices reales si existe pivote y PK
        if (Schema::hasTable('aprendiz_semillero') && ! $semilleros->isEmpty()) {
            $semilleros->transform(function ($s) {
                if (!isset($s->id_semillero)) { return $s; }
                $rel = Semillero::query()
                    ->select('id_semillero')
                    ->where('id_semillero', $s->id_semillero)
                    ->with(['aprendices' => function($q){
                        $q->select('aprendices.id_aprendiz','aprendices.nombre_completo','aprendices.correo_institucional','aprendices.programa');
                    }])->first();
                $items = [];
                if ($rel) {
                    foreach ($rel->aprendices as $ap) {
                        $items[] = [
                            'id_aprendiz' => $ap->id_aprendiz,
                            'nombre' => $ap->nombre_completo,
                            'email'  => $ap->correo_institucional,
                            'programa' => $ap->programa ?? 'Sin programa',
                        ];
                    }
                    $s->aprendices = count($items);
                }
                $s->aprendices_items = $items;
                return $s;
            });
        }

        return view('lider_semi.semilleros', compact('semilleros'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        // validar y guardar
    }

    public function show($id)
    {
        // mostrar un semillero
    }

    public function edit($id)
    {
        // formulario de edición
    }

    public function update(Request $request, $id)
    {
        // actualizar semillero
    }

    public function destroy($id)
    {
        // eliminar semillero
    }

    // --- Gestión de aprendices asignados ---
    public function editAprendices($semilleroId)
    {
        $semillero = Semillero::where('id_semillero', $semilleroId)
            ->with(['aprendices' => function($q){
                $q->select('aprendices.id_aprendiz','aprendices.nombre_completo','aprendices.correo_institucional');
            }])->firstOrFail();

        $asignadosIds = $semillero->aprendices->pluck('id_aprendiz')->all();

        $aprendices = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional')
            ->orderBy('nombre_completo')
            ->get();

        return view('lider_semi.semillero_aprendices', compact('semillero','aprendices','asignadosIds'));
    }

    public function updateAprendices(Request $request, $semilleroId)
    {
        $data = $request->validate([
            'aprendices_ids' => ['array'],
            'aprendices_ids.*' => ['integer'],
        ]);

        $semillero = Semillero::where('id_semillero', $semilleroId)->firstOrFail();
        $ids = $data['aprendices_ids'] ?? [];
        $semillero->aprendices()->sync($ids);
        // actualizar contador si existe la columna
        if (Schema::hasColumn('semilleros','aprendices')) {
            $semillero->aprendices = $semillero->aprendices()->count();
            $semillero->save();
        }

        return redirect()->route('lider_semi.semilleros')->with('status','Aprendices actualizados');
    }

    public function searchAprendices(Request $request, $semilleroId)
    {
        $q = trim((string)$request->get('q', ''));
        $tipo = trim((string)$request->get('tipo', ''));
        $num = trim((string)$request->get('num', ''));
        
        $semillero = Semillero::where('id_semillero', $semilleroId)
            ->with('aprendices:id_aprendiz')
            ->firstOrFail();
        $excluir = $semillero->aprendices->pluck('id_aprendiz')->all();

        $query = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','tipo_documento','documento','programa');
        
        // Si vienen tipo y num separados, usarlos directamente
        if ($tipo !== '' || $num !== '') {
            if ($tipo !== '') {
                $query->where('tipo_documento', $tipo);
            }
            if ($num !== '') {
                $query->where(function($w) use ($num){
                    $w->where('documento','like',"%{$num}%")
                      ->orWhere('nombre_completo','like',"%{$num}%");
                });
            }
        } elseif ($q !== '') {
            // Fallback: búsqueda genérica
            $query->where(function($w) use ($q){
                $w->where('tipo_documento','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('nombre_completo','like',"%{$q}%");
            });
        }
        
        if (!empty($excluir)) {
            $query->whereNotIn('id_aprendiz', $excluir);
        }
        $res = $query->orderBy('nombre_completo')->limit(20)->get();
        return response()->json($res);
    }

    public function attachAprendiz(Request $request, $semilleroId)
    {
        $data = $request->validate([
            'aprendiz_id' => ['required','integer','exists:aprendices,id_aprendiz'],
        ]);
        $semillero = Semillero::where('id_semillero', $semilleroId)->firstOrFail();
        $semillero->aprendices()->syncWithoutDetaching([$data['aprendiz_id']]);
        if (Schema::hasColumn('semilleros','aprendices')) {
            $semillero->aprendices = $semillero->aprendices()->count();
            $semillero->save();
        }
        $ap = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','programa')->find($data['aprendiz_id']);
        return response()->json(['ok'=>true,'aprendiz'=>$ap]);
    }

    public function detachAprendiz($semilleroId, $aprendizId)
    {
        $semillero = Semillero::where('id_semillero', $semilleroId)->firstOrFail();
        $semillero->aprendices()->detach($aprendizId);
        if (Schema::hasColumn('semilleros','aprendices')) {
            $semillero->aprendices = $semillero->aprendices()->count();
            $semillero->save();
        }
        return response()->json(['ok'=>true]);
    }

    public function createAndAttachAprendiz(Request $request, $semilleroId)
    {
        $data = $request->validate([
            'nombre_completo' => ['required','string','max:255'],
            'correo_institucional' => ['nullable','email','max:255'],
        ]);
        $ap = new Aprendiz();
        $ap->nombre_completo = $data['nombre_completo'];
        if (isset($data['correo_institucional'])) {
            $ap->correo_institucional = $data['correo_institucional'];
        }
        $ap->save();

        $semillero = Semillero::where('id_semillero', $semilleroId)->firstOrFail();
        $semillero->aprendices()->syncWithoutDetaching([$ap->id_aprendiz]);
        if (Schema::hasColumn('semilleros','aprendices')) {
            $semillero->aprendices = $semillero->aprendices()->count();
            $semillero->save();
        }
        return response()->json(['ok'=>true,'aprendiz'=>[
            'id_aprendiz'=>$ap->id_aprendiz,
            'nombre_completo'=>$ap->nombre_completo,
            'correo_institucional'=>$ap->correo_institucional,
        ]]);
    }

    // ---- Helpers y gestión por PROYECTO ----
    private function pivotProyectoAprendiz(): array
    {
        // Preferencia: esquema fijo según BD compartida
        if (Schema::hasTable('aprendiz_proyectos') &&
            Schema::hasColumn('aprendiz_proyectos','id_proyecto') &&
            Schema::hasColumn('aprendiz_proyectos','id_aprendiz')) {
            return ['table'=>'aprendiz_proyectos','projCol'=>'id_proyecto','aprCol'=>'id_aprendiz'];
        }

        $pivotCandidates = [
            'aprendiz_proyecto', 'aprendices_proyectos', 'aprendiz_proyectos', 'proyecto_aprendiz', 'proyectos_aprendices', 'proyecto_aprendices'
        ];
        $table = null;
        foreach ($pivotCandidates as $cand) {
            if (Schema::hasTable($cand)) { $table = $cand; break; }
        }
        if (!$table) return [];
        $projCols = ['id_proyecto','proyecto_id','idProyecto'];
        $aprCols  = ['id_aprendiz','aprendiz_id','idAprendiz','id_usuario'];
        $projCol = null; $aprCol = null;
        foreach ($projCols as $c) { if (Schema::hasColumn($table, $c)) { $projCol = $c; break; } }
        foreach ($aprCols as $c) { if (Schema::hasColumn($table, $c)) { $aprCol = $c; break; } }
        if (!$projCol || !$aprCol) return [];
        return ['table'=>$table,'projCol'=>$projCol,'aprCol'=>$aprCol];
    }

    public function editProyectoAprendices($proyectoId)
    {
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) abort(404, 'Relación proyecto-aprendiz no configurada');
        $proyecto = Proyecto::select('id_proyecto', DB::raw('COALESCE(nombre_proyecto, "Proyecto") as nombre'))
            ->where('id_proyecto', $proyectoId)->firstOrFail();
        $rows = DB::table($pivot['table'])
            ->join('aprendices','aprendices.id_aprendiz','=',DB::raw($pivot['table'].'.'.$pivot['aprCol']))
            ->where(DB::raw($pivot['table'].'.'.$pivot['projCol']), $proyectoId)
            ->select('aprendices.id_aprendiz','aprendices.nombre_completo','aprendices.correo_institucional')
            ->orderBy('aprendices.nombre_completo')
            ->get();
        // Simular relación para la vista
        $proyecto->aprendices = $rows;
        return view('lider_semi.proyecto_aprendices', compact('proyecto'));
    }

    public function updateProyectoAprendices(Request $request, $proyectoId)
    {
        $data = $request->validate([
            'aprendices_ids' => ['array'],
            'aprendices_ids.*' => ['integer'],
        ]);
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) abort(404, 'Relación proyecto-aprendiz no configurada');
        // limpiar y reinsertar
        DB::table($pivot['table'])->where($pivot['projCol'], $proyectoId)->delete();
        $ids = $data['aprendices_ids'] ?? [];
        $insert = [];
        foreach ($ids as $aid) {
            $insert[] = [ $pivot['projCol'] => $proyectoId, $pivot['aprCol'] => $aid ];
        }
        if (!empty($insert)) DB::table($pivot['table'])->insert($insert);
        return redirect()->route('lider_semi.semilleros')->with('status','Aprendices del proyecto actualizados');
    }

    public function searchProyectoAprendices(Request $request, $proyectoId)
    {
        $q = trim((string)$request->get('q', ''));
        $tipo = trim((string)$request->get('tipo', ''));
        $num = trim((string)$request->get('num', ''));
        
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) return response()->json([]);
        // Obtener IDs de aprendices ya asignados en términos de id_aprendiz
        if ($pivot['aprCol'] === 'id_usuario') {
            $asignados = DB::table($pivot['table'])
                ->join('aprendices','aprendices.id_usuario','=',DB::raw($pivot['table'].'.'.$pivot['aprCol']))
                ->where($pivot['projCol'], $proyectoId)
                ->pluck('aprendices.id_aprendiz')->all();
        } else {
            $asignados = DB::table($pivot['table'])
                ->join('aprendices','aprendices.'.$pivot['aprCol'],'=',DB::raw($pivot['table'].'.'.$pivot['aprCol']))
                ->where($pivot['projCol'], $proyectoId)
                ->pluck('aprendices.id_aprendiz')->all();
        }
        $query = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','tipo_documento','documento','programa');
        
        // Si vienen tipo y num separados, usarlos directamente
        if ($tipo !== '' || $num !== '') {
            if ($tipo !== '') {
                $query->where('tipo_documento', $tipo);
            }
            if ($num !== '') {
                $query->where(function($w) use ($num){
                    $w->where('documento','like',"%{$num}%")
                      ->orWhere('nombre_completo','like',"%{$num}%");
                });
            }
        } elseif ($q !== '') {
            // Fallback: búsqueda genérica
            $query->where(function($w) use ($q){
                $w->where('tipo_documento','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('nombre_completo','like',"%{$q}%");
            });
        }
        
        if (!empty($asignados)) $query->whereNotIn('id_aprendiz', $asignados);
        return response()->json($query->orderBy('nombre_completo')->limit(20)->get());
    }

    public function attachProyectoAprendiz(Request $request, $proyectoId)
    {
        $data = $request->validate(['aprendiz_id' => ['required','integer','exists:aprendices,id_aprendiz']]);
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) return response()->json(['ok'=>false], 400);
        $ap = Aprendiz::select('id_aprendiz','id_usuario')->findOrFail($data['aprendiz_id']);
        $aprValue = $pivot['aprCol'] === 'id_usuario' ? $ap->id_usuario : $ap->id_aprendiz;
        DB::table($pivot['table'])->updateOrInsert([
            $pivot['projCol'] => $proyectoId,
            $pivot['aprCol']  => $aprValue,
        ], []);
        $ap = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','programa')->find($data['aprendiz_id']);
        return response()->json(['ok'=>true,'aprendiz'=>$ap]);
    }

    public function detachProyectoAprendiz($proyectoId, $aprendizId)
    {
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) return response()->json(['ok'=>false], 400);
        if ($pivot['aprCol'] === 'id_usuario') {
            $ap = Aprendiz::select('id_usuario')->find($aprendizId);
            $aprVal = $ap ? $ap->id_usuario : null;
        } else {
            $aprVal = $aprendizId;
        }
        DB::table($pivot['table'])->where($pivot['projCol'], $proyectoId)->where($pivot['aprCol'], $aprVal)->delete();
        return response()->json(['ok'=>true]);
    }

    public function createAndAttachProyectoAprendiz(Request $request, $proyectoId)
    {
        $data = $request->validate([
            'nombre_completo' => ['required','string','max:255'],
            'correo_institucional' => ['nullable','email','max:255'],
        ]);
        $ap = new Aprendiz();
        $ap->nombre_completo = $data['nombre_completo'];
        if (isset($data['correo_institucional'])) $ap->correo_institucional = $data['correo_institucional'];
        $ap->save();
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) return response()->json(['ok'=>false], 400);
        DB::table($pivot['table'])->updateOrInsert([
            $pivot['projCol'] => $proyectoId,
            $pivot['aprCol']  => $ap->id_aprendiz,
        ], []);
        return response()->json(['ok'=>true,'aprendiz'=>[
            'id_aprendiz'=>$ap->id_aprendiz,
            'nombre_completo'=>$ap->nombre_completo,
            'correo_institucional'=>$ap->correo_institucional,
        ]]);
    }

    // Listar todos los aprendices del grupo
    public function aprendices()
    {
        $userId = auth()->id();
        
        // Obtener aprendices básicos
        $aprendices = Aprendiz::select(
            'aprendices.id_aprendiz',
            'aprendices.id_usuario',
            'aprendices.nombre_completo',
            'aprendices.tipo_documento',
            'aprendices.documento',
            'aprendices.celular',
            'aprendices.correo_institucional',
            'aprendices.correo_personal',
            'aprendices.programa',
            'aprendices.ficha',
            'aprendices.contacto_nombre',
            'aprendices.contacto_celular'
        )->orderBy('nombre_completo')->get();

        $aprendicesIds = $aprendices->pluck('id_aprendiz')->toArray();

        // Intentar obtener proyectos asignados
        $proyectosRelaciones = [];
        if (Schema::hasTable('proyectos') && !empty($aprendicesIds)) {
            $pivot = $this->pivotProyectoAprendiz();
            if (!empty($pivot)) {
                try {
                    $proyectosRelaciones = DB::table($pivot['table'])
                        ->join('proyectos', 'proyectos.id_proyecto', '=', DB::raw($pivot['table'].'.'.$pivot['projCol']))
                        ->join('aprendices', function($join) use ($pivot) {
                            if ($pivot['aprCol'] === 'id_usuario') {
                                $join->on('aprendices.id_usuario', '=', DB::raw($pivot['table'].'.'.$pivot['aprCol']));
                            } else {
                                $join->on('aprendices.id_aprendiz', '=', DB::raw($pivot['table'].'.'.$pivot['aprCol']));
                            }
                        })
                        ->whereIn('aprendices.id_aprendiz', $aprendicesIds)
                        ->select(
                            'aprendices.id_aprendiz',
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
                    ->whereIn('aprendiz_semillero.id_aprendiz', $aprendicesIds)
                    ->select('aprendiz_semillero.id_aprendiz', 'semilleros.nombre as semillero_nombre')
                    ->get()
                    ->groupBy('id_aprendiz');
            } catch (\Exception $e) {
                // Si falla, continuar sin semilleros
            }
        }

        // Asignar proyectos y semilleros a cada aprendiz
        $aprendices->transform(function($ap) use ($proyectosRelaciones, $semillerosRelaciones) {
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
