<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class UserPermissionController extends Controller
{
    // Retorna permisos actuales por usuario (JSON)
    public function show($userId): JsonResponse
    {
        // Solo ADMIN puede ver/editar permisos
        $role = strtoupper(str_replace([' ', '-'], '_', Auth::user()->role ?? ''));
        if (!in_array($role, ['ADMIN','LIDER_GENERAL'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perms = DB::table('user_module_permissions')
            ->where('user_id', $userId)
            ->get()
            ->mapWithKeys(function ($row) {
                // Normaliza nombre histórico 'reuniones' al actual 'reuniones-lideres'
                $key = ($row->module === 'reuniones') ? 'reuniones-lideres' : $row->module;
                return [
                    $key => [
                        'can_create' => (bool)$row->can_create,
                        'can_update' => (bool)$row->can_update,
                        'can_delete' => (bool)$row->can_delete,
                    ],
                ];
            });

        return response()->json(['data' => $perms]);
    }

    // Guarda permisos por usuario (POST)
    public function update(Request $request, $userId): JsonResponse
    {
        $actorRole = strtoupper(str_replace([' ', '-'], '_', Auth::user()->role ?? ''));
        // Nueva política: Solo el SuperAdmin (LIDER_GENERAL) puede otorgar/revocar
        if ($actorRole !== 'LIDER_GENERAL') {
            return response()->json(['message' => 'Solo el Líder General puede modificar estos permisos'], 403);
        }

        // Solo se permiten permisos para usuarios con rol ADMIN (CIDEINNOVA)
        $target = \App\Models\User::find($userId);
        if (!$target) {
            return response()->json(['message' => 'Usuario destino no encontrado'], 404);
        }
        $targetRole = strtoupper(str_replace([' ', '-'], '_', $target->role ?? ''));
        if ($targetRole !== 'ADMIN') {
            return response()->json([
                'message' => 'Los permisos solo aplican a usuarios con rol ADMIN.'
            ], 422);
        }

        try {
            $modules = $request->input('modules', []);
            if (!is_array($modules)) { $modules = []; }

            // Lista blanca de módulos controlados desde UI
            $allowedModules = ['usuarios','semilleros','recursos','reuniones-lideres'];

            DB::transaction(function () use ($userId, $modules, $allowedModules) {
                foreach ($allowedModules as $m) {
                    $vals = $modules[$m] ?? [];
                    $create = !empty($vals['can_create']);
                    $update = !empty($vals['can_update']);
                    $delete = !empty($vals['can_delete']);

                    // Si no hay ningún permiso, eliminamos la fila para mantener limpio
                    if (!$create && !$update && !$delete) {
                        DB::table('user_module_permissions')
                            ->where(['user_id' => $userId, 'module' => $m])
                            ->delete();
                        continue;
                    }

                    DB::table('user_module_permissions')->updateOrInsert(
                        ['user_id' => $userId, 'module' => $m],
                        [
                            'can_create' => $create ? 1 : 0,
                            'can_update' => $update ? 1 : 0,
                            'can_delete' => $delete ? 1 : 0,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            });

            return response()->json(['message' => 'Permisos actualizados']);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'message' => 'No se pudieron guardar los permisos.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
