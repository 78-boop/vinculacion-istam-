<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades_vinculacion', function (Blueprint $table) {
            if (!Schema::hasColumn('actividades_vinculacion', 'carrera_id')) {
                $table->foreignId('carrera_id')->after('id')->constrained('carreras')->onDelete('cascade');
            }
            if (!Schema::hasColumn('actividades_vinculacion', 'creado_por')) {
                $table->foreignId('creado_por')->after('carrera_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('actividades_vinculacion', 'titulo')) {
                $table->string('titulo')->after('creado_por');
            }
            if (!Schema::hasColumn('actividades_vinculacion', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('titulo');
            }
            if (!Schema::hasColumn('actividades_vinculacion', 'activo')) {
                $table->boolean('activo')->default(true)->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('actividades_vinculacion', function (Blueprint $table) {
            foreach (['activo', 'descripcion', 'titulo'] as $col) {
                if (Schema::hasColumn('actividades_vinculacion', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('actividades_vinculacion', 'creado_por')) {
                $table->dropForeign(['creado_por']);
                $table->dropColumn('creado_por');
            }
            if (Schema::hasColumn('actividades_vinculacion', 'carrera_id')) {
                $table->dropForeign(['carrera_id']);
                $table->dropColumn('carrera_id');
            }
        });
    }
};