<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('aprendiz_proyectos')) {
            Schema::create('aprendiz_proyectos', function (Blueprint $table) {
                $table->unsignedInteger('id_proyecto');
                $table->unsignedBigInteger('id_aprendiz');
                $table->primary(['id_proyecto','id_aprendiz']);
                // Si prefieres FKs, descomenta estas líneas (y asegúrate de que existan las tablas/keys):
                // $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('cascade');
                // $table->foreign('id_aprendiz')->references('id_aprendiz')->on('aprendices')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aprendiz_proyectos')) {
            Schema::dropIfExists('aprendiz_proyectos');
        }
    }
};
