<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            if (!Schema::hasColumn('aprendices','vinculado_sena')) {
                $table->boolean('vinculado_sena')->default(true)->after('programa');
            }
            if (!Schema::hasColumn('aprendices','institucion')) {
                $table->string('institucion',160)->nullable()->after('vinculado_sena');
            }
        });
    }
    public function down(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            if (Schema::hasColumn('aprendices','institucion'))   $table->dropColumn('institucion');
            if (Schema::hasColumn('aprendices','vinculado_sena')) $table->dropColumn('vinculado_sena');
        });
    }
};
