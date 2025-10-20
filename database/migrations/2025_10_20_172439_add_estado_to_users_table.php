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
    Schema::table('aprendices', function (Blueprint $table) {
        $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo')->after('id_usuario');
    });
}

public function down(): void
{
    Schema::table('aprendices', function (Blueprint $table) {
        $table->dropColumn('estado');
    });
}

};
