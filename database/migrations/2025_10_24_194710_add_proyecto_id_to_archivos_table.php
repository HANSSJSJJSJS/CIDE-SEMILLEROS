<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('archivos')) {
            // Asegurar columna
            if (!Schema::hasColumn('archivos', 'proyecto_id')) {
                Schema::table('archivos', function (Blueprint $table) {
                    if (Schema::hasColumn('archivos', 'user_id')) {
                        $table->unsignedBigInteger('proyecto_id')->nullable()->after('user_id');
                    } else {
                        $table->unsignedBigInteger('proyecto_id')->nullable();
                    }
                });
            }

            // Normalizar tipo de la columna para que coincida con proyectos.id_proyecto (INT UNSIGNED)
            try { DB::statement("ALTER TABLE `archivos` MODIFY `proyecto_id` INT UNSIGNED NULL"); } catch (\Throwable $e) { /* noop */ }

            // Agregar FK solo si existe y no está creada
            if (Schema::hasTable('proyectos') && Schema::hasColumn('proyectos', 'id_proyecto')) {
                $db = DB::getDatabaseName();
                $fk = DB::selectOne(
                    "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'archivos_proyecto_id_foreign'",
                    [$db]
                );
                if (!$fk) {
                    try {
                        DB::statement("ALTER TABLE `archivos` ADD CONSTRAINT `archivos_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id_proyecto`) ON DELETE CASCADE");
                    } catch (\Throwable $e) { /* noop */ }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('archivos')) {
            $db = DB::getDatabaseName();
            $fk = DB::selectOne(
                "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'archivos_proyecto_id_foreign'",
                [$db]
            );
            if ($fk) {
                try { DB::statement("ALTER TABLE `archivos` DROP FOREIGN KEY `archivos_proyecto_id_foreign`"); } catch (\Throwable $e) { /* noop */ }
            }
            if (Schema::hasColumn('archivos', 'proyecto_id')) {
                Schema::table('archivos', function (Blueprint $table) {
                    $table->dropColumn('proyecto_id');
                });
            }
        }
    }
};
