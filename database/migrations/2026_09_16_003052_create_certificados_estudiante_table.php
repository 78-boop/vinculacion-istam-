<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificados_estudiante')) {
            Schema::create('certificados_estudiante', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('inscripcions')->onDelete('cascade');
                $table->foreignId('tipo_certificado_id')->constrained('tipos_certificado');
                $table->string('ruta_archivo');
                $table->string('nombre_archivo_original')->nullable();
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
                $table->text('observaciones_docente')->nullable();
                $table->foreignId('aprobado_por')->nullable()->constrained('users');
                $table->timestamp('fecha_aprobacion')->nullable();
                $table->timestamps();

                // Un estudiante sube UN archivo por tipo de certificado por inscripción.
                // Si lo rechazan, se actualiza esta misma fila (no se duplica).
                $table->unique(['inscripcion_id', 'tipo_certificado_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados_estudiante');
    }
};