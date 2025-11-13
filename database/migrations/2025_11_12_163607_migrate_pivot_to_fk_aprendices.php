<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        // 1) Agregar columna e índice (nullable para no romper nada)
        Schema::table('aprendices', function (Blueprint $table) {
            if (!Schema::hasColumn('aprendices', 'semillero_id')) {
                $table->unsignedBigInteger('semillero_id')->nullable()->after('contacto_celular');
                $table->index('semillero_id', 'aprendices_semillero_id_index');
            }
        });

        // 2) Agregar FK (sin Doctrine). Ignora si ya existe o si hay error.
        if (Schema::hasTable('semilleros')) {
            try {
                DB::statement("
                    ALTER TABLE `aprendices`
                    ADD CONSTRAINT `aprendices_semillero_fk`
                    FOREIGN KEY (`semillero_id`) REFERENCES `semilleros`(`id_semillero`)
                    ON DELETE RESTRICT
                ");
            } catch (\Throwable $e) {
                // Silenciar si la FK ya existe o si hay otro detalle
                // Puedes loguearlo si quieres: logger($e->getMessage());
            }
        }
    }

    public function down(): void {
        // 1) Quitar FK e índice si existen (sin Doctrine)
        try {
            DB::statement("ALTER TABLE `aprendices` DROP FOREIGN KEY `aprendices_semillero_fk`");
        } catch (\Throwable $e) {}

        Schema::table('aprendices', function (Blueprint $table) {
            try { $table->dropIndex('aprendices_semillero_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('aprendices', 'semillero_id')) {
                $table->dropColumn('semillero_id');
            }
        });
    }
};
