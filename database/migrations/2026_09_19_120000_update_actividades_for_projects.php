<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->foreignId('proyecto_vinculacion_id')->nullable()->after('id')->constrained('proyecto_vinculacions')->nullOnDelete();
            $table->foreignId('docente_id')->nullable()->after('proyecto_vinculacion_id')->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio')->nullable()->after('fecha');
            $table->date('fecha_finalizacion')->nullable()->after('fecha_inicio');
        });

        Schema::table('actividades', function (Blueprint $table) {
            $table->foreignId('inscripcion_id')->nullable()->change();
            $table->date('fecha')->nullable()->change();
        });

        Schema::create('actividad_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('inscripcion_id')->constrained('inscripcions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['actividad_id', 'inscripcion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_inscripcion');

        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['proyecto_vinculacion_id']);
            $table->dropForeign(['docente_id']);
            $table->dropColumn(['proyecto_vinculacion_id', 'docente_id', 'fecha_inicio', 'fecha_finalizacion']);
        });
    }
};
