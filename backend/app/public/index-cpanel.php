<?php

/**
 * TORY CROWN — Smart cPanel Entry Point
 * ======================================
 * This single file handles BOTH the React frontend AND the Laravel API/admin.
 * It does NOT rely on .htaccess DirectoryIndex or mod_rewrite.
 *
 * HOW IT WORKS:
 *   - /api/*        → Boots Laravel (REST API)
 *   - /admin/*      → Boots Laravel (Filament admin panel)
 *   - /sanctum/*    → Boots Laravel (auth)
 *   - /livewire/*   → Boots Laravel (Livewire updates)
 *   - /barcode/*    → Boots Laravel
 *   - Everything else → Serves React SPA (index.html)
 *
 * DIRECTORY STRUCTURE ON SERVER:
 *   /home/username/
 *     backend/          ← All Laravel files here
 *     public_html/
 *       index.php       ← THIS FILE
 *       .htaccess       ← htaccess-cpanel.htaccess (renamed)
 *       index.html      ← React build entry
 *       assets/         ← React JS/CSS assets
 */

// cPanel mod_rewrite sometimes changes REQUEST_URI to the rewritten script.
// REDIRECT_URL contains the original path we actually want to check.
$originalUri = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($originalUri, PHP_URL_PATH);
$requestPath = '/' . ltrim($requestPath, '/');

// Routes that belong to Laravel backend
$laravelPrefixes = [
    '/api/',
    '/admin',
    '/sanctum',
    '/livewire',
    '/livewire-',   // versioned livewire endpoints e.g. /livewire-b9bff195/
    '/filament',    // Filament CSS/JS assets
    '/vendor',      // Laravel vendor assets (Livewire, etc.)
    '/build',       // Vite-built Laravel assets (if any)
    '/barcode',
    '/_ignition',
    '/telescope',
    '/horizon',
];

$isLaravelRoute = false;
foreach ($laravelPrefixes as $prefix) {
    if (str_starts_with($requestPath, $prefix)) {
        $isLaravelRoute = true;
        break;
    }
}


if ($isLaravelRoute) {
    // ── BOOT LARAVEL ────────────────────────────────────────────────────────
    define('LARAVEL_START', microtime(true));

    require __DIR__ . '/../backend/vendor/autoload.php';

    $app = require_once __DIR__ . '/../backend/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();

    $kernel->terminate($request, $response);

} else {
    // ── SERVE REACT SPA ─────────────────────────────────────────────────────
    $reactIndex = __DIR__ . '/index.html';

    if (file_exists($reactIndex)) {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        readfile($reactIndex);
    } else {
        http_response_code(503);
        echo '<h1>Frontend not deployed yet.</h1><p>Please upload the React build to public_html/.</p>';
    }
}
