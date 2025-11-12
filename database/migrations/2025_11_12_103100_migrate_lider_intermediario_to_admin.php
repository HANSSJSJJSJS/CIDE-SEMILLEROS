<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Migrar todos los usuarios con rol LIDER_INTERMEDIARIO a ADMIN
        DB::table('users')
            ->whereRaw("UPPER(REPLACE(REPLACE(TRIM(role),' ',''),'-','_')) = 'LIDER_INTERMEDIARIO'")
            ->update(['role' => 'ADMIN']);
    }

    public function down(): void
    {
        // Revertir: no es determinístico si algunos ADMIN ya eran ADMIN antes.
        // Solo revertimos los que tengan permisos finos o un flag claro no disponible,
        // así que dejamos un down inofensivo que no cambie nada.
        // Si se necesita reversión exacta, hágase manualmente.
    }
};
