<?php
/**
 * AJAX Endpoint - Apply Coupon Code
 * Validates and applies coupon code to session
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

// Get POST data
$couponCode = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';

// Validate coupon code
if (empty($couponCode)) {
    sendJson([
        'success' => false,
        'message' => 'Please enter a coupon code'
    ]);
}

try {
    // Apply coupon
    $result = applyCoupon($couponCode);

    if ($result['success']) {
        // Get cart subtotal
        $subtotal = getCartSubtotal();

        // Calculate coupon discount
        $discountAmount = calculateCouponDiscount($subtotal);
        $newSubtotal = $subtotal - $discountAmount;

        sendJson([
            'success' => true,
            'message' => $result['message'],
            'coupon_code' => $result['coupon']['code'],
            'discount_percent' => $result['coupon']['discount_percent'],
            'discount_amount' => $discountAmount,
            'original_subtotal' => $subtotal,
            'new_subtotal' => $newSubtotal
        ]);
    } else {
        sendJson([
            'success' => false,
            'message' => $result['message']
        ]);
    }
} catch (Exception $e) {
    sendJson([
        'success' => false,
        'message' => 'Failed to apply coupon. Please try again.'
    ]);
}

