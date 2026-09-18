<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla registro_horas
        if (!Schema::hasTable('registro_horas')) {
            Schema::create('registro_horas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('inscripcions')->onDelete('cascade');
                $table->date('fecha');
                $table->decimal('horas_registradas', 3, 1);
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

        // 2. Crear tabla actividades_estudiante
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

        // 3. Actualizar tabla evidencias si es necesario
        if (!Schema::hasTable('evidencias')) {
            Schema::create('evidencias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actividad_estudiante_id')->nullable()->constrained('actividades_estudiante')->onDelete('cascade');
                $table->foreignId('registro_hora_id')->nullable()->constrained('registro_horas')->onDelete('cascade');
                $table->enum('tipo_archivo', ['imagen', 'video', 'audio', 'pdf', 'documento']);
                $table->string('nombre_archivo');
                $table->string('ruta_archivo');
                $table->bigInteger('tamaño_bytes')->nullable();
                $table->text('descripcion')->nullable();
                $table->boolean('verificada')->default(false);
                $table->timestamps();
            });
        }

        // 4. Crear tabla certificados
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

        // 5. Crear tabla observaciones_docente
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

    public function down(): void
    {
        Schema::dropIfExists('observaciones_docente');
        Schema::dropIfExists('certificados');
        Schema::dropIfExists('actividades_estudiante');
        Schema::dropIfExists('registro_horas');
    }
};