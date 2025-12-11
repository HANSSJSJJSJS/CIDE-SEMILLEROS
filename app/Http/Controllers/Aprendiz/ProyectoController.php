<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\LiderSemillero;
use App\Models\Evidencia;
use App\Models\Archivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProyectoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // IDs de proyectos asignados al usuario (robusto sin suponer pivote fija)
        $ids = $this->proyectoIdsUsuario((int)$user->id);
        $proyectos = empty($ids)
            ? collect([])
            : Proyecto::whereIn('id_proyecto', $ids)
                ->with(['semillero'])
                ->get();

        // Conteos para tarjetas
        $countProyectos = $proyectos->count();
        $countPendientes = 0;
        $countCompletos = 0;
        if (!empty($ids)) {
            try {
                $countPendientes = Archivo::where('user_id', $user->id)
                    ->whereIn('proyecto_id', $ids)
                    ->where('estado', 'pendiente')
                    ->count();
                $countCompletos = Archivo::where('user_id', $user->id)
                    ->whereIn('proyecto_id', $ids)
                    ->whereIn('estado', ['aprobado','completo','completado'])
                    ->count();
            } catch (\Throwable $e) {
                $countPendientes = 0; $countCompletos = 0;
            }
        }

        // Stats por proyecto (para progreso y X/Y) – igual que líder: totales por proyecto en documentos
        $stats = [];
        foreach ($proyectos as $p) {
            $subidos = 0; $completos = 0; $pendientes = 0;
            try {
                if (Schema::hasTable('documentos')) {
                    // Totales por proyecto (como líder): entregas = total documentos; aprobadas = estado=aprobado; pendientes = estado=pendiente
                    $base = DB::table('documentos')->where('id_proyecto', $p->id_proyecto);
                    $subidos = (clone $base)->count();
                    if (Schema::hasColumn('documentos','estado')) {
                        $completos = (clone $base)->where('estado','aprobado')->count();
                        $pendientes = (clone $base)->where('estado','pendiente')->count();
                    } else {
                        $completos = $subidos;
                        $pendientes = 0;
                    }
                }
            } catch (\Throwable $e) {}
            $den = max(1, $subidos);
            $pct = (int) round(($completos / $den) * 100);
            $stats[$p->id_proyecto] = [
                'subidos' => $subidos,
                'completos' => $completos,
                'pendientes' => $pendientes,
                'progreso_pct' => $pct,
            ];
        }

        return view('aprendiz.proyectos.proyecto', compact('proyectos','countProyectos','countPendientes','countCompletos','stats'));
    }

    public function show($id)
    {
        $user = Auth::user();
        // Verificar pertenencia del usuario al proyecto
        $ids = $this->proyectoIdsUsuario((int)$user->id);
        if (!in_array((int)$id, array_map('intval', $ids), true)) {
            abort(404);
        }
        // Cargar proyecto
        $proyecto = Proyecto::with(['semillero', 'aprendices', 'evidencias'])
            ->where('id_proyecto', $id)
            ->firstOrFail();

        // Compañeros (otros aprendices asignados al proyecto)
        $aprUserCol = Schema::hasTable('aprendices')
            ? (Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : (Schema::hasColumn('aprendices','user_id') ? 'user_id' : null))
            : null;
        $companeros = $proyecto->aprendices;
        if ($aprUserCol) {
            $companeros = $companeros->filter(function($ap) use ($aprUserCol, $user){
                $val = null;
                if (method_exists($ap, 'getAttribute')) { $val = $ap->getAttribute($aprUserCol); }
                return (int)$val !== (int)$user->id;
            })->values();
        } else {
            $companeros = $companeros->values();
        }

        // Fallback si la relación está vacía: construir compañeros desde pivotes disponibles
        if ($companeros->isEmpty()) {
            $idsApr = collect();
            if (Schema::hasTable('aprendiz_proyecto')) {
                $idsApr = $idsApr->merge(
                    DB::table('aprendiz_proyecto')->where('id_proyecto', $proyecto->id_proyecto)->pluck('id_aprendiz')
                );
            }
            if (Schema::hasTable('proyecto_user')) {
                $userIds = DB::table('proyecto_user')->where('id_proyecto', $proyecto->id_proyecto)->pluck('user_id');
                if ($userIds->isNotEmpty() && Schema::hasTable('aprendices')) {
                    $aprUserFk = Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario'
                                 : (Schema::hasColumn('aprendices','user_id') ? 'user_id' : null);
                    if ($aprUserFk) {
                        $idsApr = $idsApr->merge(DB::table('aprendices')->whereIn($aprUserFk, $userIds)->pluck('id_aprendiz'));
                    }
                }
            }
            $idsApr = $idsApr->filter()->unique()->values();
            if ($idsApr->isNotEmpty() && Schema::hasTable('aprendices')) {
                $companeros = DB::table('aprendices')->whereIn('id_aprendiz', $idsApr)->get()->map(function($ap){ return (object)$ap; });
                // excluir al usuario actual si podemos mapear
                if ($aprUserCol) {
                    $companeros = $companeros->filter(function($ap) use ($aprUserCol, $user){ return (int)($ap->$aprUserCol ?? 0) !== (int)$user->id; })->values();
                }
            }
        }

        // Líder del semillero (tolerante a variaciones de columnas)
        $lider = null;
        if ($proyecto->semillero) {
            // Detectar posibles columnas que apunten al líder
            $sem = $proyecto->semillero;
            $leaderCandidates = [
                $sem->id_lider_semi ?? null,
                $sem->id_lider_usuario ?? null,
                $sem->id_lider ?? null,
                $sem->id_usuario ?? null,
                $sem->lider_id ?? null,
            ];
            $leaderId = collect($leaderCandidates)->filter(fn($v)=> !empty($v))->first();

            if ($leaderId) {
                // 1) Intentar por PK de lideres_semillero (id_lider_semi)
                $lider = LiderSemillero::with('user')->where('id_lider_semi', $leaderId)->first();
                // 2) Intentar por columna alternativa id_usuario en lideres_semillero
                if (!$lider && \Illuminate\Support\Facades\Schema::hasColumn('lideres_semillero','id_usuario')) {
                    $lider = LiderSemillero::with('user')->where('id_usuario', $leaderId)->first();
                }
                // 3) Fallback: construir desde Users si existe ese id
                if (!$lider && class_exists(User::class)) {
                    $u = User::find($leaderId);
                    if ($u) {
                        $nombre = trim(($u->nombre ?? '').' '.($u->apellidos ?? '')) ?: ($u->name ?? null);
                        $lidObj = new \stdClass();
                        $lidObj->nombre_completo = $nombre ?: null;
                        $lidObj->correo_institucional = $u->email ?? null;
                        $userObj = new \stdClass();
                        $userObj->email = $u->email ?? null;
                        $lidObj->user = $userObj;
                        $lider = $lidObj;
                    }
                }
            }
            // Fallback adicional: si el semillero no guarda el id del líder, buscar en lideres_semillero por id_semillero
            if (!$lider && isset($sem->id_semillero)) {
                $lider = LiderSemillero::with('user')
                    ->where('id_semillero', $sem->id_semillero)
                    ->orderByDesc(\Illuminate\Support\Facades\Schema::hasColumn('lideres_semillero','actualizado_en') ? 'actualizado_en' : 'id_lider_semi')
                    ->first();
            }
        }

        // Filtros de evidencias: por fecha exacta (created_at) y por nombre del compañero (autor)
        $fecha = request('fecha');
        // Normalizar fecha posible en formato dd/mm/yyyy a Y-m-d
        if (is_string($fecha) && strpos($fecha, '/') !== false) {
            $parts = explode('/', $fecha);
            if (count($parts) === 3) {
                [$d,$m,$y] = $parts;
                if (checkdate((int)$m,(int)$d,(int)$y)) { $fecha = sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d); }
            }
        }
        $nombre = request('nombre');

        // Validación del nombre contra compañeros del proyecto (incluye al propio usuario si tiene registro en aprendices)
        $nombreError = null;
        $aplicarFiltroNombre = false;
        if ($nombre) {
            // Resolver aprendiz actual por columna disponible (id_usuario o user_id)
            $aprendizActual = null;
            if ($aprUserCol) {
                $aprendizActual = \App\Models\Aprendiz::where($aprUserCol, $user->id)->first();
            }
            $lista = $companeros->values();
            if ($aprendizActual) { $lista = $lista->push($aprendizActual); }

            $hayMatch = $lista->filter(function ($ap) use ($nombre) {
                $val = null;
                if (method_exists($ap, 'getAttribute')) {
                    $val = $ap->nombre_completo ?? trim(($ap->nombres ?? '') . ' ' . ($ap->apellidos ?? ''));
                }
                return $val && stripos($val, $nombre) !== false;
            })->isNotEmpty();

            // Aplicar filtro aunque no se encuentre coincidencia previa; nombreError es informativo
            $aplicarFiltroNombre = true;
            if (!$hayMatch) { $nombreError = 'No se encontró ningún compañero con ese nombre en este proyecto.'; }
        }

        // Construir evidencias desde 'documentos' del proyecto (incluye asignadas y subidas)
        $docAprCol = $this->getDocumentoAprendizColumn();
        $queryDocs = DB::table('documentos')
            ->where('documentos.id_proyecto', $proyecto->id_proyecto);

        if ($fecha) {
            $dateCol = Schema::hasColumn('documentos','fecha_subida') ? 'fecha_subida' : (Schema::hasColumn('documentos','created_at') ? 'created_at' : null);
            if ($dateCol) { $queryDocs->whereDate($dateCol, $fecha); }
        }

        if ($aplicarFiltroNombre && $nombre) {
            // Intentar filtrar por nombre del aprendiz asociado al documento
            $nameExpr = Schema::hasTable('aprendices') && Schema::hasColumn('aprendices','nombre_completo')
                ? 'aprendices.nombre_completo'
                : "CONCAT(COALESCE(aprendices.nombres,''),' ',COALESCE(aprendices.apellidos,''))";
            $ownerKey = null; // columna en aprendices que empata con documentos.$docAprCol
            foreach (['id_aprendiz','id','id_usuario','user_id'] as $cand) { if (Schema::hasColumn('aprendices',$cand)) { $ownerKey = $cand; break; } }
            if ($ownerKey) {
                $queryDocs->leftJoin('aprendices', DB::raw('aprendices.'.$ownerKey), '=', DB::raw('documentos.'.$docAprCol))
                          ->whereRaw("$nameExpr LIKE ?", ["%$nombre%"]);
            } else {
                // Fallback: filtrar por users.name si docAprCol apunta a id de usuario
                if ($docAprCol === 'id_usuario' && Schema::hasTable('users')) {
                    $queryDocs->leftJoin('users', 'users.id', '=', 'documentos.id_usuario')
                              ->where('users.name', 'LIKE', "%$nombre%");
                }
            }
        }

        $docs = $queryDocs->orderByDesc(Schema::hasColumn('documentos','fecha_subida') ? 'fecha_subida' : 'id_documento')->get();

        // Mapear a estructura esperada por la vista
        $evidencias = $docs->map(function($d){
            $estado = isset($d->estado) ? strtolower((string)$d->estado) : null;
            if (!$estado) {
                // Derivar por presencia de archivo (ruta_archivo no vacío)
                $hasFile = isset($d->ruta_archivo) && trim((string)$d->ruta_archivo) !== '';
                $estado = $hasFile ? 'completado' : 'pendiente';
            }
            $created = $d->fecha_subida ?? ($d->created_at ?? null);
            return (object) [
                'id_documento' => $d->id_documento ?? null,
                'nombre' => $d->documento ?? 'Evidencia',
                'estado' => $estado,
                'created_at' => $created ? (new \Carbon\Carbon($created)) : null,
                'has_file' => isset($d->ruta_archivo) && trim((string)$d->ruta_archivo) !== '',
                // El autor lo resolverá la vista con $ev->autor opcional, aquí lo dejamos nulo
                'autor' => null,
            ];
        });

        // Documentos del proyecto (para listarlos en el detalle)
        if (Schema::hasTable('archivos')) {
            $archivos = Archivo::with('user')
                ->where('proyecto_id', $proyecto->id_proyecto)
                ->orderByDesc('subido_en')
                ->get();
        } elseif (Schema::hasTable('documentos')) {
            // Fallback: usar la tabla documentos y mapear columnas a las esperadas por la vista
            $archivos = DB::table('documentos')
                ->where('id_proyecto', $proyecto->id_proyecto)
                ->orderByDesc('fecha_subida')
                ->select([
                    DB::raw('documento as nombre_original'),
                    DB::raw('ruta_archivo as ruta')
                ])->get();
        } else {
            $archivos = collect();
        }

        return view('aprendiz.proyectos.show_proyecto', compact('proyecto', 'companeros', 'lider', 'evidencias', 'fecha', 'nombre', 'nombreError', 'archivos'));
    }

    private function proyectoIdsUsuario(int $userId): array
    {
        // Caso directo para el esquema más común: tabla aprendiz_proyecto + aprendices
        if (Schema::hasTable('aprendiz_proyecto') && Schema::hasTable('aprendices')) {
            try {
                $aprTbl = 'aprendices';
                $aprPkCol = Schema::hasColumn($aprTbl,'id_aprendiz') ? 'id_aprendiz' : (Schema::hasColumn($aprTbl,'id') ? 'id' : null);
                $aprUserFk = null;
                foreach (['id_usuario','user_id','id_user'] as $cand) {
                    if (Schema::hasColumn($aprTbl,$cand)) { $aprUserFk = $cand; break; }
                }
                if ($aprPkCol && $aprUserFk && Schema::hasColumn('aprendiz_proyecto','id_aprendiz') && Schema::hasColumn('aprendiz_proyecto','id_proyecto')) {
                    $aprId = DB::table($aprTbl)->where($aprUserFk, $userId)->value($aprPkCol);
                    if ($aprId) {
                        $ids = DB::table('aprendiz_proyecto')
                            ->where('id_aprendiz', $aprId)
                            ->distinct()
                            ->pluck('id_proyecto')
                            ->map(fn($v)=> (int)$v)
                            ->all();
                        if (!empty($ids)) {
                            Log::info('Aprendiz/ProyectoController@proyectoIdsUsuario direct aprendiz_proyecto', [
                                'user_id' => $userId,
                                'aprendiz_id' => $aprId,
                                'ids' => $ids,
                            ]);
                            return $ids;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Aprendiz/ProyectoController@proyectoIdsUsuario direct aprendiz_proyecto error', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Intentar con pivotes conocidas
        $pivotTables = ['proyecto_user', 'aprendiz_proyecto', 'aprendices_proyectos', 'aprendiz_proyectos', 'proyecto_aprendiz', 'proyectos_aprendices', 'proyecto_aprendices'];
        $projCols   = ['id_proyecto','proyecto_id','idProyecto'];
        $userCols   = ['user_id','id_usuario','id_aprendiz','aprendiz_id','idAprendiz'];

        foreach ($pivotTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $pcol = null; $ucol = null;
            foreach ($projCols as $c) { if (Schema::hasColumn($tbl, $c)) { $pcol = $c; break; } }
            foreach ($userCols as $c) { if (Schema::hasColumn($tbl, $c)) { $ucol = $c; break; } }
            if ($pcol && $ucol) {
                try {
                    // Si la columna de usuario en la pivote representa un id de aprendiz, debemos mapear desde users -> aprendices
                    $aprColsPivot = ['id_aprendiz','aprendiz_id','idAprendiz'];
                    if (in_array($ucol, $aprColsPivot, true)) {
                        // Determinar cómo relacionar aprendices con users
                        $aprTable = 'aprendices';
                        if (Schema::hasTable($aprTable)) {
                            $userFkCandidates = ['id_usuario','user_id'];
                            $aprPkCandidates  = ['id_aprendiz','id'];
                            $userFkCol = null; $aprPkCol = null;
                            foreach ($userFkCandidates as $cand) { if (Schema::hasColumn($aprTable, $cand)) { $userFkCol = $cand; break; } }
                            foreach ($aprPkCandidates as $cand) { if (Schema::hasColumn($aprTable, $cand)) { $aprPkCol = $cand; break; } }
                            if ($userFkCol && $aprPkCol) {
                                $aprendizIds = DB::table($aprTable)
                                    ->where($userFkCol, $userId)
                                    ->pluck($aprPkCol)
                                    ->map(fn($v)=> (int)$v)
                                    ->all();
                            if (!empty($aprendizIds)) {
                                return DB::table($tbl)
                                    ->whereIn($ucol, $aprendizIds)
                                    ->distinct()
                                    ->pluck($pcol)
                                    ->map(fn($v)=> (int)$v)
                                    ->all();
                                }
                            }
                        }
                        // Si no hay manera de mapear, seguir al siguiente intento
                        continue;
                    }

                    // Caso normal: la columna en pivote referencia directamente al user id
                    $res = DB::table($tbl)
                        ->where($ucol, $userId)
                        ->distinct()
                        ->pluck($pcol)
                        ->map(fn($v)=> (int)$v)
                        ->all();
                    if (!empty($res)) { return $res; }
                } catch (\Exception $e) {
                    // probar siguiente
                }
            }
        }

        // Fallback: documentos como relación implícita proyecto-usuario/aprendiz
        if (Schema::hasTable('documentos')) {
            // Priorizar documentos.id_usuario si existe
            if (Schema::hasColumn('documentos','id_usuario') && Schema::hasColumn('documentos','id_proyecto')) {
                try {
                    Log::info('Aprendiz/ProyectoController@fallback documentos.id_usuario', ['user_id' => $userId]);
                    $res = DB::table('documentos')
                        ->where('id_usuario', $userId)
                        ->distinct()
                        ->pluck('id_proyecto')
                        ->map(fn($v)=> (int)$v)
                        ->all();
                    if (!empty($res)) { return $res; }
                } catch (\Exception $e) {}
            }
            // Si no, intentar mapear via aprendices -> documentos.id_aprendiz
            if (Schema::hasColumn('documentos','id_aprendiz') && Schema::hasTable('aprendices')) {
                $aprId = null;
                // Determinar columnas PK disponibles realmente en 'aprendices'
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                // Intentar resolver por id_usuario, luego user_id, luego email
                if (Schema::hasColumn('aprendices','id_usuario')) {
                    foreach ($aprPkCols as $pk) {
                        $aprId = DB::table('aprendices')->where('id_usuario', $userId)->value($pk);
                        if (!is_null($aprId)) break;
                    }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','user_id')) {
                    foreach ($aprPkCols as $pk) {
                        $aprId = DB::table('aprendices')->where('user_id', $userId)->value($pk);
                        if (!is_null($aprId)) break;
                    }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','email')) {
                    $email = DB::table('users')->where('id', $userId)->value('email');
                    if ($email) {
                        foreach ($aprPkCols as $pk) {
                            $aprId = DB::table('aprendices')->where('email', $email)->value($pk);
                            if (!is_null($aprId)) break;
                        }
                    }
                }
                if ($aprId) {
                    try {
                        Log::info('Aprendiz/ProyectoController@fallback documentos.id_aprendiz', ['aprendiz_id' => $aprId]);
                        $res = DB::table('documentos')
                            ->where('id_aprendiz', $aprId)
                            ->distinct()
                            ->pluck('id_proyecto')
                            ->map(fn($v)=> (int)$v)
                            ->all();
                        if (!empty($res)) { return $res; }
                    } catch (\Exception $e) {}
                }
            }
        }

        // Fallback final: proyectos del semillero del aprendiz
        if (Schema::hasTable('aprendices') && Schema::hasTable('proyectos') && Schema::hasColumn('proyectos','id_semillero')) {
            try {
                // Detectar FK user->aprendiz
                $aprTbl = 'aprendices';
                $userFkCol = null; foreach (['id_usuario','user_id','id_user'] as $c) { if (Schema::hasColumn($aprTbl,$c)) { $userFkCol = $c; break; } }
                $aprPkCol  = Schema::hasColumn($aprTbl,'id_aprendiz') ? 'id_aprendiz' : (Schema::hasColumn($aprTbl,'id') ? 'id' : null);
                if ($userFkCol) {
                    // Resolver columna de semillero en aprendices: semillero_id o id_semillero
                    $semCol = Schema::hasColumn($aprTbl,'semillero_id') ? 'semillero_id' : (Schema::hasColumn($aprTbl,'id_semillero') ? 'id_semillero' : null);
                    if ($semCol) {
                        $semilleroId = DB::table($aprTbl)->where($userFkCol, $userId)->value($semCol);
                        if ($semilleroId) {
                            $ids = DB::table('proyectos')->where('id_semillero', $semilleroId)->pluck('id_proyecto')->map(fn($v)=>(int)$v)->all();
                            if (!empty($ids)) {
                                Log::info('Aprendiz/ProyectoController@fallback semillero', ['user_id' => $userId, 'semillero_id' => $semilleroId, 'ids' => $ids, 'sem_col' => $semCol]);
                                return $ids;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Aprendiz/ProyectoController@fallback semillero error', ['user_id' => $userId, 'error' => $e->getMessage()]);
            }
        }

        Log::info('Aprendiz/ProyectoController@sin proyectos', ['user_id' => $userId]);
        return [];
    }

    // Detectar la columna en 'documentos' que referencia al aprendiz
    private function getDocumentoAprendizColumn(): string
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz')) { return 'id_aprendiz'; }
        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario')) { return 'id_usuario'; }
        return 'id_aprendiz';
    }
}


