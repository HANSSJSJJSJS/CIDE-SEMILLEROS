<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->enum('dirigido_a', ['todos', 'aprendices', 'lideres'])
                  ->default('todos')
                  ->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropColumn('dirigido_a');
        });
    }
};
