<?php

/**
 * EasyCart - Single Entry Point
 * All requests are routed through this file
 */

// Define base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /easyCart/public/index.php
$scriptPath = dirname($scriptName); // e.g., /easyCart/public
$baseUrl = $protocol . '://' . $host . str_replace('\\', '/', $scriptPath);
// Remove /public (case-insensitive) if it's at the end
$baseUrl = preg_replace('/\/public\/?$/i', '', $baseUrl);
// Ensure no trailing slash
$baseUrl = rtrim($baseUrl, '/');
define('BASE_URL', $baseUrl);

// Start session
session_start();

// Autoloader for models, core, and controllers
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialize application
$app = new App();
