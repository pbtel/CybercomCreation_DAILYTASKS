<?php
/**
 * Database Configuration - PostgreSQL
 * Phase 6 - Database Integration
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'easycart_db');
define('DB_USER', 'postgres');
define('DB_PASS', 'root');

// PDO DSN with UTF-8 charset
define('DB_DSN', 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';options=\'--client_encoding=UTF8\'');

// PDO Options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
