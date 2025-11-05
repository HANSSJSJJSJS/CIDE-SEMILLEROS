<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProyectoUserTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('proyecto_user')) {
            Schema::create('proyecto_user', function (Blueprint $table) {
                $table->id();

                // Claves (sin foráneas aún)
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('id_proyecto');

                $table->timestamps();

                // Índice único para evitar duplicados
                $table->unique(['user_id', 'id_proyecto']);
            });
        }

        // Agregar foráneas/índices solo si faltan, usando SQL crudo para evitar errores por duplicados
        $dbName = DB::getDatabaseName();

        // FK user_id -> users(id)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            $fk1 = DB::selectOne(
                "SELECT 1 AS exists_fk FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'proyecto_user_user_id_foreign'",
                [$dbName]
            );
            if (!$fk1) {
                try {
                    DB::statement("ALTER TABLE `proyecto_user` ADD CONSTRAINT `proyecto_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
                } catch (\Throwable $e) { /* noop */ }
            }
        }

        // FK id_proyecto -> proyectos(id_proyecto)
        if (Schema::hasTable('proyectos') && Schema::hasColumn('proyectos', 'id_proyecto')) {
            $fk2 = DB::selectOne(
                "SELECT 1 AS exists_fk FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'proyecto_user_id_proyecto_foreign'",
                [$dbName]
            );
            if (!$fk2) {
                try {
                    DB::statement("ALTER TABLE `proyecto_user` ADD CONSTRAINT `proyecto_user_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos`(`id_proyecto`) ON DELETE CASCADE");
                } catch (\Throwable $e) { /* noop */ }
            }
        }

        // Índice único en (user_id, id_proyecto)
        $uniqueIdx = DB::selectOne(
            "SELECT 1 AS exists_idx FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'proyecto_user' AND INDEX_NAME = 'proyecto_user_user_id_id_proyecto_unique'",
            [$dbName]
        );
        if (!$uniqueIdx) {
            try {
                DB::statement("CREATE UNIQUE INDEX `proyecto_user_user_id_id_proyecto_unique` ON `proyecto_user`(`user_id`,`id_proyecto`)");
            } catch (\Throwable $e) { /* noop */ }
        }
    }

    public function down()
    {
        Schema::dropIfExists('proyecto_user');
    }
}
