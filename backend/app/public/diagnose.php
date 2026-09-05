<?php
// TORY CROWN — Login Diagnostics v2
// Upload to public_html/diagnose.php, visit, then DELETE immediately.
header('Content-Type: text/plain');

$backendPath = __DIR__ . '/../backend';
require $backendPath . '/vendor/autoload.php';
$app = require_once $backendPath . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SESSION CONFIG ===\n";
echo "SESSION_DRIVER:        " . config('session.driver') . "\n";
echo "SESSION_DOMAIN:        " . (config('session.domain') ?: 'NOT SET') . "\n";
echo "SESSION_SECURE:        " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "APP_URL:               " . config('app.url') . "\n";
echo "APP_KEY set:           " . (config('app.key') ? 'YES' : 'NO') . "\n";

echo "\n=== ADMIN USERS IN DATABASE ===\n";
try {
    $users = DB::table('users')->get(['id', 'name', 'email', 'created_at']);
    if ($users->isEmpty()) {
        echo "❌ NO USERS FOUND — need to create admin account!\n";
    } else {
        foreach ($users as $u) {
            echo "✅ ID:{$u->id} | {$u->name} | {$u->email}\n";
        }
    }
} catch (Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SESSION STORAGE ===\n";
$sessionPath = $backendPath . '/storage/framework/sessions';
echo "Session path exists:   " . (is_dir($sessionPath) ? 'YES' : 'NO') . "\n";
echo "Session path writable: " . (is_writable($sessionPath) ? 'YES' : '❌ NOT WRITABLE') . "\n";
$sessionFiles = glob($sessionPath . '/*');
echo "Session files stored:  " . count($sessionFiles) . "\n";

echo "\n=== LATEST ERRORS ===\n";
$log = $backendPath . '/storage/logs/laravel.log';
if (file_exists($log)) {
    $lines = array_slice(file($log), -40);
    echo implode('', $lines);
} else {
    echo "No log file found.\n";
}

echo "\n⚠️  DELETE THIS FILE IMMEDIATELY AFTER READING!\n";
