<?php
/**
 * Root Entry Point / Router
 * This file routes requests to the public/ directory or handles them as an MVC entry point.
 */

// If we're using the PHP built-in server, handle static files in public/
if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri) && !is_dir(__DIR__ . '/public' . $uri)) {
        return false;
    }
}

// Redirect root index to public/index.php for MVC processing
// We want to keep the clean URL if possible, or just let public/index.php handle it.
// To ensure BASE_URL and other paths work correctly, we'll route everything to public/index.php.
require_once __DIR__ . '/public/index.php';