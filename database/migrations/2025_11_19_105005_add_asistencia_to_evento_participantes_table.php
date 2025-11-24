<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento_participantes', function (Blueprint $table) {
            $table->enum('asistencia', ['pendiente', 'asistio', 'no_asistio'])
                  ->default('pendiente')
                  ->after('id_aprendiz');
        });
    }

    public function down(): void
    {
        Schema::table('evento_participantes', function (Blueprint $table) {
            $table->dropColumn('asistencia');
        });
    }
};
