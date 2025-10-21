<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@cide.edu.co'],
            [
                'name' => 'Administrador General',
                'apellidos' => 'Sistema',
                'password' => Hash::make('Admin123*'),
            ]
        );

        // Si usas roles
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Administrador');
        }
    }
}
