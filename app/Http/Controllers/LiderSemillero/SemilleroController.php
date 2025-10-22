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
            // Obtener proyectos con sus datos reales
            $proyectos = DB::table('proyectos')
                ->select(
                    'id_proyecto',
                    'id_semillero',
                    'id_tipo_proyecto',
                    'nombre_proyecto as nombre',
                    'descripcion',
                    'estado',
                    'fecha_inicio',
                    'fecha_fin',
                    'creado_en',
                    'actualizado_en'
                )
                ->get();
            
            // Calcular progreso basado en fechas
            $proyectos->transform(function($p){
                if ($p->fecha_inicio && $p->fecha_fin) {
                    $inicio = strtotime($p->fecha_inicio);
                    $fin = strtotime($p->fecha_fin);
                    $ahora = time();
                    if ($ahora < $inicio) {
                        $p->progreso = 0;
                    } elseif ($ahora > $fin) {
                        $p->progreso = 100;
                    } else {
                        $total = $fin - $inicio;
                        $transcurrido = $ahora - $inicio;
                        $p->progreso = $total > 0 ? round(($transcurrido / $total) * 100) : 0;
                    }
                } else {
                    $p->progreso = 0;
                }
                $p->aprendices = 0; // Se actualizará después
                return $p;
            });

            // Enriquecer con aprendices reales si existe la relación N:M proyecto-aprendiz
            if ($proyectos->isNotEmpty() && Schema::hasTable('aprendices')) {
                $pivot = $this->pivotProyectoAprendiz();
                if (!empty($pivot)) {
                    $ids = $proyectos->pluck('id_proyecto')->filter()->values()->all();
                    if (!empty($ids)) {
                        // JOIN correcto según si la pivote usa id_aprendiz o id_usuario
                        $joinCol = ($pivot['aprCol'] === 'id_usuario' && Schema::hasColumn('aprendices', 'id_usuario')) ? 'id_usuario' : 'id_aprendiz';
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

        $query = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','tipo_documento','documento','programa','ficha');
        
        // Aplicar filtros si existen
        if ($tipo !== '') {
            $query->where('tipo_documento', $tipo);
        }
        if ($num !== '') {
            $query->where(function($w) use ($num){
                $w->where('documento','like',"%{$num}%")
                  ->orWhere('nombre_completo','like',"%{$num}%")
                  ->orWhere('ficha','like',"%{$num}%");
            });
        }
        if ($q !== '' && $tipo === '' && $num === '') {
            // Fallback: búsqueda genérica solo si no hay tipo ni num
            $query->where(function($w) use ($q){
                $w->where('tipo_documento','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('nombre_completo','like',"%{$q}%")
                  ->orWhere('ficha','like',"%{$q}%");
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
        // Usar la tabla documentos como relación entre proyectos y aprendices
        if (Schema::hasTable('documentos') &&
            Schema::hasColumn('documentos','id_proyecto') &&
            Schema::hasColumn('documentos','id_aprendiz')) {
            return ['table'=>'documentos','projCol'=>'id_proyecto','aprCol'=>'id_aprendiz'];
        }

        // Fallback: buscar tabla pivote tradicional
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
        $pivotProjCol = null; $pivotAprCol = null;
        foreach ($projCols as $c) { if (Schema::hasColumn($table, $c)) { $pivotProjCol = $c; break; } }
        foreach ($aprCols as $c) { if (Schema::hasColumn($table, $c)) { $pivotAprCol = $c; break; } }
        if (!$pivotProjCol || !$pivotAprCol) return [];
        return ['table'=>$table,'projCol'=>$pivotProjCol,'aprCol'=>$pivotAprCol];
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
        
        // Si usamos documentos como pivote, solo eliminar placeholders
        if ($pivot['table'] === 'documentos') {
            DB::table('documentos')
                ->where('id_proyecto', $proyectoId)
                ->where('documento', 'LIKE', 'PLACEHOLDER_%')
                ->delete();
        } else {
            // Pivote tradicional: limpiar todo
            DB::table($pivot['table'])->where($pivot['projCol'], $proyectoId)->delete();
        }
        
        $ids = $data['aprendices_ids'] ?? [];
        $insert = [];
        foreach ($ids as $aid) {
            if ($pivot['table'] === 'documentos') {
                // Para documentos, crear registros completos con placeholder
                $insert[] = [
                    'id_proyecto' => $proyectoId,
                    'id_aprendiz' => $aid,
                    'documento' => 'PLACEHOLDER_' . $aid,
                    'ruta_archivo' => '',
                    'tipo_archivo' => null,
                    'tamanio' => null,
                    'fecha_subida' => now(),
                ];
            } else {
                // Pivote tradicional
                $insert[] = [ $pivot['projCol'] => $proyectoId, $pivot['aprCol'] => $aid ];
            }
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
        if ($pivot['aprCol'] === 'id_usuario' && Schema::hasColumn('aprendices', 'id_usuario')) {
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
        $query = Aprendiz::select('id_aprendiz','nombre_completo','correo_institucional','tipo_documento','documento','programa','ficha');
        
        // Aplicar filtros si existen
        if ($tipo !== '') {
            $query->where('tipo_documento', $tipo);
        }
        if ($num !== '') {
            $query->where(function($w) use ($num){
                $w->where('documento','like',"%{$num}%")
                  ->orWhere('nombre_completo','like',"%{$num}%")
                  ->orWhere('ficha','like',"%{$num}%");
            });
        }
        if ($q !== '' && $tipo === '' && $num === '') {
            // Fallback: búsqueda genérica solo si no hay tipo ni num
            $query->where(function($w) use ($q){
                $w->where('tipo_documento','like',"%{$q}%")
                  ->orWhere('documento','like',"%{$q}%")
                  ->orWhere('nombre_completo','like',"%{$q}%")
                  ->orWhere('ficha','like',"%{$q}%");
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
        
        // Seleccionar columnas que existen
        $selectCols = ['id_aprendiz','nombre_completo','correo_institucional'];
        if (Schema::hasColumn('aprendices', 'id_usuario')) {
            $selectCols[] = 'id_usuario';
        }
        $ap = Aprendiz::select($selectCols)->findOrFail($data['aprendiz_id']);
        
        // Si estamos usando documentos como pivote, verificar si ya existe una relación
        if ($pivot['table'] === 'documentos') {
            $existe = DB::table('documentos')
                ->where('id_proyecto', $proyectoId)
                ->where('id_aprendiz', $ap->id_aprendiz)
                ->exists();
            
            if (!$existe) {
                // Crear un registro placeholder en documentos para establecer la relación
                DB::table('documentos')->insert([
                    'id_proyecto' => $proyectoId,
                    'id_aprendiz' => $ap->id_aprendiz,
                    'documento' => 'PLACEHOLDER_' . $ap->id_aprendiz, // NOT NULL, usar placeholder único
                    'ruta_archivo' => '', // NOT NULL, vacío
                    'tipo_archivo' => null, // Nullable
                    'tamanio' => null, // Nullable (nota: es "tamanio" con i)
                    'fecha_subida' => now(), // NOT NULL
                ]);
            }
        } else {
            // Pivote tradicional
            $aprValue = $ap->id_aprendiz;
            if ($pivot['aprCol'] === 'id_usuario' && isset($ap->id_usuario)) {
                $aprValue = $ap->id_usuario;
            }
            DB::table($pivot['table'])->updateOrInsert([
                $pivot['projCol'] => $proyectoId,
                $pivot['aprCol']  => $aprValue,
            ], []);
        }
        
        return response()->json(['ok'=>true,'aprendiz'=>[
            'id_aprendiz' => $ap->id_aprendiz,
            'nombre_completo' => $ap->nombre_completo,
            'correo_institucional' => $ap->correo_institucional,
        ]]);
    }

    public function detachProyectoAprendiz($proyectoId, $aprendizId)
    {
        $pivot = $this->pivotProyectoAprendiz();
        if (empty($pivot)) return response()->json(['ok'=>false], 400);
        
        if ($pivot['table'] === 'documentos') {
            // Solo eliminar registros placeholder (identificados por el prefijo PLACEHOLDER_)
            DB::table('documentos')
                ->where('id_proyecto', $proyectoId)
                ->where('id_aprendiz', $aprendizId)
                ->where('documento', 'LIKE', 'PLACEHOLDER_%')
                ->delete();
        } else {
            // Pivote tradicional
            if ($pivot['aprCol'] === 'id_usuario' && Schema::hasColumn('aprendices', 'id_usuario')) {
                $ap = Aprendiz::select('id_usuario')->find($aprendizId);
                $aprVal = $ap ? $ap->id_usuario : null;
            } else {
                $aprVal = $aprendizId;
            }
            DB::table($pivot['table'])->where($pivot['projCol'], $proyectoId)->where($pivot['aprCol'], $aprVal)->delete();
        }
        
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
        
        // Obtener aprendices básicos usando Query Builder para evitar problemas con columnas faltantes
        $selectCols = [
            'id_aprendiz',
            'nombre_completo',
            'tipo_documento',
            'documento',
            'celular',
            'correo_institucional',
            'correo_personal',
            'programa',
            'ficha',
            'contacto_nombre',
            'contacto_celular'
        ];
        if (Schema::hasColumn('aprendices', 'id_usuario')) {
            $selectCols[] = 'id_usuario';
        }
        $aprendices = DB::table('aprendices')->select($selectCols)->orderBy('nombre_completo')->get();

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
                            if ($pivot['aprCol'] === 'id_usuario' && Schema::hasColumn('aprendices', 'id_usuario')) {
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

    // Gestión de Documentación - Listar proyectos
    public function documentos()
    {
        $userId = auth()->id();
        
        // Verificar si existe la tabla de proyectos
        if (!Schema::hasTable('proyectos')) {
            return view('lider_semi.documentos', ['proyectos' => collect([])]);
        }

        // Obtener proyectos
        $proyectos = DB::table('proyectos')
            ->select(
                'id_proyecto',
                DB::raw('COALESCE(nombre_proyecto, "Proyecto") as nombre'),
                DB::raw('COALESCE(descripcion, "") as descripcion'),
                DB::raw('COALESCE(estado, "ACTIVO") as estado')
            )
            ->get();

        // Contar documentos reales por proyecto desde la tabla documentos
        $proyectos->transform(function($proyecto) {
            if (Schema::hasTable('documentos')) {
                // Verificar si existe la columna estado
                $hasEstado = Schema::hasColumn('documentos', 'estado');
                
                // Contar total de entregas (incluyendo placeholders/evidencias creadas por el líder)
                $proyecto->entregas = DB::table('documentos')
                    ->where('id_proyecto', $proyecto->id_proyecto)
                    ->count();
                
                if ($hasEstado) {
                    // Contar pendientes
                    $proyecto->pendientes = DB::table('documentos')
                        ->where('id_proyecto', $proyecto->id_proyecto)
                        ->where('estado', 'pendiente')
                        ->count();
                    
                    // Contar aprobadas
                    $proyecto->aprobadas = DB::table('documentos')
                        ->where('id_proyecto', $proyecto->id_proyecto)
                        ->where('estado', 'aprobado')
                        ->count();
                } else {
                    // Si no existe columna estado, todas son pendientes
                    $proyecto->pendientes = $proyecto->entregas;
                    $proyecto->aprobadas = 0;
                }
            } else {
                $proyecto->entregas = 0;
                $proyecto->pendientes = 0;
                $proyecto->aprobadas = 0;
            }
            
            return $proyecto;
        });

        // Separar proyectos activos y completados
        $proyectosActivos = $proyectos->filter(function($p) {
            $estadoUpper = strtoupper($p->estado);
            return in_array($estadoUpper, ['ACTIVO', 'EN_EJECUCION', 'EN EJECUCION', 'EJECUCION', 'EN_FORMULACION', 'EN FORMULACION', 'FORMULACION']);
        });

        $proyectosCompletados = $proyectos->filter(function($p) {
            $estadoUpper = strtoupper($p->estado);
            return in_array($estadoUpper, ['COMPLETADO', 'FINALIZADO', 'TERMINADO', 'CERRADO']);
        });

        return view('lider_semi.documentos', compact('proyectosActivos', 'proyectosCompletados'));
    }

    // Listar proyectos para el select del modal
    public function listarProyectos()
    {
        if (!Schema::hasTable('proyectos')) {
            return response()->json(['proyectos' => []]);
        }

        $proyectos = DB::table('proyectos')
            ->select('id_proyecto', 'nombre_proyecto')
            ->orderBy('nombre_proyecto')
            ->get();

        return response()->json(['proyectos' => $proyectos]);
    }

    // Obtener aprendices asignados a un proyecto
    public function obtenerAprendicesProyecto($proyectoId)
    {
        try {
            if (!Schema::hasTable('proyectos') || !Schema::hasTable('aprendices')) {
                return response()->json(['aprendices' => []]);
            }

            // Buscar la tabla pivot
            $pivot = $this->pivotProyectoAprendiz();
            
            if (empty($pivot)) {
                return response()->json(['aprendices' => []]);
            }

            // Obtener IDs únicos de aprendices del proyecto
            $aprendizIds = DB::table($pivot['table'])
                ->where($pivot['projCol'], $proyectoId)
                ->whereNotNull($pivot['aprendizCol'])
                ->where($pivot['aprendizCol'], '>', 0)
                ->distinct()
                ->pluck($pivot['aprendizCol']);

            // Si no hay aprendices, retornar vacío
            if ($aprendizIds->isEmpty()) {
                return response()->json(['aprendices' => []]);
            }

            // Obtener información de los aprendices
            $aprendices = DB::table('aprendices')
                ->whereIn('id_aprendiz', $aprendizIds)
                ->where('documento', '!=', 'SIN_ASIGNAR') // Excluir el aprendiz "Sin Asignar"
                ->select('id_aprendiz', 'nombre_completo')
                ->orderBy('nombre_completo')
                ->get();

            return response()->json(['aprendices' => $aprendices]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener aprendices del proyecto: ' . $e->getMessage());
            return response()->json(['aprendices' => []]);
        }
    }

    // Guardar evidencia de avance (crea un registro en documentos)
    public function guardarEvidencia(Request $request)
    {
        try {
            $request->validate([
                'proyecto_id' => 'required|integer|exists:proyectos,id_proyecto',
                'aprendiz_id' => 'nullable|integer|exists:aprendices,id_aprendiz',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'tipo_evidencia' => 'required|string',
                'fecha' => 'required|date'
            ]);

            // Verificar si existe tabla de documentos
            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            // Verificar si id_aprendiz permite NULL
            $columns = DB::select("SHOW COLUMNS FROM documentos WHERE Field = 'id_aprendiz'");
            $allowsNull = !empty($columns) && $columns[0]->Null === 'YES';
            
            // Preparar datos para insertar
            $dataToInsert = [
                'id_proyecto' => $request->proyecto_id,
                'documento' => $request->titulo,
                'ruta_archivo' => '',
                'tipo_archivo' => $request->tipo_evidencia,
                'tamanio' => 0,
                'mime_type' => '',
                'fecha_subido' => now(),
                'fecha_limite' => $request->fecha,
                'estado' => 'pendiente',
                'tipo_documento' => $request->tipo_evidencia,
                'descripcion' => $request->descripcion
            ];
            
            // Agregar id_aprendiz según lo que se haya seleccionado
            if ($request->has('aprendiz_id') && $request->aprendiz_id) {
                // Si se seleccionó un aprendiz, usarlo
                $dataToInsert['id_aprendiz'] = $request->aprendiz_id;
            } elseif ($allowsNull) {
                // Si permite NULL y no se seleccionó aprendiz, usar NULL
                $dataToInsert['id_aprendiz'] = null;
            } else {
                // Si no permite NULL, buscar o crear aprendiz "Sin Asignar"
                $aprendizSinAsignar = DB::table('aprendices')
                    ->where('documento', '=', 'SIN_ASIGNAR')
                    ->first();
                
                if (!$aprendizSinAsignar) {
                    try {
                        // Crear aprendiz especial "Sin Asignar"
                        $idAprendizSinAsignar = DB::table('aprendices')->insertGetId([
                            'nombres' => 'Sin',
                            'apellidos' => 'Asignar',
                            'nombre_completo' => 'Sin Asignar',
                            'tipo_documento' => 'CC',
                            'documento' => 'SIN_ASIGNAR',
                            'celular' => '0000000000',
                            'correo_institucional' => 'sin.asignar@sena.edu.co',
                            'correo_personal' => 'sin.asignar@sena.edu.co',
                            'programa' => 'N/A',
                            'ficha' => '0000000',
                            'contacto_nombre' => 'N/A',
                            'contacto_celular' => '0000000000'
                        ]);
                        $dataToInsert['id_aprendiz'] = $idAprendizSinAsignar;
                        \Log::info('Aprendiz Sin Asignar creado con ID: ' . $idAprendizSinAsignar);
                    } catch (\Exception $e) {
                        \Log::error('Error al crear aprendiz Sin Asignar: ' . $e->getMessage());
                        throw $e;
                    }
                } else {
                    $dataToInsert['id_aprendiz'] = $aprendizSinAsignar->id_aprendiz;
                    \Log::info('Usando aprendiz Sin Asignar existente con ID: ' . $aprendizSinAsignar->id_aprendiz);
                }
            }
            
            // Crear un registro en la tabla documentos
            $documentoId = DB::table('documentos')->insertGetId($dataToInsert);

            return response()->json([
                'success' => true,
                'message' => 'Evidencia registrada exitosamente.',
                'documento_id' => $documentoId
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al guardar evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener entregas de un proyecto
    public function obtenerEntregas($proyectoId)
    {
        try {
            if (!Schema::hasTable('documentos')) {
                return response()->json(['entregas' => []]);
            }

            // Verificar si existe columna descripcion
            $hasDescripcion = Schema::hasColumn('documentos', 'descripcion');
            
            // Obtener documentos reales de la tabla documentos
            $selectFields = [
                'd.id_documento as id',
                'd.documento as titulo',
                'd.ruta_archivo',
                'd.tipo_archivo',
                'd.tamanio',
                'd.fecha_subido as fecha',
                DB::raw("COALESCE(d.estado, 'pendiente') as estado"),
                DB::raw("COALESCE(a.nombre_completo, 'Sin asignar') as nombre_aprendiz"),
                'd.ruta_archivo as archivo_url',
                'd.documento as archivo_nombre'
            ];
            
            // Agregar descripción si la columna existe
            if ($hasDescripcion) {
                $selectFields[] = DB::raw("COALESCE(d.descripcion, '') as descripcion");
            } else {
                $selectFields[] = DB::raw("'' as descripcion");
            }
            
            $entregas = DB::table('documentos as d')
                ->leftJoin('aprendices as a', 'd.id_aprendiz', '=', 'a.id_aprendiz')
                ->where('d.id_proyecto', $proyectoId)
                // Ya no excluimos placeholders, ahora los usamos para evidencias creadas por el líder
                ->select($selectFields)
                ->orderBy('d.fecha_subido', 'desc')
                ->get();

            // Convertir rutas de archivo a URLs públicas y formatear datos
            $entregas = $entregas->map(function($entrega) {
                if ($entrega->ruta_archivo) {
                    // Si la ruta ya es una URL completa, usarla tal cual
                    if (filter_var($entrega->ruta_archivo, FILTER_VALIDATE_URL)) {
                        $entrega->archivo_url = $entrega->ruta_archivo;
                    } else {
                        // Si es una ruta relativa, convertirla a URL pública
                        $entrega->archivo_url = asset('storage/' . $entrega->ruta_archivo);
                    }
                }
                
                // Formatear fecha
                if ($entrega->fecha) {
                    try {
                        $fecha = new \DateTime($entrega->fecha);
                        $entrega->fecha = $fecha->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Mantener fecha original si hay error
                    }
                }
                
                return $entrega;
            });

            return response()->json(['entregas' => $entregas]);

        } catch (\Exception $e) {
            return response()->json([
                'entregas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    // Cambiar estado de una entrega
    public function cambiarEstadoEntrega(Request $request, $entregaId)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,aprobado,rechazado'
            ]);

            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            // Obtener el proyecto_id antes de actualizar
            $documento = DB::table('documentos')
                ->where('id_documento', $entregaId)
                ->first();

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            // Verificar si la columna estado existe, si no, agregarla temporalmente
            if (!Schema::hasColumn('documentos', 'estado')) {
                Schema::table('documentos', function($table) {
                    $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('fecha_subida');
                });
            }

            DB::table('documentos')
                ->where('id_documento', $entregaId)
                ->update([
                    'estado' => $request->estado
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'proyecto_id' => $documento->id_proyecto
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    // Actualizar documento completo (tipo_documento, fecha_limite, descripción)
    public function actualizarDocumento(Request $request, $documentoId)
    {
        try {
            $request->validate([
                'tipo_documento' => 'required|string',
                'fecha_limite' => 'required|date',
                'descripcion' => 'nullable|string'
            ]);

            if (!Schema::hasTable('documentos')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabla de documentos no encontrada'
                ], 404);
            }

            // Obtener el documento
            $documento = DB::table('documentos')
                ->where('id_documento', $documentoId)
                ->first();

            if (!$documento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            // Verificar y crear columnas si no existen
            if (!Schema::hasColumn('documentos', 'tipo_documento')) {
                Schema::table('documentos', function($table) {
                    $table->string('tipo_documento', 50)->nullable()->after('tipo_archivo');
                });
            }

            if (!Schema::hasColumn('documentos', 'fecha_limite')) {
                Schema::table('documentos', function($table) {
                    $table->date('fecha_limite')->nullable()->after('fecha_subida');
                });
            }

            if (!Schema::hasColumn('documentos', 'descripcion')) {
                Schema::table('documentos', function($table) {
                    $table->text('descripcion')->nullable()->after('estado');
                });
            }

            // Preparar datos para actualizar
            $updateData = [
                'tipo_documento' => $request->tipo_documento,
                'fecha_limite' => $request->fecha_limite
            ];

            // Agregar descripción
            if ($request->has('descripcion')) {
                $updateData['descripcion'] = $request->descripcion;
            }

            // Actualizar documento
            DB::table('documentos')
                ->where('id_documento', $documentoId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Documento actualizado exitosamente',
                'proyecto_id' => $documento->id_proyecto
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el documento: ' . $e->getMessage()
            ], 500);
        }
    }
}
