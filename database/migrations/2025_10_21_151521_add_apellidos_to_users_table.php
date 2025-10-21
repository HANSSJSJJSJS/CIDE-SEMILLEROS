<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // agregamos la columna 'apellidos' después de 'name'
            if (!Schema::hasColumn('users', 'apellidos')) {
                $table->string('apellidos', 120)->after('name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'apellidos')) {
                $table->dropColumn('apellidos');
            }
        });
    }
};
