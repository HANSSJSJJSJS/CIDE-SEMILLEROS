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
        Schema::create('admins_and_lideres_generales', function (Blueprint $table) {
            $table->id();
            // agrega aquí las columnas que necesites, ejemplo:
            $table->string('nombre', 120);
            $table->unsignedBigInteger('id_usuario');
            // cualquier otra columna...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins_and_lideres_generales');
    }
};
