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
    Schema::table('archivos', function (Blueprint $table) {
        $table->unsignedBigInteger('proyecto_id')->nullable()->after('user_id');

        // si quieres relación foránea
        $table->foreign('proyecto_id')->references('id_proyecto')->on('proyectos')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            //
        });
    }
};
