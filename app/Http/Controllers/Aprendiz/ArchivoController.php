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

        // 2. Obtener proyectos del usuario (sin asumir pivote fija)
        $ids = $this->proyectoIdsUsuario((int)$user->id);
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

        return view('aprendiz.archivos.index', compact('proyectos', 'archivos', 'proyecto', 'fecha'));
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
                'id_proyecto' => $request->proyecto_id,
                'id_usuario'  => Auth::id(),
                'nombre'      => $nombreOriginal,
                'estado'      => 'pendiente',
            ]);

            $resultados[] = [
                'id' => $registro->id ?? null,
                'nombre_original' => $registro->nombre_original,
                'ruta' => Storage::disk('public')->url($registro->ruta),
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
