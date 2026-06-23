<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BACKUP MODULE DIAGNOSTIC ===\n\n";

// 1. Check routes registered
$routes = app('router')->getRoutes();
$backupRoutes = [];
foreach ($routes as $route) {
    if (str_contains($route->getName() ?? '', 'backup')) {
        $backupRoutes[] = $route->getName() . ' → ' . $route->uri();
    }
}
echo "Routes:\n";
foreach ($backupRoutes as $r) echo "  ✓ $r\n";
echo "\n";

// 2. Check storage disk
try {
    $disk = Illuminate\Support\Facades\Storage::disk('local');
    echo "✓ Local disk accessible\n";
    $exists = $disk->exists('backups');
    echo ($exists ? "✓" : "!") . " backups/ directory " . ($exists ? "exists" : "does NOT exist — will create") . "\n";
    if (!$exists) {
        $disk->makeDirectory('backups');
        echo "✓ backups/ directory created\n";
    }
} catch (Throwable $e) {
    echo "✗ Disk error: " . $e->getMessage() . "\n";
}

// 3. Try instantiating controller WITHOUT middleware (middleware needs HTTP context)
try {
    // Simulate auth so middleware check passes
    $user = App\Models\User::where('role', 'super_admin')->first();
    if ($user) {
        Illuminate\Support\Facades\Auth::setUser($user);
        echo "✓ Super admin found: {$user->name}\n";
    } else {
        echo "! No super admin user found\n";
    }

    // Call getBackupList directly via reflection
    $ctrl = new App\Http\Controllers\Admin\BackupController();
    $method = new ReflectionMethod($ctrl, 'getBackupList');
    $method->setAccessible(true);
    $list = $method->invoke($ctrl);
    echo "✓ getBackupList() returned " . count($list) . " items\n";
} catch (Throwable $e) {
    echo "✗ Controller error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 4. Check view exists
$viewPath = resource_path('views/admin/backup/index.blade.php');
echo (file_exists($viewPath) ? "✓" : "✗") . " View: admin/backup/index.blade.php\n";

// 5. Check NoCacheMiddleware
$nc = file_get_contents('app/Http/Middleware/NoCacheMiddleware.php');
echo (str_contains($nc, '->withHeaders(') ? "✗ withHeaders() still in code!" : "✓ NoCacheMiddleware fixed") . "\n";

// 6. Check AuditLog model
try {
    $cols = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM audit_logs");
    echo "✓ audit_logs table exists (" . count($cols) . " columns)\n";
} catch (Throwable $e) {
    echo "✗ audit_logs: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
