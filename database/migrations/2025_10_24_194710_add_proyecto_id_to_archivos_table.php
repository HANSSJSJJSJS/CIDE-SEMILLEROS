<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            // Si aún no existe, agrega la columna proyecto_id
            if (!Schema::hasColumn('archivos', 'proyecto_id')) {
                $table->unsignedBigInteger('proyecto_id')->nullable()->after('user_id');
            }

            // 🔹 Clave foránea correcta (usa id_proyecto)
            $table->foreign('proyecto_id')
                ->references('id_proyecto')
                ->on('proyectos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn('proyecto_id');
        });
    }
};
