<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('evento_participantes')) {
            Schema::create('evento_participantes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('id_evento');
                $table->unsignedBigInteger('id_aprendiz'); // usamos id_aprendiz como clave externa a aprendices
                $table->timestamps();

                $table->unique(['id_evento','id_aprendiz']);

                if (Schema::hasTable('eventos')) {
                    $table->foreign('id_evento')->references('id_evento')->on('eventos')->onDelete('cascade');
                }
                if (Schema::hasTable('aprendices')) {
                    // La PK del modelo Aprendiz está configurada como id_usuario, pero aquí la mayoría de controladores usan id_aprendiz
                    // Por compatibilidad, dejamos la FK suave (solo índice si no coincide la PK real)
                    try {
                        $table->foreign('id_aprendiz')->references('id_aprendiz')->on('aprendices')->onDelete('cascade');
                    } catch (\Throwable $e) {
                        // Si falla porque no existe id_aprendiz como PK, al menos dejamos index
                        $table->index('id_aprendiz');
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_participantes');
    }
};
