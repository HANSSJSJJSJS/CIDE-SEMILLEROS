<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemillerosSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'nombre' => 'Bioprocesos y Biotecnología Aplicada (BIBA)',
                'linea_investigacion' => 'Ciencias Aplicadas en Desarrollo Ambiental',
            ],
            [
                'nombre' => 'Administración y Salud, Deportes y Bienestar',
                'linea_investigacion' => 'Administración en Salud, Deportes y Bienestar',
            ],
            [
                'nombre' => 'Agroindustria Seguridad Alimentaria',
                'linea_investigacion' => 'Seguridad Alimentaria',
            ],
            [
                'nombre' => 'Grupo de Estudio de Desarrollo de Software (GEDS)',
                'linea_investigacion' => 'Telecomunicaciones y Tecnologías Virtuales',
            ],
            [
                'nombre' => 'Investigación de Mercados para las Mipymes (INVERPYMES)',
                'linea_investigacion' => 'Comercio y Servicios para el Desarrollo Empresarial',
            ],
            [
                'nombre' => 'Materiales, Procesos de Manufactura y Automatización (MAPRA)',
                'linea_investigacion' => 'Diseño, Ingeniería y Mecatrónica',
            ],
            [
                'nombre' => 'Micronanotec',
                'linea_investigacion' => 'Integración de tecnologías convergentes para el mejoramiento de la calidad de vida',
            ],
            [
                'nombre' => 'Desarrollo de Videojuegos Serios',
                'linea_investigacion' => 'Telecomunicaciones y Tecnologías Virtuales',
            ],
            [
                'nombre' => 'PICIDE (Pedagogía)',
                'linea_investigacion' => 'Ciencias Sociales y Ciencias de la Educación',
            ],
        ];

        foreach ($rows as $r) {
            DB::table('semilleros')->updateOrInsert(
                ['nombre' => $r['nombre']],
                ['linea_investigacion' => $r['linea_investigacion']]
            );
        }
   
       }
       
}


