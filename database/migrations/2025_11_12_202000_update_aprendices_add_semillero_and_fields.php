<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            if (!Schema::hasColumn('aprendices', 'vinculado_sena')) {
                $table->boolean('vinculado_sena')->default(1)->after('programa');
            }
            if (!Schema::hasColumn('aprendices', 'institucion')) {
                $table->string('institucion', 160)->nullable()->after('vinculado_sena');
            }
            if (!Schema::hasColumn('aprendices', 'semillero_id')) {
                $table->unsignedBigInteger('semillero_id')->nullable()->after('contacto_celular');
            }
        });

        // Cambiar columnas a nullable (usar SQL directo para evitar dependencia a doctrine/dbal)
        // Asegurar que las columnas existen antes de cambiar
        if (Schema::hasColumn('aprendices', 'ficha')) {
            DB::statement("ALTER TABLE `aprendices` MODIFY `ficha` varchar(30) NULL");
        }
        if (Schema::hasColumn('aprendices', 'programa')) {
            DB::statement("ALTER TABLE `aprendices` MODIFY `programa` varchar(160) NULL");
        }

        // Índice y FK para semillero_id
        Schema::table('aprendices', function (Blueprint $table) {
            if (Schema::hasColumn('aprendices', 'semillero_id')) {
                if (!self::indexExists('aprendices', 'aprendices_semillero_fk')) {
                    $table->index('semillero_id', 'aprendices_semillero_fk');
                }
            }
        });

        // Agregar constraint con SQL directo para controlar nombre igual al dump
        if (Schema::hasColumn('aprendices', 'semillero_id')) {
            // Evitar error si ya existe
            $exists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'aprendices_semillero_fk'");
            if (empty($exists)) {
                DB::statement("ALTER TABLE `aprendices` ADD CONSTRAINT `aprendices_semillero_fk` FOREIGN KEY (`semillero_id`) REFERENCES `semilleros`(`id_semillero`)");
            }
        }
    }

    public function down(): void
    {
        // Quitar FK si existe
        $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'aprendices_semillero_fk'");
        if (!empty($fkExists)) {
            DB::statement("ALTER TABLE `aprendices` DROP FOREIGN KEY `aprendices_semillero_fk`");
        }

        Schema::table('aprendices', function (Blueprint $table) {
            // Quitar índice si existe
            if (self::indexExists('aprendices', 'aprendices_semillero_fk')) {
                $table->dropIndex('aprendices_semillero_fk');
            }

            if (Schema::hasColumn('aprendices', 'semillero_id')) {
                $table->dropColumn('semillero_id');
            }
            if (Schema::hasColumn('aprendices', 'institucion')) {
                $table->dropColumn('institucion');
            }
            if (Schema::hasColumn('aprendices', 'vinculado_sena')) {
                $table->dropColumn('vinculado_sena');
            }
        });

        // Revertir nullable a NOT NULL (si aplica)
        if (Schema::hasColumn('aprendices', 'ficha')) {
            DB::statement("ALTER TABLE `aprendices` MODIFY `ficha` varchar(30) NOT NULL");
        }
        if (Schema::hasColumn('aprendices', 'programa')) {
            DB::statement("ALTER TABLE `aprendices` MODIFY `programa` varchar(160) NOT NULL");
        }
    }

    private static function indexExists(string $table, string $index): bool
    {
        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($result);
    }
};
