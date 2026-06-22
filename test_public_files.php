<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $files = App\Models\PublicFile::latest()->paginate(20);
    foreach ($files as $f) {
        $uuid   = $f->uuid;
        $exists = $f->attachment_exists;
        $url    = $f->getSignedDownloadUrl();
        echo "OK: uuid={$uuid}, exists={$exists}, url=" . substr($url, 0, 60) . "\n";
    }
    echo "TOTAL: " . $files->total() . " records\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
