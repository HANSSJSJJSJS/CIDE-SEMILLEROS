<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            // Asegúrate de que la columna exista y sea unsignedBigInteger
            // $table->unsignedBigInteger('user_id')->change(); // requiere doctrine/dbal si necesitas cambiar tipo
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');  // si borras el user, borra sus recursos
        });
    }

    public function down(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
