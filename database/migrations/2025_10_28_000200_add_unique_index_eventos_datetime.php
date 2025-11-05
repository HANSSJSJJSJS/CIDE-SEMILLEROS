<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('eventos')) return;

        // Detectar la columna de líder disponible en tu BD
        $leaderCol = null;
        if (Schema::hasColumn('eventos','id_lider_semi')) $leaderCol = 'id_lider_semi';
        elseif (Schema::hasColumn('eventos','id_lider_usuario')) $leaderCol = 'id_lider_usuario';
        elseif (Schema::hasColumn('eventos','id_lider')) $leaderCol = 'id_lider';

        if ($leaderCol) {
            // Crear índice sólo si no existe
            $db = DB::getDatabaseName();
            $exists = DB::selectOne(
                "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'eventos' AND INDEX_NAME = 'eventos_lider_fecha_unique'",
                [$db]
            );
            if (!$exists) {
                Schema::table('eventos', function (Blueprint $table) use ($leaderCol) {
                    $table->unique([$leaderCol, 'fecha_hora'], 'eventos_lider_fecha_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('eventos')) return;
        $db = DB::getDatabaseName();
        $exists = DB::selectOne(
            "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'eventos' AND INDEX_NAME = 'eventos_lider_fecha_unique'",
            [$db]
        );
        if ($exists) {
            try {
                Schema::table('eventos', function (Blueprint $table) {
                    $table->dropUnique('eventos_lider_fecha_unique');
                });
            } catch (\Throwable $e) { /* noop */ }
        }
    }
};
