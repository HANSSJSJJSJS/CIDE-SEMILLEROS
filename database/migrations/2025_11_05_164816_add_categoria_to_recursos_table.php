<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('recursos', 'categoria')) {
            Schema::table('recursos', function (Blueprint $table) {
                $table->enum('categoria', ['plantillas', 'manuales', 'otros'])->default('otros')->after('archivo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('recursos', 'categoria')) {
            Schema::table('recursos', function (Blueprint $table) {
                $table->dropColumn('categoria');
            });
        }
    }
};