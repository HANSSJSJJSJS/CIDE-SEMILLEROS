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
    if (Schema::hasTable('aprendices') && !Schema::hasColumn('aprendices', 'estado')) {
        Schema::table('aprendices', function (Blueprint $table) {
            if (Schema::hasColumn('aprendices', 'id_usuario')) {
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo')->after('id_usuario');
            } else {
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            }
        });
    }
}

public function down(): void
{
    if (Schema::hasTable('aprendices') && Schema::hasColumn('aprendices', 'estado')) {
        Schema::table('aprendices', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}

};
