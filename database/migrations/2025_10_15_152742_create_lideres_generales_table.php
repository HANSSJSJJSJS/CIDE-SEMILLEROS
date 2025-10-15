<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Agregar columna "nombre" a las dos tablas
        Schema::table('administradores', function (Blueprint $table) {
            $table->string('nombre', 120)->after('id_usuario');
        });

        Schema::table('lideres_generales', function (Blueprint $table) {
            $table->string('nombre', 120)->after('id_usuario');
        });

        // ✅ Rellenar los nombres con los datos de users
        DB::statement("
            UPDATE administradores a
            JOIN users u ON u.id = a.id_usuario
            SET a.nombre = u.name
        ");
        DB::statement("
            UPDATE lideres_generales l
            JOIN users u ON u.id = l.id_usuario
            SET l.nombre = u.name
        ");
    }

    public function down(): void
    {
        Schema::table('administradores', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
        Schema::table('lideres_generales', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
};
