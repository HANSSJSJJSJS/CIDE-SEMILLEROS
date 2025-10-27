<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            $table->string('nombre_completo', 255)->nullable()->after('apellidos');
        });
        
        // Actualizar registros existentes concatenando nombres y apellidos
        DB::statement('UPDATE aprendices SET nombre_completo = CONCAT(COALESCE(nombres, ""), " ", COALESCE(apellidos, "")) WHERE nombre_completo IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            $table->dropColumn('nombre_completo');
        });
    }
};
