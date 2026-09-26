<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// La migración 2026_09_16_003123 debía crear esta tabla, pero por error contenía
// el código de la columna "cedula". Esta migración crea la tabla que faltaba.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('certificados_administrativos')) {
            return;
        }

        Schema::create('certificados_administrativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->unique()->constrained('inscripcions')->cascadeOnDelete();
            $table->string('numero_certificado', 60);
            $table->date('fecha_generacion');
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ruta_pdf');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados_administrativos');
    }
};
