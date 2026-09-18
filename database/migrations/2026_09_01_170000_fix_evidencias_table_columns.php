<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La tabla `evidencias` se creó originalmente para colgar de `actividades`
     * (columnas actividad_id, archivo_path, nombre_original). Después el
     * sistema pasó a subir evidencias por `registro_horas`, pero como esa
     * migración usaba `if (!Schema::hasTable('evidencias'))`, nunca se
     * aplicó porque la tabla ya existía. Resultado: al código (Evidencia
     * model, RegistroHorasController) le faltan columnas reales en la tabla.
     * Ningún controlador ni vista usa ya actividad_id/archivo_path/nombre_original.
     */
    public function up(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            if (Schema::hasColumn('evidencias', 'actividad_id')) {
                $table->dropForeign(['actividad_id']);
                $table->dropColumn('actividad_id');
            }
            if (Schema::hasColumn('evidencias', 'archivo_path')) {
                $table->dropColumn('archivo_path');
            }
            if (Schema::hasColumn('evidencias', 'nombre_original')) {
                $table->dropColumn('nombre_original');
            }
        });

        Schema::table('evidencias', function (Blueprint $table) {
            if (!Schema::hasColumn('evidencias', 'registro_hora_id')) {
                $table->foreignId('registro_hora_id')->nullable()
                    ->constrained('registro_horas')->onDelete('cascade');
            }
            if (!Schema::hasColumn('evidencias', 'actividad_estudiante_id')) {
                $table->foreignId('actividad_estudiante_id')->nullable()
                    ->constrained('actividades_estudiante')->onDelete('cascade');
            }
            if (!Schema::hasColumn('evidencias', 'nombre_archivo')) {
                $table->string('nombre_archivo')->nullable();
            }
            if (!Schema::hasColumn('evidencias', 'ruta_archivo')) {
                $table->string('ruta_archivo')->nullable();
            }
            if (!Schema::hasColumn('evidencias', 'tamaño_bytes')) {
                $table->bigInteger('tamaño_bytes')->nullable();
            }
            if (!Schema::hasColumn('evidencias', 'verificada')) {
                $table->boolean('verificada')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            foreach (['registro_hora_id', 'actividad_estudiante_id'] as $fk) {
                if (Schema::hasColumn('evidencias', $fk)) {
                    $table->dropForeign([$fk]);
                }
            }
            foreach (['registro_hora_id', 'actividad_estudiante_id', 'nombre_archivo', 'ruta_archivo', 'tamaño_bytes', 'verificada'] as $col) {
                if (Schema::hasColumn('evidencias', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
