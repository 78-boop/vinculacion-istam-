<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Permiso que da el administrador para que un estudiante se inscriba en otra actividad
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'permitir_nueva_actividad')) {
                $table->boolean('permitir_nueva_actividad')->default(false)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'permitir_nueva_actividad')) {
                $table->dropColumn('permitir_nueva_actividad');
            }
        });
    }
};
