<?php
/**
 * AJAX Endpoint - Remove Coupon Code
 * Removes applied coupon from session
 */

// Start output buffering to catch any stray output
ob_start();

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/products.php';

// Helper to send clean JSON response
function sendJson($data)
{
    if (ob_get_length())
        ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Invalid request method']);
}

try {
    // Remove coupon
    $removed = removeCoupon();

    if ($removed) {
        // Get cart subtotal (without coupon)
        $subtotal = getCartSubtotal();

        sendJson([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'subtotal' => $subtotal
        ]);
    } else {
        sendJson([
            'success' => false,
            'message' => 'No coupon to remove'
        ]);
    }
} catch (Exception $e) {
    sendJson([
        'success' => false,
        'message' => 'Failed to remove coupon'
    ]);
}

