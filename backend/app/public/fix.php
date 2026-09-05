<?php
// fix-permissions.php
$paths = [
    __DIR__ . '/../backend/storage',
    __DIR__ . '/../backend/storage/app',
    __DIR__ . '/../backend/storage/app/public',
    __DIR__ . '/../backend/storage/framework',
    __DIR__ . '/../backend/storage/framework/cache',
    __DIR__ . '/../backend/storage/framework/sessions',
    __DIR__ . '/../backend/storage/framework/views',
    __DIR__ . '/../backend/storage/logs',
    __DIR__ . '/../backend/bootstrap/cache',
];

echo "<h3>Fixing Permissions...</h3><ul>";

foreach ($paths as $path) {
    if (file_exists($path)) {
        chmod($path, 0775);
        echo "<li>Set 0775 on: " . basename($path) . "</li>";
    } else {
        echo "<li style='color:red;'>Path not found: " . basename($path) . "</li>";
    }
}

// Also clear the log file to see fresh logs
$logFile = __DIR__ . '/../backend/storage/logs/laravel.log';
if (file_exists($logFile)) {
    chmod($logFile, 0664);
    file_put_contents($logFile, ''); // Empty the log
    echo "<li>Cleared and set 0664 on: laravel.log</li>";
}

echo "</ul><h3>Done!</h3>";
