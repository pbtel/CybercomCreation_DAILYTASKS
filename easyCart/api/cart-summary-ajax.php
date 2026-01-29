<?php
/**
 * AJAX Endpoint - Cart Summary
 * Fetches current cart summary data
 */

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/products.php';

// Only accept GET or POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get cart data
    $cartItems = getCartItemsWithDetails();
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    
    // Format cart items for response
    $formattedItems = [];
    foreach ($cartItems as $key => $item) {
        $formattedItems[] = [
            'cart_key' => $key,
            'product_id' => $item['product']['id'],
            'product_name' => $item['product']['name'],
            'product_price' => $item['product']['price'],
            'product_image' => $item['product']['image'],
            'quantity' => $item['quantity'],
            'variant' => $item['variant'],
            'subtotal' => $item['subtotal']
        ];
    }
    
    // Get coupon discount if applied
    $appliedCoupon = getAppliedCoupon();
    $couponDiscount = 0;
    $subtotalAfterCoupon = $cartSubtotal;
    
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($cartSubtotal);
        $subtotalAfterCoupon = $cartSubtotal - $couponDiscount;
    }
    
    echo json_encode([
        'success' => true,
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'coupon_discount' => $couponDiscount,
        'subtotal_after_coupon' => $subtotalAfterCoupon,
        'coupon_code' => $appliedCoupon ? $appliedCoupon['code'] : null,
        'coupon_percent' => $appliedCoupon ? $appliedCoupon['discount_percent'] : 0,
        'items' => $formattedItems,
        'is_empty' => empty($cartItems)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch cart summary'
    ]);
}
?>
