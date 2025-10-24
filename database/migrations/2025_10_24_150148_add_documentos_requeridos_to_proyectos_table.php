<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (! Schema::hasColumn('proyectos', 'documentos_requeridos')) {
                $table->integer('documentos_requeridos')->default(3)->after('descripcion');
            }
        });
    }

    public function down()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (Schema::hasColumn('proyectos', 'documentos_requeridos')) {
                $table->dropColumn('documentos_requeridos');
            }
        });
    }
};
