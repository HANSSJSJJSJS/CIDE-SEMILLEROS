<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('evento_participantes')) {
            return; // nada que hacer
        }

        // Asegurar que la columna id sea AUTO_INCREMENT y PRIMARY KEY
        $statements = [
            // Normalizar tipo primero
            "ALTER TABLE `evento_participantes` MODIFY `id` BIGINT UNSIGNED NOT NULL",
            // Intentar quitar PK si existe (puede fallar si no hay PK)
            "ALTER TABLE `evento_participantes` DROP PRIMARY KEY",
            // Volver a agregar PK en id
            "ALTER TABLE `evento_participantes` ADD PRIMARY KEY (`id`)",
            // Hacerla auto_increment
            "ALTER TABLE `evento_participantes` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT",
        ];

        foreach ($statements as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Continuar para que sea idempotente en distintos estados
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('evento_participantes')) {
            return;
        }
        // Revertir AUTO_INCREMENT (dejarla como BIGINT UNSIGNED NOT NULL conservando PK)
        try {
            DB::statement("ALTER TABLE `evento_participantes` MODIFY `id` BIGINT UNSIGNED NOT NULL");
        } catch (\Throwable $e) {
            // noop
        }
    }
};
