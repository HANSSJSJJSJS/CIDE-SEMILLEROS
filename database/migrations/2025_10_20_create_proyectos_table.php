<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id('id_proyecto');
            $table->foreignId('id_semillero')->constrained('semilleros');
            $table->foreignId('id_tipo_proyecto')->constrained('tipos_proyecto');
            $table->string('nombre_proyecto');
            $table->text('descripcion');
            $table->enum('estado', ['activo', 'inactivo', 'pendiente']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamp('creado_en')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('actualizado_en')->nullable()->default(null)->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
};
