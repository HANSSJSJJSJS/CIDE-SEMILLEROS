<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('evidencias')) {
            Schema::create('evidencias', function (Blueprint $table) {
                $table->id('id_evidencia');

                // Clave (sin FK por ahora)
                $table->unsignedBigInteger('proyecto_id');

                // Datos de la evidencia
                $table->string('nombre');
                $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
                $table->timestamps();
            });
        }

        // Agregar FK solo si existe proyectos.id_proyecto y la FK no existe aún
        $db = DB::getDatabaseName();
        if (Schema::hasTable('proyectos') && Schema::hasColumn('proyectos', 'id_proyecto')) {
            $exists = DB::selectOne(
                "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND CONSTRAINT_NAME = 'evidencias_proyecto_id_foreign'",
                [$db]
            );
            if (!$exists) {
                try {
                    DB::statement("ALTER TABLE `evidencias` ADD CONSTRAINT `evidencias_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id_proyecto`) ON DELETE CASCADE");
                } catch (\Throwable $e) { /* noop */ }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('evidencias');
    }
};
