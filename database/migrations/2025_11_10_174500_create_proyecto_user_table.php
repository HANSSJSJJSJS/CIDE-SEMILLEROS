<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('proyecto_user')) {
            Schema::create('proyecto_user', function (Blueprint $table) {
                $table->id();

                // IMPORTANTE: proyectos.id_proyecto = INT(10) UNSIGNED
                $table->unsignedInteger('id_proyecto');

                // users.id = BIGINT UNSIGNED
                $table->unsignedBigInteger('user_id');

                $table->timestamps();

                // Evitar duplicados
                $table->unique(['id_proyecto', 'user_id']);

                // FK a proyectos.id_proyecto
                $table->foreign('id_proyecto')
                    ->references('id_proyecto')
                    ->on('proyectos')
                    ->onDelete('cascade');

                // FK a users.id
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_user');
    }
};
