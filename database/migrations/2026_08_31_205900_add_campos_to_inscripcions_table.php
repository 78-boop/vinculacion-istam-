<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            if (!Schema::hasColumn('inscripcions', 'estudiante_id')) {
                $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('inscripcions', 'proyecto_vinculacion_id')) {
                $table->foreignId('proyecto_vinculacion_id')->constrained('proyecto_vinculacions')->onDelete('cascade');
            }
            if (!Schema::hasColumn('inscripcions', 'fecha_inscripcion')) {
                $table->date('fecha_inscripcion');
            }
            if (!Schema::hasColumn('inscripcions', 'horas_cumplidas')) {
                $table->unsignedInteger('horas_cumplidas')->default(0);
            }
            if (!Schema::hasColumn('inscripcions', 'estado')) {
                $table->enum('estado', ['activo', 'completado', 'retirado'])->default('activo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropForeign(['estudiante_id']);
            $table->dropForeign(['proyecto_vinculacion_id']);
            $table->dropColumn(['estudiante_id', 'proyecto_vinculacion_id', 'fecha_inscripcion', 'horas_cumplidas', 'estado']);
        });
    }
};