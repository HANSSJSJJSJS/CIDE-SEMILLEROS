<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ajustar tabla migrations: asegurar id AUTO_INCREMENT (MySQL)
        try {
            DB::statement("ALTER TABLE `migrations` MODIFY `id` BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE `migrations` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        } catch (\Throwable $e) {
            // Si no existe PK, intentar agregarla
            try { DB::statement("ALTER TABLE `migrations` ADD PRIMARY KEY (`id`)"); } catch (\Throwable $e2) {}
            try { DB::statement("ALTER TABLE `migrations` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT"); } catch (\Throwable $e3) {}
        }

        // Ajustar tabla users: asegurar id AUTO_INCREMENT (MySQL)
        try {
            DB::statement("ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        } catch (\Throwable $e) {
            // Si no existe PK, intentar agregarla
            try { DB::statement("ALTER TABLE `users` ADD PRIMARY KEY (`id`)"); } catch (\Throwable $e2) {}
            try { DB::statement("ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT"); } catch (\Throwable $e3) {}
        }
    }

    public function down(): void
    {
        // No revertimos AUTO_INCREMENT automáticamente para evitar pérdida de integridad.
    }
};
