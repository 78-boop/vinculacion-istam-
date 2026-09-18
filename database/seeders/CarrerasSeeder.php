<?php

namespace Database\Seeders;

use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarrerasSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'Contabilidad', 'imagen' => 'images/carreras/contabilidad.jpg', 'horas_requeridas' => 90],
            ['nombre' => 'Desarrollo de Software', 'imagen' => 'images/carreras/desarrollo-software.jpg', 'horas_requeridas' => 40],
            ['nombre' => 'Producción Agrícola', 'imagen' => 'images/carreras/produccion-agricola.jpg', 'horas_requeridas' => 90],
            ['nombre' => 'Producción Pecuaria', 'imagen' => 'images/carreras/produccion-pecuaria.jpg', 'horas_requeridas' => 30],
        ];

        foreach ($carreras as $carrera) {
            Carrera::updateOrCreate(
                ['nombre' => $carrera['nombre']],
                ['imagen' => $carrera['imagen'], 'activo' => true, 'horas_requeridas' => $carrera['horas_requeridas']]
            );
        }
    }
}