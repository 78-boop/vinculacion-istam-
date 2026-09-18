<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            if (!Schema::hasColumn('carreras', 'imagen')) {
                $table->string('imagen')->nullable();
            }
            if (!Schema::hasColumn('carreras', 'activo')) {
                $table->boolean('activo')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            if (Schema::hasColumn('carreras', 'activo')) {
                $table->dropColumn('activo');
            }
            if (Schema::hasColumn('carreras', 'imagen')) {
                $table->dropColumn('imagen');
            }
        });
    }
};