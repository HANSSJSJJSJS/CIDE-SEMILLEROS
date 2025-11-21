<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Opción 1: SQL directo (no requiere doctrine/dbal)
        DB::statement('ALTER TABLE recursos MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        // Si quieres revertir (opcional): quitar AUTO_INCREMENT
        DB::statement('ALTER TABLE recursos MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
    }
};
