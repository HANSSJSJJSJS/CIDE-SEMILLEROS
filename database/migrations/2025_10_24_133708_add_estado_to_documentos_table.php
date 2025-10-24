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
    Schema::table('documentos', function (Blueprint $table) {
        $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
    });
}

public function down()
{
    Schema::table('documentos', function (Blueprint $table) {
        $table->dropColumn('estado');
    });
}
};
