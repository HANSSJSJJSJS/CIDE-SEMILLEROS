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
        Schema::table('documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('documentos', 'estado')) {
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                      ->default('pendiente')
                      ->after('fecha_subida');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (Schema::hasColumn('documentos', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
