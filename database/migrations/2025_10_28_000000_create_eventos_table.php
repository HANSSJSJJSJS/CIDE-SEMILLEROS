<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('eventos')) {
            Schema::create('eventos', function (Blueprint $table) {
                $table->bigIncrements('id_evento');
                // Quién creó/programó el evento (líder)
                $table->unsignedBigInteger('id_lider')->index();
                // (Opcional) usuario creador si lo usas en consultas
                $table->unsignedBigInteger('id_usuario')->nullable()->index();
                // Proyecto asociado
                $table->unsignedBigInteger('id_proyecto')->nullable()->index();

                $table->string('titulo');
                $table->string('tipo')->nullable();
                $table->text('descripcion')->nullable();
                $table->dateTime('fecha_hora')->index();
                $table->integer('duracion')->default(60); // minutos
                $table->string('ubicacion')->default('presencial'); // presencial | virtual | hibrido | otro
                $table->string('link_virtual')->nullable();
                $table->string('codigo_reunion')->nullable();
                $table->string('recordatorio')->nullable();

                $table->timestamps();

                // FKs suaves (evitan fallos en entornos sin integridad)
                if (Schema::hasTable('users')) {
                    $table->foreign('id_lider')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
                }
                if (Schema::hasTable('proyectos')) {
                    $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::dropIfExists('eventos');
        }
    }
};
