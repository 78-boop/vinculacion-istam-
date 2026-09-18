<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PeriodoAcademico;
use App\Models\ProyectoVinculacion;
use App\Models\Inscripcion;
use App\Models\RegistroHora;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== CREAR USUARIOS =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $estudiante = User::firstOrCreate(
            ['email' => 'estudiante@test.com'],
            [
                'name' => 'Estudiante Test',
                'password' => Hash::make('password'),
                'role' => 'estudiante',
                'email_verified_at' => now(),
            ]
        );

        $docente = User::firstOrCreate(
            ['email' => 'docente@test.com'],
            [
                'name' => 'Docente',
                'password' => Hash::make('password'),
                'role' => 'docente',
                'email_verified_at' => now(),
            ]
        );

        // ===== CREAR PERÍODO ACADÉMICO =====
        $periodo = PeriodoAcademico::firstOrCreate(
            ['nombre' => '2026-2'],
            [
                'fecha_inicio' => Carbon::createFromDate(2026, 8, 1),
                'fecha_fin' => Carbon::createFromDate(2026, 12, 31),
            ]
        );

        // ===== CREAR PROYECTO DE VINCULACIÓN =====
        $proyecto = ProyectoVinculacion::firstOrCreate(
            ['nombre' => 'Proyecto Test'],
            [
                'descripcion' => 'Proyecto de prueba',
                'docente_id' => $docente->id,
                'periodo_academico_id' => $periodo->id,
            ]
        );

        // ===== CREAR INSCRIPCIÓN =====
        $inscripcion = Inscripcion::firstOrCreate(
            ['estudiante_id' => $estudiante->id, 'proyecto_vinculacion_id' => $proyecto->id],
            [
                'fecha_inscripcion' => Carbon::now(),
                'horas_cumplidas' => 32,
                'estado' => 'activo',
                'horas_requeridas' => 90,
            ]
        );

        // ===== CREAR REGISTROS DE HORAS =====
        for ($i = 0; $i < 4; $i++) {
            RegistroHora::firstOrCreate(
                [
                    'inscripcion_id' => $inscripcion->id,
                    'fecha' => Carbon::createFromDate(2026, 8, 28)->addDays($i)->startOfDay(),
                ],
                [
                    'horas_registradas' => 8.0,
                    'descripcion' => 'Subs plataformas digitales de la empresa TeddSas para optimizar procesos internos',
                    'estado' => 'pendiente',
                ]
            );
        }

        $this->call(TiposCertificadoSeeder::class);
        $this->call(CarrerasSeeder::class);

        $this->command->info('✅ Seeding completado exitosamente!');
    }
}