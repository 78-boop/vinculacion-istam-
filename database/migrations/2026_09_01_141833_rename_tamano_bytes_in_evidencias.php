<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NEUTRALIZADA A PROPÓSITO: el resto del código (Evidencia.php,
        // RegistroHorasController.php) usa 'tamaño_bytes' (con ñ). Si esta
        // migración renombraba la columna, rompía la subida de evidencias.
        // La migración 2026_09_01_170000_fix_evidencias_table_columns ya
        // dejó 'tamaño_bytes' como el nombre definitivo de la columna.
    }

    public function down(): void
    {
        //
    }
};