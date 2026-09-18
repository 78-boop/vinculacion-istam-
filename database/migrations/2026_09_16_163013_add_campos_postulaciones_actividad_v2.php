<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postulaciones_actividad', function (Blueprint $table) {
            if (!Schema::hasColumn('postulaciones_actividad', 'actividad_vinculacion_id')) {
                $table->foreignId('actividad_vinculacion_id')->after('id')->constrained('actividades_vinculacion')->onDelete('cascade');
            }
            if (!Schema::hasColumn('postulaciones_actividad', 'inscripcion_id')) {
                $table->foreignId('inscripcion_id')->after('actividad_vinculacion_id')->constrained('inscripcions')->onDelete('cascade');
            }
            if (!Schema::hasColumn('postulaciones_actividad', 'estado')) {
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente')->after('inscripcion_id');
            }
            if (!Schema::hasColumn('postulaciones_actividad', 'observaciones_docente')) {
                $table->text('observaciones_docente')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('postulaciones_actividad', 'aprobado_por')) {
                $table->foreignId('aprobado_por')->nullable()->after('observaciones_docente')->constrained('users');
            }
            if (!Schema::hasColumn('postulaciones_actividad', 'fecha_aprobacion')) {
                $table->timestamp('fecha_aprobacion')->nullable()->after('aprobado_por');
            }
        });
    }

    public function down(): void
    {
        Schema::table('postulaciones_actividad', function (Blueprint $table) {
            if (Schema::hasColumn('postulaciones_actividad', 'fecha_aprobacion')) {
                $table->dropColumn('fecha_aprobacion');
            }
            if (Schema::hasColumn('postulaciones_actividad', 'aprobado_por')) {
                $table->dropForeign(['aprobado_por']);
                $table->dropColumn('aprobado_por');
            }
            foreach (['observaciones_docente', 'estado'] as $col) {
                if (Schema::hasColumn('postulaciones_actividad', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('postulaciones_actividad', 'inscripcion_id')) {
                $table->dropForeign(['inscripcion_id']);
                $table->dropColumn('inscripcion_id');
            }
            if (Schema::hasColumn('postulaciones_actividad', 'actividad_vinculacion_id')) {
                $table->dropForeign(['actividad_vinculacion_id']);
                $table->dropColumn('actividad_vinculacion_id');
            }
        });
    }
};