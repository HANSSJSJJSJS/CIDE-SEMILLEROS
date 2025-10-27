<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('id_evento');
            $table->unsignedBigInteger('id_lider')->nullable();
            $table->unsignedBigInteger('id_proyecto')->nullable();
            $table->string('titulo');
            $table->string('tipo')->default('general'); // planificacion, seguimiento, revision, capacitacion, general
            $table->dateTime('fecha_hora');
            $table->integer('duracion')->default(60); // duración en minutos
            $table->string('ubicacion')->nullable(); // sala1, sala2, lab1, lab2, virtual, otra
            $table->string('recordatorio')->default('none'); // none, 15, 30, 60, 1440 (minutos)
            $table->timestamps();

            // Foreign key solo para usuarios (más segura)
            if (Schema::hasTable('users')) {
                $table->foreign('id_lider')->references('id')->on('users')->onDelete('cascade');
            }
            
            // No agregamos foreign key para proyectos para evitar problemas de compatibilidad
            // La integridad se manejará a nivel de aplicación
        });

        // Tabla pivot para participantes del evento
        Schema::create('evento_participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_evento');
            $table->unsignedBigInteger('id_aprendiz');
            $table->timestamps();

            // Foreign keys solo si las tablas existen
            if (Schema::hasTable('eventos')) {
                $table->foreign('id_evento')->references('id_evento')->on('eventos')->onDelete('cascade');
            }
            
            if (Schema::hasTable('aprendices')) {
                $table->foreign('id_aprendiz')->references('id_aprendiz')->on('aprendices')->onDelete('cascade');
            }
            
            $table->unique(['id_evento', 'id_aprendiz']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_participantes');
        Schema::dropIfExists('eventos');
    }
};
