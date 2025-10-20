<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Crear 3 proyectos de prueba
        $proyectosData = [
            ['nombre' => 'Sistema de Gestión de Fichas', 'descripcion' => 'Proyecto para automatizar el seguimiento de aprendices.'],
            ['nombre' => 'Aplicación Móvil SENA', 'descripcion' => 'Desarrollo de una app informativa para el centro.'],
            ['nombre' => 'Dashboard de Monitoreo IoT', 'descripcion' => 'Plataforma para visualizar datos de sensores.'],
        ];

        // Usamos try-catch en caso de que la tabla use 'id' y no 'id_proyecto' como PK
        try {
            DB::table('proyectos')->insert($proyectosData);
        } catch (\Exception $e) {
            // Si falla, intentamos con el campo 'id' (si es que existe y no usamos id_proyecto)
            foreach ($proyectosData as $data) {
                // Asume que la tabla usa autoincremental, si usas 'id_proyecto' esto debe coincidir.
                // Si la migración usa 'id', descomenta la línea de abajo y ajusta
                // $data['id'] = null; // para evitar errores si la clave es 'id'
                DB::table('proyectos')->insert($data);
            }
        }

        // 2. Asociar estos proyectos al primer usuario aprendiz que encuentres
        $aprendiz = User::where('role', 'APRENDIZ')->first();
        $proyectos = Proyecto::all();

        if ($aprendiz && $proyectos->count() > 0) {
            // Sincroniza todos los proyectos existentes con el primer aprendiz
            // Esto asume que la relación 'proyectos' existe en el modelo User (como corregimos)
            // La tabla pivote usada aquí es 'proyecto_user'
            $aprendiz->proyectos()->sync($proyectos->pluck('id_proyecto'));

            $this->command->info("Se han creado 3 proyectos y se han asociado al usuario Aprendiz.");
        } else {
            $this->command->warn("No se encontró ningún usuario con el rol 'APRENDIZ' o no se pudieron crear proyectos.");
        }
    }
}
