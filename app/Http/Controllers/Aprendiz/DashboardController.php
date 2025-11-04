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

        foreach ($pivotTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $pcol = null; $ucol = null;
            foreach ($projCols as $c) { if (Schema::hasColumn($tbl, $c)) { $pcol = $c; break; } }
            foreach ($userCols as $c) { if (Schema::hasColumn($tbl, $c)) { $ucol = $c; break; } }
            if ($pcol && $ucol) {
                try {
                    return (int) DB::table($tbl)
                        ->where($ucol, $userId)
                        ->distinct()
                        ->count($pcol);
                } catch (\Exception $e) {
                    // intentar siguiente
                }
            }
        }

        // Fallback: contar proyectos desde documentos si existe esa relación implícita
        if (Schema::hasTable('documentos') && Schema::hasColumn('documentos','id_proyecto') && Schema::hasColumn('documentos','id_usuario')) {
            try {
                return (int) DB::table('documentos')
                    ->where('id_usuario', $userId)
                    ->distinct()
                    ->count('id_proyecto');
            } catch (\Exception $e) {}
        }

        return 0;
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
