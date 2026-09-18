<?php

namespace Database\Seeders;

use App\Models\TipoCertificado;
use Illuminate\Database\Seeder;

class TiposCertificadoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['codigo' => 'FPVS01', 'nombre' => 'Oficio solicitando participar del Proyecto de Vinculación', 'orden' => 1],
            ['codigo' => 'FPVS02', 'nombre' => 'Solicitud de Requerimiento de Prácticas de Vinculación', 'orden' => 2],
            ['codigo' => 'FPVS04', 'nombre' => 'Informe Semanal de Actividades', 'orden' => 3],
            ['codigo' => 'FPVS05', 'nombre' => 'Informe Consolidado Mensual de Logros Alcanzados', 'orden' => 4],
            ['codigo' => 'FPVS06', 'nombre' => 'Evaluación del Desempeño por parte del Tutor Académico', 'orden' => 5],
            ['codigo' => 'FPVS07', 'nombre' => 'Solicitud de Revisión del Informe de Vinculación', 'orden' => 6],
            ['codigo' => 'FPVS08', 'nombre' => 'Solicitud de Aprobación de las Horas de Vinculación', 'orden' => 7],
            ['codigo' => 'FPVS10', 'nombre' => 'Certificación Tutor Empresarial', 'orden' => 8],
        ];

        foreach ($tipos as $tipo) {
            TipoCertificado::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                ['nombre' => $tipo['nombre'], 'orden' => $tipo['orden'], 'activo' => true]
            );
        }
    }
}