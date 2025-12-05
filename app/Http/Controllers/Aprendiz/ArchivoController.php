<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Proyecto;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User si no lo está
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ArchivoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el usuario autenticado.
        $user = Auth::user();

        // 2. Obtener proyectos del usuario priorizando el pivote proyecto_user
        $ids = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_user')) {
            try {
                $ids = DB::table('proyecto_user')
                    ->where('user_id', (int)$user->id)
                    ->distinct()
                    ->pluck('id_proyecto')
                    ->map(fn($v)=> (int)$v)
                    ->all();
            } catch (\Exception $e) {
                $ids = [];
            }
        }
        if (empty($ids)) {
            $ids = $this->proyectoIdsUsuario((int)$user->id);
        }
        $proyectos = empty($ids)
            ? collect([])
            : Proyecto::whereIn('id_proyecto', $ids)->get();

        // 3. Filtros simples
        $proyecto = $request->query('proyecto');
        $fecha = $request->query('fecha');

        // 4. Consulta con filtros y paginación (si existe la tabla 'archivos')
        if (Schema::hasTable('archivos')) {
            $archivos = Archivo::with(['proyecto'])
                ->where('user_id', $user->id)
                ->when($proyecto, fn($q)=> $q->where('proyecto_id', $proyecto))
                ->when($fecha, fn($q)=> $q->whereDate('subido_en', $fecha))
                ->orderByDesc('subido_en')
                ->paginate(10)
                ->appends(['proyecto'=>$proyecto, 'fecha'=>$fecha]);
        } else {
            // Retornar paginador vacío si no existe la tabla
            $archivos = new LengthAwarePaginator([], 0, 10, (int)$request->get('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('aprendiz.archivos.archivo', compact('proyectos', 'archivos', 'proyecto', 'fecha'));
    }

    /**
     * Mostrar/stream del archivo subido por el aprendiz
     */
    public function show($id)
    {
        // Buscar registro y asegurar pertenencia al usuario autenticado
        if (!\Illuminate\Support\Facades\Schema::hasTable('archivos')) {
            abort(404);
        }

        $archivo = Archivo::find($id);
        if (!$archivo) {
            abort(404);
        }
        if ((int)$archivo->user_id !== (int)Auth::id()) {
            abort(403);
        }

        $ruta = $archivo->ruta;
        if (empty($ruta) || !Storage::disk('public')->exists($ruta)) {
            abort(404);
        }

        // Determinar si se debe mostrar inline o descargar
        $mime = $archivo->mime_type ?: Storage::disk('public')->mimeType($ruta);
        $filename = $archivo->nombre_original ?: basename($ruta);
        $headers = [
            'Content-Type' => $mime,
        ];

        // Para PDF mostrar inline; para otros tipos, también inline si el navegador lo soporta
        return Storage::disk('public')->response($ruta, $filename, $headers);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $ids = $this->proyectoIdsUsuario((int)$user->id);
        $proyectos = empty($ids)
            ? collect([])
            : Proyecto::whereIn('id_proyecto', $ids)->get();
        $proyectoSeleccionado = $request->query('proyecto');
        return view('aprendiz.archivos.upload', compact('proyectos', 'proyectoSeleccionado'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id_proyecto',
            'documentos' => 'required', // puede ser array o archivo único
            'documentos.*' => 'mimes:pdf|max:10240',
        ]);

        $files = [];
        if ($request->hasFile('documentos')) {
            $files = is_array($request->file('documentos'))
                ? $request->file('documentos')
                : [$request->file('documentos')];
        }

        $resultados = [];
        foreach ($files as $documento) {
            if (!$documento) { continue; }
            $nombreOriginal = $documento->getClientOriginalName();
            $nombreAlmacenado = uniqid().'_'.$nombreOriginal;
            $ruta = $documento->storeAs('documentos', $nombreAlmacenado, 'public');

            $registro = Archivo::create([
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreAlmacenado,
                'ruta' => $ruta,
                'proyecto_id' => $request->proyecto_id,
                'user_id' => Auth::id(),
                'estado' => 'aprobado',
                'mime_type' => $documento->getClientMimeType(),
                'subido_en' => now(),
            ]);

            // Crear evidencia asociada para reflejarse en el detalle del proyecto
            Evidencia::create([
                'proyecto_id' => $request->proyecto_id,
                'id_usuario'  => Auth::id(),
                'nombre'      => $nombreOriginal,
                'estado'      => 'pendiente',
            ]);

            // Construir URL pública (local public disk): usa /storage symlink
            $publicUrl = null;
            if (!empty($registro->ruta)) {
                $publicUrl = asset('storage/'.$registro->ruta);
            }

            $resultados[] = [
                'id' => $registro->id ?? null,
                'nombre_original' => $registro->nombre_original,
                'ruta' => $publicUrl,
                'estado' => $registro->estado,
                'mime_type' => $registro->mime_type,
                'subido_en' => $registro->subido_en,
            ];
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'archivos' => $resultados]);
        }

        return redirect()->route('aprendiz.archivos.index')
            ->with('success', 'Documentos subidos correctamente');
    }


    /**
     * Devuelve en JSON los archivos del usuario autenticado para un proyecto dado
     */
    public function listByProject(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $proyectoId = (int)$request->query('proyecto_id');

        // Seguridad: confirmar que el proyecto pertenece al usuario usando
        // la misma lógica robusta que en index() (soporta proyecto_user y aprendiz_proyecto)
        $idsUsuario = $this->proyectoIdsUsuario((int)$userId);
        if (empty($idsUsuario) || !in_array($proyectoId, array_map('intval', $idsUsuario), true)) {
            return response()->json(['ok'=>false,'archivos'=>[]], 200);
        }

        if (!Schema::hasTable('archivos')) {
            return response()->json(['ok'=>true,'archivos'=>[]]);
        }

        $items = Archivo::where('user_id', $userId)
            ->where('proyecto_id', $proyectoId)
            ->orderByDesc('subido_en')
            ->get()
            ->map(function($r){
                $publicUrl = !empty($r->ruta) ? asset('storage/'.$r->ruta) : null;
                return [
                    'id' => $r->id ?? null,
                    'nombre' => $r->nombre_original ?? basename($r->ruta ?? ''),
                    'mime' => $r->mime_type ?? null,
                    'url' => $publicUrl,
                    'fecha' => optional($r->subido_en)->format('Y-m-d H:i') ?? (string)($r->subido_en ?? ''),
                ];
            })->values();

        return response()->json(['ok'=>true,'archivos'=>$items]);
    }

    public function destroy(Archivo $archivo)
    {
        if ($archivo->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($archivo->ruta);
        $archivo->delete();

        return redirect()->route('aprendiz.archivos.index')
            ->with('success', 'Documento eliminado correctamente');
    }

    private function proyectoIdsUsuario(int $userId): array
    {
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
                    // Si la pivote usa id_aprendiz/aprendiz_id, mapear desde users -> aprendices
                    $aprColsPivot = ['id_aprendiz','aprendiz_id','idAprendiz'];
                    if (in_array($ucol, $aprColsPivot, true)) {
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
                        continue; // probar siguiente pivote si no mapeó
                    }

                    // Caso normal: la pivote referencia directamente al user id
                    return DB::table($tbl)
                        ->where($ucol, $userId)
                        ->distinct()
                        ->pluck($pcol)
                        ->map(fn($v)=> (int)$v)
                        ->all();
                } catch (\Exception $e) {}
            }
        }

        // Fallback: documentos como relación implícita
        if (Schema::hasTable('documentos')) {
            if (Schema::hasColumn('documentos','id_usuario') && Schema::hasColumn('documentos','id_proyecto')) {
                try {
                    return DB::table('documentos')
                        ->where('id_usuario', $userId)
                        ->distinct()
                        ->pluck('id_proyecto')
                        ->map(fn($v)=> (int)$v)
                        ->all();
                } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('documentos','id_aprendiz') && Schema::hasTable('aprendices')) {
                $aprId = null;
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
                if (Schema::hasColumn('aprendices','id_usuario')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('id_usuario', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','user_id')) {
                    foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('user_id', $userId)->value($pk); if (!is_null($aprId)) break; }
                }
                if (is_null($aprId) && Schema::hasColumn('aprendices','email')) {
                    $email = DB::table('users')->where('id', $userId)->value('email');
                    if ($email) {
                        foreach ($aprPkCols as $pk) { $aprId = DB::table('aprendices')->where('email', $email)->value($pk); if (!is_null($aprId)) break; }
                    }
                }
                if ($aprId) {
                    try {
                        return DB::table('documentos')
                            ->where('id_aprendiz', $aprId)
                            ->distinct()
                            ->pluck('id_proyecto')
                            ->map(fn($v)=> (int)$v)
                            ->all();
                    } catch (\Exception $e) {}
                }
            }
        }

        return [];
    }
}
