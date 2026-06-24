<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'file_records', 'file_transfers', 'file_movements',
    'transfer_requests', 'users', 'departments', 'designations',
    'notifications', 'audit_logs'
];

foreach ($tables as $table) {
    try {
        $cols = DB::select("SHOW COLUMNS FROM `{$table}`");
        $names = array_column($cols, 'Field');
        echo "\n[{$table}]\n  " . implode(', ', $names) . "\n";
    } catch (Throwable $e) {
        echo "\n[{$table}] MISSING: " . $e->getMessage() . "\n";
    }
}
