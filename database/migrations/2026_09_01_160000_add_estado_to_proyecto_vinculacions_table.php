<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyecto_vinculacions', function (Blueprint $table) {
            if (!Schema::hasColumn('proyecto_vinculacions', 'estado')) {
                // Los proyectos existentes (creados directo por el admin) quedan
                // como 'aprobado' automáticamente. Las propuestas nuevas de un
                // docente se crean en 'pendiente' hasta que el admin las revise.
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('aprobado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proyecto_vinculacions', function (Blueprint $table) {
            if (Schema::hasColumn('proyecto_vinculacions', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
