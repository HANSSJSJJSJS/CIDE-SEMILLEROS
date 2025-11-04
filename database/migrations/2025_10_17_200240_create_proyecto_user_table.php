<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectoUserTable extends Migration
{
    public function up()
    {
        Schema::create('proyecto_user', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_proyecto');

            $table->timestamps();

            // Constraints (foráneas)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('cascade');

            // Evitar duplicados
            $table->unique(['user_id', 'id_proyecto']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyecto_user');
    }
}
