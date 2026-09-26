<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            if (! Schema::hasColumn('actividades', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable();
            }
            if (! Schema::hasColumn('actividades', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }
};