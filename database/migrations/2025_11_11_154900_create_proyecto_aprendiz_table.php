<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('proyecto_aprendiz')) {
            Schema::create('proyecto_aprendiz', function (Blueprint $table) {

                $table->bigIncrements('id');

                // Aquí uso los tipos correctos SEGÚN TU BD real
                $table->unsignedInteger('id_proyecto');   // INT(10) UNSIGNED en tabla proyectos
                $table->unsignedBigInteger('id_aprendiz'); // BIGINT UNSIGNED en tabla aprendices

                $table->timestamps();

                $table->unique(['id_proyecto', 'id_aprendiz'], 'proyecto_aprendiz_unique');

                // FK correctas
                $table->foreign('id_proyecto')
                    ->references('id_proyecto')->on('proyectos')
                    ->onDelete('cascade');

                $table->foreign('id_aprendiz')
                    ->references('id_aprendiz')->on('aprendices')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_aprendiz');
    }
};
