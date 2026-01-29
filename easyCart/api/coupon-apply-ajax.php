<?php
/**
 * AJAX Endpoint - Apply Coupon Code
 * Validates and applies coupon code to session
 */

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/products.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$couponCode = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';

// Validate coupon code
if (empty($couponCode)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a coupon code'
    ]);
    exit;
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
        
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'coupon_code' => $result['coupon']['code'],
            'discount_percent' => $result['coupon']['discount_percent'],
            'discount_amount' => $discountAmount,
            'original_subtotal' => $subtotal,
            'new_subtotal' => $newSubtotal
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to apply coupon. Please try again.'
    ]);
}
?>
