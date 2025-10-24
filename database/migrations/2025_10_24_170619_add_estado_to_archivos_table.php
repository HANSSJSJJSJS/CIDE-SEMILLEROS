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
    Schema::table('archivos', function (Blueprint $table) {
        $table->enum('estado', ['pendiente', 'aprobado'])->default('pendiente')->after('ruta');
    });
}

public function down(): void
{
    Schema::table('archivos', function (Blueprint $table) {
        $table->dropColumn('estado');
    });
}

};
