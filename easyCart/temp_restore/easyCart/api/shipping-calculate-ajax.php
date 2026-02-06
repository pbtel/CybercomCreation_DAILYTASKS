<?php
/**
 * AJAX Endpoint - Shipping Calculation
 * Calculates shipping cost, tax, and total based on shipping method
 */

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/shipping.php';
require_once '../includes/products.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'standard';
$subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : null;

// If subtotal not provided, get from cart
if ($subtotal === null) {
    $subtotal = getCartSubtotal();
}

// Validate shipping method
$validMethods = ['standard', 'express', 'whiteglove', 'freight'];
if (!in_array($shippingMethod, $validMethods)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid shipping method'
    ]);
    exit;
}

try {
    // Apply coupon discount if available
    $appliedCoupon = getAppliedCoupon();
    $couponDiscount = 0;
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($subtotal);
    }
    $subtotalAfterCoupon = $subtotal - $couponDiscount;
    
    // Calculate shipping cost on coupon-discounted subtotal
    $shippingCost = calculateShippingCost($subtotalAfterCoupon, $shippingMethod);
    
    // Calculate tax (18% GST on Subtotal + Shipping)
    $tax = calculateTax($subtotalAfterCoupon, $shippingCost);
    
    // Calculate total
    $total = calculateOrderTotal($subtotalAfterCoupon, $shippingCost, $tax);
    
    // Get shipping method description
    $description = getShippingMethodDescription($shippingMethod, $subtotalAfterCoupon);
    
    // Store selected shipping method in session for persistence
    $_SESSION['selected_shipping_method'] = $shippingMethod;
    
    echo json_encode([
        'success' => true,
        'subtotal' => $subtotal,
        'shipping_cost' => $shippingCost,
        'tax' => $tax,
        'total' => $total,
        'shipping_method' => $shippingMethod,
        'shipping_description' => $description
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to calculate shipping'
    ]);
}
?>
