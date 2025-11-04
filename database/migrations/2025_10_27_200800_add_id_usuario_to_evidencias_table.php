<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            if (!Schema::hasColumn('evidencias', 'id_usuario')) {
                $table->unsignedBigInteger('id_usuario')->nullable()->after('id_proyecto')->index();
                // Si existe tabla users, agrega FK. En entornos sin FK compatibles, puedes comentar esta línea.
                if (Schema::hasTable('users')) {
                    $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            if (Schema::hasColumn('evidencias', 'id_usuario')) {
                try {
                    $table->dropForeign(['id_usuario']);
                } catch (Throwable $e) {
                    // Ignorar si no existe la FK
                }
                $table->dropColumn('id_usuario');
            }
        });
    }
};
