<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Actualizar tabla inscripcions (con chequeo: puede que ya existan)
        Schema::table('inscripcions', function (Blueprint $table) {
            if (!Schema::hasColumn('inscripcions', 'horas_requeridas')) {
                $table->integer('horas_requeridas')->default(90)->after('estado');
            }
            if (!Schema::hasColumn('inscripcions', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable();
            }
            if (!Schema::hasColumn('inscripcions', 'fecha_finalizacion_estimada')) {
                $table->date('fecha_finalizacion_estimada')->nullable();
            }
            if (!Schema::hasColumn('inscripcions', 'certificado_aprobado')) {
                $table->boolean('certificado_aprobado')->default(false);
            }
        });

        // 2. Crear tabla registro_horas (con chequeo: la migración _fix ya la crea)
        if (!Schema::hasTable('registro_horas')) {
            Schema::create('registro_horas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('inscripcions')->onDelete('cascade');
                $table->date('fecha');
                $table->decimal('horas_registradas', 3, 1); // máximo 8 horas
                $table->text('descripcion')->nullable();
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'perdida'])->default('pendiente');
                $table->text('observaciones')->nullable();
                $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->unique(['inscripcion_id', 'fecha']);
                $table->index(['inscripcion_id', 'fecha']);
                $table->index('estado');
            });
        }

        // 3. Crear tabla actividades_estudiante (con chequeo)
        if (!Schema::hasTable('actividades_estudiante')) {
            Schema::create('actividades_estudiante', function (Blueprint $table) {
                $table->id();
                $table->foreignId('registro_hora_id')->constrained('registro_horas')->onDelete('cascade');
                $table->foreignId('inscripcion_id')->constrained('inscripcions')->onDelete('cascade');
                $table->date('fecha');
                $table->text('actividad_realizada');
                $table->longText('resultado')->nullable();
                $table->enum('estado', ['registrada', 'verificada', 'completa'])->default('registrada');
                $table->timestamps();
                $table->index('fecha');
            });
        }

        // 4. Tabla evidencias: ya existe desde antes (con otro esquema, luego migrado
        // por 2026_09_01_170000_fix_evidencias_table_columns). No se crea de nuevo.

        // 5. Crear tabla certificados (con chequeo)
        if (!Schema::hasTable('certificados')) {
            Schema::create('certificados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('inscripcions')->onDelete('cascade');
                $table->date('fecha_generacion');
                $table->string('numero_certificado')->unique();
                $table->decimal('horas_certificadas', 5, 1);
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
                $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
                $table->text('observaciones_rechazo')->nullable();
                $table->date('fecha_aprobacion')->nullable();
                $table->string('ruta_pdf')->nullable();
                $table->timestamps();
                $table->index('estado');
            });
        }

        // 6. Crear tabla observaciones_docente (con chequeo)
        if (!Schema::hasTable('observaciones_docente')) {
            Schema::create('observaciones_docente', function (Blueprint $table) {
                $table->id();
                $table->foreignId('registro_hora_id')->constrained('registro_horas')->onDelete('cascade');
                $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
                $table->text('observacion');
                $table->enum('aprobacion', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     * (down() no se toca a propósito: si algún día hacen rollback, no queremos
     * que borre tablas/columnas que otras migraciones también reclaman como propias)
     */
    public function down(): void
    {
        //
    }
};