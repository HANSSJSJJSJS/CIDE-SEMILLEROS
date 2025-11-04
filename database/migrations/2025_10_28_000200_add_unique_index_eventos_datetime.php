<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                // Crear índice único por líder y fecha_hora para evitar doble reserva exacta
                $table->unique(['id_lider', 'fecha_hora'], 'eventos_lider_fecha_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                $table->dropUnique('eventos_lider_fecha_unique');
            });
        }
    }
};
