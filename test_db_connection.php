<?php
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');

require __DIR__ . '/bootstrap/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.default' => 'sqlite']);

echo "=== Database Connection Test ===\n";
echo "Current Connection: " . config('database.default') . "\n";
echo "SQLite Database Path: " . config('database.connections.sqlite.database') . "\n";

try {
    // Test database connection
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful!\n";

    // Check if users table exists
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table';");
    echo "\n📋 Tables in database:\n";
    foreach ($tables as $table) {
        echo "  - " . $table->name . "\n";
    }

    // Check users table schema if it exists
    $hasUsersTable = false;
    foreach ($tables as $table) {
        if ($table->name === 'users') {
            $hasUsersTable = true;
            break;
        }
    }

    if ($hasUsersTable) {
        $columns = DB::select("PRAGMA table_info(users);");
        echo "\n👤 Users table columns:\n";
        foreach ($columns as $column) {
            echo "  - " . $column->name . " (" . $column->type . ")\n";
        }

        // Count users
        $userCount = DB::table('users')->count();
        echo "\n👥 Total users in database: " . $userCount . "\n";
    } else {
        echo "\n❌ Users table does not exist!\n";
        echo "You need to run migrations first.\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
