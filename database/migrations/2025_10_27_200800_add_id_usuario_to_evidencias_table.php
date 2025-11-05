<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('evidencias') && !Schema::hasColumn('evidencias', 'id_usuario')) {
            Schema::table('evidencias', function (Blueprint $table) {
                if (Schema::hasColumn('evidencias', 'proyecto_id')) {
                    $table->unsignedBigInteger('id_usuario')->nullable()->after('proyecto_id')->index();
                } else {
                    $table->unsignedBigInteger('id_usuario')->nullable()->index();
                }
            });

            // Agregar FK a users solo si existe y si no está creada
            if (Schema::hasTable('users') && Schema::hasColumn('users','id')) {
                $db = DB::getDatabaseName();
                $fk = DB::selectOne(
                    "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'evidencias_id_usuario_foreign'",
                    [$db]
                );
                if (!$fk) {
                    try {
                        DB::statement("ALTER TABLE `evidencias` ADD CONSTRAINT `evidencias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users`(`id`) ON DELETE SET NULL");
                    } catch (\Throwable $e) { /* noop */ }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('evidencias')) {
            $db = DB::getDatabaseName();
            $fk = DB::selectOne(
                "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'evidencias_id_usuario_foreign'",
                [$db]
            );
            if ($fk) {
                try { DB::statement("ALTER TABLE `evidencias` DROP FOREIGN KEY `evidencias_id_usuario_foreign`"); } catch (\Throwable $e) { /* noop */ }
            }
            if (Schema::hasColumn('evidencias', 'id_usuario')) {
                Schema::table('evidencias', function (Blueprint $table) {
                    $table->dropColumn('id_usuario');
                });
            }
        }
    }
};
