<?php
require __DIR__ . '/bootstrap/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    User::create([
        'name' => 'Administrador',
        'email' => 'admin@istam.edu.ec',
        'password' => Hash::make('123456'),
        'role' => 'administrador'
    ]);

    User::create([
        'name' => 'Juan Estudiante',
        'email' => 'juan@istam.edu.ec',
        'password' => Hash::make('123456'),
        'role' => 'estudiante'
    ]);

    User::create([
        'name' => 'Carlos Docente',
        'email' => 'carlos@istam.edu.ec',
        'password' => Hash::make('123456'),
        'role' => 'docente'
    ]);

    echo "✅ Usuarios creados exitosamente!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
