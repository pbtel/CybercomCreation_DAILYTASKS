<?php
// Mock session
session_start();
$_SESSION['cart_id'] = 1; // Assuming cart 1 exists or doesn't matter for logic check
$_SESSION['user'] = ['logged_in' => false];

// Define constants expected by config.php if not already
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '5432');
    define('DB_NAME', 'easycart_db');
    define('DB_USER', 'postgres');
    define('DB_PASS', 'root');
    define('DB_DSN', 'pgsql:host=localhost;port=5432;dbname=easycart_db');
    define('DB_OPTIONS', []);
}

// Check includes
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/coupon-helpers.php';

echo "Includes loaded successfully.\n";

// Mock DB functions to avoid actual DB connection failing
// We can't easily mock global functions unless we use runkit or namespaces, which we don't carry.
// So we just try to call validateCouponCode first.

$valid = validateCouponCode('SAVE10');
var_dump($valid);

if ($valid) {
    echo "Coupon valid.\n";
} else {
    echo "Coupon invalid.\n";
}

// We cannot call applyCoupon() because it will try to connect to DB and fail (I assume).
// But we can check if functions exist.

if (function_exists('updateCartCouponDB')) {
    echo "updateCartCouponDB exists.\n";
} else {
    echo "updateCartCouponDB MISSING.\n";
}

if (function_exists('updateOrderCartCouponDB')) {
    echo "updateOrderCartCouponDB exists.\n";
} else {
    echo "updateOrderCartCouponDB MISSING.\n";
}

echo "Done.\n";
