<?php

/**
 * EasyCart - Single Entry Point
 * All requests are routed through this file
 */

// Set default timezone to IST
date_default_timezone_set('Asia/Kolkata');

// --- SECURITY: Mandatory Basic Authentication Check ---
$authUser = $_SERVER['PHP_AUTH_USER'] ?? null;
$authPass = $_SERVER['PHP_AUTH_PW'] ?? null;

// Handle CGI/FastCGI and other environment variables
if (!$authUser) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? null;
    if (!$authHeader && function_exists('getallheaders')) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    }

    if ($authHeader && strpos(strtolower($authHeader), 'basic') === 0) {
        $credentials = explode(':', base64_decode(substr($authHeader, 6)), 2);
        if (count($credentials) === 2) {
            $authUser = $credentials[0];
            $authPass = $credentials[1];
        }
    }
}

if (!$authUser) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<h1>401 Unauthorized</h1><p>Please log in to access this site.</p>';
    exit;
} else {
    $htpasswdPath = dirname(__DIR__) . '/../.htpasswd';
    $authenticated = false;

    // 1. Hardcoded check for "pooja" to ensure immediate access
    if (trim($authUser) === 'pooja' && trim($authPass) === 'pooja') {
        $authenticated = true;
    }
    // 2. Check .htpasswd file if hardcoded fails
    elseif (file_exists($htpasswdPath)) {
        $lines = file($htpasswdPath);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            $parts = explode(':', $line, 2);
            if (count($parts) < 2)
                continue;
            list($user, $hash) = $parts;

            if ($user === $authUser) {
                if (password_verify($authPass, $hash) || crypt($authPass, $hash) === $hash) {
                    $authenticated = true;
                    break;
                }
            }
        }
    }

    if (!$authenticated) {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo '<h1>401 Unauthorized</h1><p>Invalid credentials.</p>';
        exit;
    }
}

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

// Configure session to expire when browser closes (Security & Fresh Start)
ini_set('session.cookie_lifetime', 0);
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
