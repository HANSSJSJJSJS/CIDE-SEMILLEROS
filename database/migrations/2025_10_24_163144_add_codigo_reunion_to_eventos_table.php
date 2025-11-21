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
        if (Schema::hasTable('eventos')) {
            // Añadir columna si no existe
            if (!Schema::hasColumn('eventos', 'codigo_reunion')) {
                Schema::table('eventos', function (Blueprint $table) {
                    $table->string('codigo_reunion', 255)->nullable()->after('link_virtual');
                });
            }

            // Crear índice si no existe
            $db = DB::getDatabaseName();
            $idx = DB::selectOne(
                "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'eventos' AND INDEX_NAME = 'eventos_codigo_reunion_index'",
                [$db]
            );
            if (!$idx) {
                try {
                    Schema::table('eventos', function (Blueprint $table) {
                        $table->index('codigo_reunion');
                    });
                } catch (\Throwable $e) { /* noop */ }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('eventos')) {
            $db = DB::getDatabaseName();
            $idx = DB::selectOne(
                "SELECT 1 FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'eventos' AND INDEX_NAME = 'eventos_codigo_reunion_index'",
                [$db]
            );
            if ($idx) {
                try {
                    Schema::table('eventos', function (Blueprint $table) {
                        $table->dropIndex('eventos_codigo_reunion_index');
                    });
                } catch (\Throwable $e) { /* noop */ }
            }
            if (Schema::hasColumn('eventos', 'codigo_reunion')) {
                Schema::table('eventos', function (Blueprint $table) {
                    $table->dropColumn('codigo_reunion');
                });
            }
        }
    }
};
