<?php
$logPath = __DIR__ . '/../backend/storage/logs/laravel.log';
echo "<pre>";
echo "=== LATEST LARAVEL LOG ===\n";
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -50);
    foreach ($lastLines as $line) {
        echo htmlspecialchars($line);
    }
} else {
    echo "No log file found.";
}
echo "</pre>";
