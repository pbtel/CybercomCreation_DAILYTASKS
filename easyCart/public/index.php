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

// Load core classes
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';

// Initialize application
$app = new App();
