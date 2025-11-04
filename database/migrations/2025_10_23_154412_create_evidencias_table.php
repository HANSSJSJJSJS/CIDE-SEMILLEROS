<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id('id_evidencia');

            // Clave foránea correcta hacia proyectos.id_proyecto
            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')
                  ->references('id_proyecto')
                  ->on('proyectos')
                  ->onDelete('cascade');

            // Datos de la evidencia
            $table->string('nombre');
            $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evidencias');
    }
};
