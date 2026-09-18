<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            if (!Schema::hasColumn('carreras', 'horas_requeridas')) {
                $table->unsignedInteger('horas_requeridas')->default(90);
            }
        });
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            if (Schema::hasColumn('carreras', 'horas_requeridas')) {
                $table->dropColumn('horas_requeridas');
            }
        });
    }
};