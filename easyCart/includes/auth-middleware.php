<?php
/**
 * Authentication Middleware
 * Restricts access to authenticated users only
 * Usage: require_once 'includes/auth-middleware.php';
 */

// Ensure session functions are loaded
require_once __DIR__ . '/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    // Store the current page URL for redirect after login
    $currentPage = $_SERVER['REQUEST_URI'];

    // Set flash message
    setFlashMessage('error', 'Please login to access this page.');

    // Redirect to login with return URL
    header('Location: login.php?redirect=' . urlencode($currentPage));
    exit;
}
