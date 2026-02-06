<?php
/**
 * AJAX Endpoint - Remove Coupon Code
 * Removes applied coupon from session
 */

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/products.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Remove coupon
    $removed = removeCoupon();
    
    if ($removed) {
        // Get cart subtotal (without coupon)
        $subtotal = getCartSubtotal();
        
        echo json_encode([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'subtotal' => $subtotal
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No coupon to remove'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove coupon'
    ]);
}
?>
