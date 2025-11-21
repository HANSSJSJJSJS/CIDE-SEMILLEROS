<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    if (Schema::hasTable('archivos')) {
        // Agregar columna si no existe
        if (!Schema::hasColumn('archivos', 'proyecto_id')) {
            Schema::table('archivos', function (Blueprint $table) {
                // Si no existe user_id, no usamos after() para evitar errores
                if (Schema::hasColumn('archivos', 'user_id')) {
                    $table->unsignedBigInteger('proyecto_id')->nullable()->after('user_id');
                } else {
                    $table->unsignedBigInteger('proyecto_id')->nullable();
                }
            });
        }

        // Agregar FK solo si existen tabla/columna y si no existe ya la restricción
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


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('archivos')) {
            $db = DB::getDatabaseName();
            // Quitar FK si existe
            $fk = DB::selectOne(
                "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'archivos_proyecto_id_foreign'",
                [$db]
            );
            if ($fk) {
                try { DB::statement("ALTER TABLE `archivos` DROP FOREIGN KEY `archivos_proyecto_id_foreign`"); } catch (\Throwable $e) { /* noop */ }
            }
            // Quitar columna si existe
            if (Schema::hasColumn('archivos', 'proyecto_id')) {
                Schema::table('archivos', function (Blueprint $table) {
                    $table->dropColumn('proyecto_id');
                });
            }
        }
    }
};
