<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_vinculacions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periodo_academico_id')->constrained('periodo_academicos')->onDelete('cascade');
            $table->unsignedInteger('horas_requeridas')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_vinculacions');
    }
};