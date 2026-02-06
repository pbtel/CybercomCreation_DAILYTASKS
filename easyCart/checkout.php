<?php
// Checkout Controller
// This file handles the checkout page logic and includes the view

$pageTitle = "Checkout";
require_once 'includes/auth-middleware.php'; // Require login to access checkout
require_once 'includes/shipping.php';
require_once 'includes/coupon-helpers.php';
require_once 'includes/shipping-type-helpers.php';

$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();

// Apply coupon discount if available
$appliedCoupon = getAppliedCoupon();
$couponDiscount = 0;
if ($appliedCoupon) {
    $couponDiscount = calculateCouponDiscount($subtotal);
}
$subtotalAfterCoupon = $subtotal - $couponDiscount;

// Get available shipping methods and auto-select default if needed
$availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotalAfterCoupon);
$selectedShippingMethod = getOrSetDefaultShippingMethod();

// Validate selected method is available, if not reset to default
if (!in_array($selectedShippingMethod, $availableShippingMethods)) {
    $selectedShippingMethod = getDefaultShippingMethod($cartItems, $subtotalAfterCoupon);
    setSelectedShippingMethod($selectedShippingMethod);
}

$shipping = calculateShippingCost($subtotalAfterCoupon, $selectedShippingMethod);
$tax = calculateTax($subtotalAfterCoupon, $shipping);
$total = calculateOrderTotal($subtotalAfterCoupon, $shipping, $tax);

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Include the view
require_once 'views/checkout.view.php';