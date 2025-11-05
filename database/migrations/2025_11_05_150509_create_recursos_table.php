<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recursos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('nombre_archivo'); // nombre visible del archivo
            $table->string('archivo');        // ruta del archivo en storage/app/public/...
            $table->text('descripcion')->nullable();

            // Relación con el usuario que sube el recurso
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps(); // crea fecha de creación y modificación
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};
 