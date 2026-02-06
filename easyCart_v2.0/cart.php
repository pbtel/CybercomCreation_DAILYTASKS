<?php
// Cart Controller
// This file handles the shopping cart page logic and includes the view

$pageTitle = "Shopping Cart";
require_once 'includes/auth-middleware.php'; // Require login to access cart

$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();

// Calculate subtotal after applying coupon discount
$appliedCoupon = getAppliedCoupon();
$couponDiscount = 0;
if ($appliedCoupon) {
    $couponDiscount = calculateCouponDiscount($subtotal);
}
$subtotalAfterCoupon = $subtotal - $couponDiscount;

// Get available shipping methods - use subtotal AFTER coupon
$availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotalAfterCoupon);

// Shipping will be calculated at checkout based on selected method
$shippingNote = 'Calculated at checkout';
// Tax note - will be calculated on (Subtotal + Shipping) at checkout
$taxNote = 'Calculated at checkout';

// Include the view
require_once 'views/cart.view.php';