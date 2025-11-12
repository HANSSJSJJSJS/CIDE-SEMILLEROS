<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserDemoSeeder extends Seeder
{
    public function run(): void
    {
        $hasTelefono = DB::getSchemaBuilder()->hasColumn('users', 'telefono');

        // LIDER_INTERMEDIARIO demo (idempotente)
        $liderData = [
            'name'            => 'Lider Inter Demo',
            'apellidos'       => 'Suplente',
            'password'        => Hash::make('LiderInter123!'),
            'role'            => 'LIDER_INTERMEDIARIO',
            'remember_token'  => Str::random(10),
            'updated_at'      => now(),
            'created_at'      => now(),
        ];
        if ($hasTelefono) { $liderData['telefono'] = ''; }
        DB::table('users')->updateOrInsert(
            ['email' => 'lider.inter@example.com'],
            $liderData
        );
        $liderInterId = (int) DB::table('users')->where('email','lider.inter@example.com')->value('id');
    }
}
