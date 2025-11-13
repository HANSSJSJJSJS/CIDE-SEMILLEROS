<?php

namespace App\Http\Controllers\Aprendiz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Archivo;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔹 Contar proyectos del aprendiz de forma robusta (sin asumir pivote fijo)
        $proyectosCount = $this->contarProyectosUsuario((int)$user->id);

        // 🔹 Contar documentos por estado de forma robusta (tabla 'documentos')
        $documentosPendientes = $this->contarDocumentosUsuario((int)$user->id, 'pendiente');
        $documentosCompletados = $this->contarDocumentosUsuario((int)$user->id, 'aprobado');

        // 🔹 Calcular progreso promedio
        $totalDocumentos = $documentosPendientes + $documentosCompletados;
        $progresoPromedio = $totalDocumentos > 0
            ? round(($documentosCompletados / $totalDocumentos) * 100, 2)
            : 0;

        return view('aprendiz.dashboard-aprendiz', compact(
            'user',
            'proyectosCount',
            'documentosPendientes',
            'documentosCompletados',
            'progresoPromedio'
        ));
    }

    // 🔹 Endpoint dinámico (AJAX)
    public function stats()
    {
        $user = Auth::user();

        $proyectosCount = $this->contarProyectosUsuario((int)$user->id);

        $documentosPendientes = $this->contarDocumentosUsuario((int)$user->id, 'pendiente');
        $documentosCompletados = $this->contarDocumentosUsuario((int)$user->id, 'aprobado');

        $totalDocumentos = $documentosPendientes + $documentosCompletados;
        $progresoPromedio = $totalDocumentos > 0
            ? round(($documentosCompletados / $totalDocumentos) * 100, 2)
            : 0;

        return response()->json([
            'proyectosCount' => $proyectosCount,
            'documentosPendientes' => $documentosPendientes,
            'documentosCompletados' => $documentosCompletados,
            'progresoPromedio' => $progresoPromedio,
        ]);
    }

    private function contarProyectosUsuario(int $userId): int
    {
        // Candidatos de tabla pivote y columnas
        $pivotTables = ['proyecto_user', 'aprendiz_proyecto', 'aprendices_proyectos', 'aprendiz_proyectos', 'proyecto_aprendiz', 'proyectos_aprendices', 'proyecto_aprendices'];
        $projCols   = ['id_proyecto','proyecto_id','idProyecto'];
        $userCols   = ['user_id','id_usuario','id_aprendiz','aprendiz_id','idAprendiz'];

        $ids = collect();

        foreach ($pivotTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $pcol = null; $ucol = null;
            foreach ($projCols as $c) { if (Schema::hasColumn($tbl, $c)) { $pcol = $c; break; } }
            foreach ($userCols as $c) { if (Schema::hasColumn($tbl, $c)) { $ucol = $c; break; } }
            if ($pcol && $ucol) {
                try {
                    // Si la columna de usuario en la pivote representa un id de aprendiz, mapear antes
                    $aprColsPivot = ['id_aprendiz','aprendiz_id','idAprendiz'];
                    if (in_array($ucol, $aprColsPivot, true)) {
                        if (Schema::hasTable('aprendices')) {
                            $userFkCandidates = ['id_usuario','user_id'];
                            $aprPkCandidates  = ['id_aprendiz','id'];
                            $userFkCol = null; $aprPkCol = null;
                            foreach ($userFkCandidates as $cand) { if (Schema::hasColumn('aprendices', $cand)) { $userFkCol = $cand; break; } }
                            foreach ($aprPkCandidates as $cand) { if (Schema::hasColumn('aprendices', $cand)) { $aprPkCol = $cand; break; } }
                            if ($userFkCol && $aprPkCol) {
                                $aprendizIds = DB::table('aprendices')
                                    ->where($userFkCol, $userId)
                                    ->pluck($aprPkCol)
                                    ->filter()
                                    ->all();
                                if (!empty($aprendizIds)) {
                                    $ids = $ids->merge(
                                        DB::table($tbl)
                                            ->whereIn($ucol, $aprendizIds)
                                            ->distinct()
                                            ->pluck($pcol)
                                    );
                                }
                            }
                        }
                    } else {
                        // Caso normal: la columna en pivote referencia directamente al user id
                        $ids = $ids->merge(
                            DB::table($tbl)
                                ->where($ucol, $userId)
                                ->distinct()
                                ->pluck($pcol)
                        );
                    }
                } catch (\Exception $e) {
                    // continuar con el siguiente pivote
                }
            }
        }

        // Fallbacks desde documentos
        if (Schema::hasTable('documentos') && Schema::hasColumn('documentos','id_proyecto')) {
            // 1) documentos.id_usuario
            if (Schema::hasColumn('documentos','id_usuario')) {
                try {
                    $ids = $ids->merge(
                        DB::table('documentos')
                            ->where('id_usuario', $userId)
                            ->distinct()
                            ->pluck('id_proyecto')
                    );
                } catch (\Exception $e) {}
            }
            // 2) documentos.id_aprendiz mapeando desde users -> aprendices
            if (Schema::hasColumn('documentos','id_aprendiz') && Schema::hasTable('aprendices')) {
                $aprId = null;
                $aprPkCols = [];
                if (Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCols[] = 'id_aprendiz'; }
                if (Schema::hasColumn('aprendices','id')) { $aprPkCols[] = 'id'; }
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
                if (!is_null($aprId)) {
                    try {
                        $ids = $ids->merge(
                            DB::table('documentos')
                                ->where('id_aprendiz', $aprId)
                                ->distinct()
                                ->pluck('id_proyecto')
                        );
                    } catch (\Exception $e) {}
                }
            }
        }

        return $ids->filter()->map(fn($v)=> (int)$v)->unique()->count();
    }

    private function contarDocumentosUsuario(int $userId, string $estado): int
    {
        if (!Schema::hasTable('documentos')) return 0;
        if (!Schema::hasColumn('documentos', 'estado')) return 0;

        // Caso 1: documentos.id_usuario existe
        if (Schema::hasColumn('documentos', 'id_usuario')) {
            return (int) DB::table('documentos')
                ->where('id_usuario', $userId)
                ->where('estado', $estado)
                ->count();
        }

        // Caso 2: documentos.id_aprendiz y mapear desde el usuario
        if (Schema::hasColumn('documentos', 'id_aprendiz')) {
            $aprendizId = null;

            // Determinar columnas disponibles en aprendices
            $aprTableHasIdApr = Schema::hasColumn('aprendices', 'id_aprendiz');
            $aprPkCol = $aprTableHasIdApr ? 'id_aprendiz' : (Schema::hasColumn('aprendices','id') ? 'id' : null);

            if ($aprPkCol) {
                // 1) Preferir mapear por email (más portable entre esquemas)
                if (Schema::hasColumn('aprendices','email')) {
                    $userEmail = DB::table('users')->where('id', $userId)->value('email');
                    if ($userEmail) {
                        $apr = Aprendiz::select($aprPkCol)->where('email', $userEmail)->first();
                        $aprendizId = $apr ? $apr->{$aprPkCol} : null;
                    }
                }

                // 2) Relación directa por id_usuario/user_id si existen
                if ($aprendizId === null && Schema::hasColumn('aprendices','id_usuario')) {
                    $apr = Aprendiz::select($aprPkCol)->where('id_usuario', $userId)->first();
                    $aprendizId = $apr ? $apr->{$aprPkCol} : null;
                } elseif ($aprendizId === null && Schema::hasColumn('aprendices','user_id')) {
                    $apr = Aprendiz::select($aprPkCol)->where('user_id', $userId)->first();
                    $aprendizId = $apr ? $apr->{$aprPkCol} : null;
                }
            }

            if ($aprendizId === null) return 0;

            return (int) DB::table('documentos')
                ->where('id_aprendiz', $aprendizId)
                ->where('estado', $estado)
                ->count();
        }

        return 0;
    }
}
