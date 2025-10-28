<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                if (!Schema::hasColumn('eventos', 'linea_investigacion')) {
                    $table->string('linea_investigacion')->nullable()->after('tipo');
                } else {
                    // Hacerla nullable si existe como NOT NULL
                    try {
                        $table->string('linea_investigacion')->nullable()->change();
                    } catch (\Throwable $e) {
                        // Si el driver no soporta change() sin doctrine/dbal, ignorar silenciosamente
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                // No eliminamos la columna para no romper datos; comenta si deseas revertir
                // $table->dropColumn('linea_investigacion');
            });
        }
    }
};
