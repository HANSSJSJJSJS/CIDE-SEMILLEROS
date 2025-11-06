<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\LiderSemillero;
use App\Models\Evidencia;
use App\Models\Archivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        return view('aprendiz.proyectos.proyecto', compact('proyectos'));
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
        $companeros = $proyecto->aprendices
            ->where('id_usuario', '!=', $user->id)
            ->values();

        // Líder del semillero (si el proyecto pertenece a un semillero con líder asociado)
        $lider = null;
        if ($proyecto->semillero && !empty($proyecto->semillero->id_lider_usuario)) {
            $lider = LiderSemillero::with('user')
                ->where('id_usuario', $proyecto->semillero->id_lider_usuario)
                ->first();
        }

        // Filtros de evidencias: por fecha exacta (created_at) y por nombre del compañero (autor)
        $fecha = request('fecha');
        $nombre = request('nombre');

        // Validación del nombre contra compañeros del proyecto (incluye al propio usuario si tiene registro en aprendices)
        $nombreError = null;
        $aplicarFiltroNombre = false;
        if ($nombre) {
            $aprendizActual = \App\Models\Aprendiz::where('id_usuario', $user->id)->first();
            $lista = $companeros->values();
            if ($aprendizActual) { $lista = $lista->push($aprendizActual); }

            $hayMatch = $lista->filter(function ($ap) use ($nombre) {
                $val = null;
                if (method_exists($ap, 'getAttribute')) {
                    $val = $ap->nombre_completo ?? trim(($ap->nombres ?? '') . ' ' . ($ap->apellidos ?? ''));
                }
                return $val && stripos($val, $nombre) !== false;
            })->isNotEmpty();

            if ($hayMatch) {
                $aplicarFiltroNombre = true;
            } else {
                $nombreError = 'No se encontró ningún compañero con ese nombre en este proyecto.';
            }
        }

        $evidencias = Evidencia::with(['autor'])
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->when($fecha, function ($q) use ($fecha) {
                $q->whereDate('created_at', $fecha);
            })
            ->when($aplicarFiltroNombre && $nombre, function ($q) use ($nombre) {
                $q->whereHas('autor', function ($s) use ($nombre) {
                    $s->whereRaw("CONCAT(COALESCE(nombres,''),' ',COALESCE(apellidos,'')) LIKE ?", ["%$nombre%"]); 
                });
            })
            ->orderByDesc('created_at')
            ->get();

        // Documentos del proyecto (para listarlos en el detalle)
        $archivos = Archivo::with('user')
            ->where('proyecto_id', $proyecto->id_proyecto)
            ->orderByDesc('subido_en')
            ->get();

        return view('aprendiz.proyectos.show_proyecto', compact('proyecto', 'companeros', 'lider', 'evidencias', 'fecha', 'nombre', 'nombreError', 'archivos'));
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
                    return DB::table('documentos')
                        ->where('id_usuario', $userId)
                        ->distinct()
                        ->pluck('id_proyecto')
                        ->map(fn($v)=> (int)$v)
                        ->all();
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


