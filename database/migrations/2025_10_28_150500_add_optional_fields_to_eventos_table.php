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
                if (!Schema::hasColumn('eventos', 'descripcion')) {
                    $table->text('descripcion')->nullable()->after('tipo');
                }
                if (!Schema::hasColumn('eventos', 'link_virtual')) {
                    $table->string('link_virtual')->nullable()->after('ubicacion');
                }
                if (!Schema::hasColumn('eventos', 'codigo_reunion')) {
                    $table->string('codigo_reunion', 50)->nullable()->after('link_virtual');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eventos')) {
            Schema::table('eventos', function (Blueprint $table) {
                if (Schema::hasColumn('eventos', 'codigo_reunion')) {
                    $table->dropColumn('codigo_reunion');
                }
                if (Schema::hasColumn('eventos', 'link_virtual')) {
                    $table->dropColumn('link_virtual');
                }
                if (Schema::hasColumn('eventos', 'descripcion')) {
                    $table->dropColumn('descripcion');
                }
            });
        }
    }
};
