<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Aprendiz que subió el archivo
            $table->string('nombre_original'); // Nombre original del archivo
            $table->string('nombre_almacenado'); // Nombre con que se guarda en el servidor
            $table->string('ruta'); // Ruta donde está guardado el archivo
            $table->string('mime_type')->nullable(); // Tipo MIME (ej: application/pdf)
            $table->timestamp('subido_en')->useCurrent(); // Fecha y hora de subida
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
