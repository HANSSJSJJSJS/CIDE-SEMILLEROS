<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla APRENDICES
        Schema::table('aprendices', function (Blueprint $table) {
            // Elimina la FK o columna anterior si existe
            if (Schema::hasColumn('aprendices', 'id_tipo_documento')) {
                $table->dropForeign(['id_tipo_documento']); // evita error si tenía FK
                $table->dropColumn('id_tipo_documento');
            }
            // Crea el nuevo campo INT
            $table->integer('id_tipo_documento')->nullable()->after('programa');
        });

        // Tabla LIDERES_SEMILLERO
        Schema::table('lideres_semillero', function (Blueprint $table) {
            if (Schema::hasColumn('lideres_semillero', 'id_tipo_documento')) {
                $table->dropForeign(['id_tipo_documento']);
                $table->dropColumn('id_tipo_documento');
            }
            $table->integer('id_tipo_documento')->nullable()->after('apellidos');
        });
    }

    public function down(): void
    {
        // Si haces rollback, puedes revertir a FK o string (ajústalo según tu estructura anterior)
        Schema::table('aprendices', function (Blueprint $table) {
            $table->dropColumn('id_tipo_documento');
        });
        Schema::table('lideres_semillero', function (Blueprint $table) {
            $table->dropColumn('id_tipo_documento');
        });
    }
};
