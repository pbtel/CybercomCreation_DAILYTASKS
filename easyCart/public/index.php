<?php

/**
 * EasyCart - Single Entry Point
 * All requests are routed through this file
 */

// Define base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($protocol . '://' . $host . dirname($scriptPath), '/');
define('BASE_URL', $baseUrl);

// Start session
session_start();

// Load core classes
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';
require_once '../app/core/Session.php';

// Initialize application
$app = new App();
