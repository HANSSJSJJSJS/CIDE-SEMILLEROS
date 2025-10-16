<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Tabla APRENDICES
        if (Schema::hasTable('aprendices')) {
            Schema::table('aprendices', function (Blueprint $table) {
                // Si la columna existe, intentamos eliminar FK y columna de forma segura
                if (Schema::hasColumn('aprendices', 'id_tipo_documento')) {
                    try {
                        DB::statement('ALTER TABLE aprendices DROP FOREIGN KEY IF EXISTS aprendices_id_tipo_documento_foreign');
                    } catch (\Exception $e) {
                        // Ignorar si no existe
                    }

                    try {
                        $table->dropColumn('id_tipo_documento');
                    } catch (\Exception $e) {
                        // Ignorar si ya no existe
                    }
                }

                // Crear de nuevo la columna
                if (!Schema::hasColumn('aprendices', 'id_tipo_documento')) {
                    $table->integer('id_tipo_documento')->nullable()->after('programa');
                }
            });
        }

        // ✅ Tabla LIDERES_SEMILLERO
        if (Schema::hasTable('lideres_semillero')) {
            Schema::table('lideres_semillero', function (Blueprint $table) {
                if (Schema::hasColumn('lideres_semillero', 'id_tipo_documento')) {
                    try {
                        DB::statement('ALTER TABLE lideres_semillero DROP FOREIGN KEY IF EXISTS lideres_semillero_id_tipo_documento_foreign');
                    } catch (\Exception $e) {
                        // Ignorar si no existe
                    }

                    try {
                        $table->dropColumn('id_tipo_documento');
                    } catch (\Exception $e) {
                        // Ignorar si ya no existe
                    }
                }

                if (!Schema::hasColumn('lideres_semillero', 'id_tipo_documento')) {
                    $table->integer('id_tipo_documento')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aprendices')) {
            Schema::table('aprendices', function (Blueprint $table) {
                if (Schema::hasColumn('aprendices', 'id_tipo_documento')) {
                    try {
                        $table->dropColumn('id_tipo_documento');
                    } catch (\Exception $e) {
                        // Ignorar si ya no existe
                    }
                }
            });
        }

        if (Schema::hasTable('lideres_semillero')) {
            Schema::table('lideres_semillero', function (Blueprint $table) {
                if (Schema::hasColumn('lideres_semillero', 'id_tipo_documento')) {
                    try {
                        $table->dropColumn('id_tipo_documento');
                    } catch (\Exception $e) {
                        // Ignorar si ya no existe
                    }
                }
            });
        }
    }
};
