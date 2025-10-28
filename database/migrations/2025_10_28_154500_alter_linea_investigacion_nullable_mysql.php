<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            try {
                DB::statement("ALTER TABLE `eventos` MODIFY `linea_investigacion` VARCHAR(255) NULL");
            } catch (\Throwable $e) {
                // Ignorar si la columna no existe o ya es NULL
            }
        }
    }

    public function down(): void
    {
        // No revertimos para no forzar NOT NULL nuevamente
    }
};
