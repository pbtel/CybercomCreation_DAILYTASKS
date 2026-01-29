<?php
/**
 * Coupon Helper Functions
 * Handles coupon code validation and discount calculations
 */

// Define valid coupon codes and their discount percentages
$validCoupons = [
    'SAVE5' => 5,
    'SAVE10' => 10,
    'SAVE15' => 15,
    'SAVE20' => 20
];

/**
 * Validate coupon code (case-insensitive)
 * 
 * @param string $code Coupon code to validate
 * @return array|false Returns array with code and discount percent, or false if invalid
 */
function validateCouponCode($code) {
    global $validCoupons;
    
    // Convert to uppercase for case-insensitive comparison
    $code = strtoupper(trim($code));
    
    if (isset($validCoupons[$code])) {
        return [
            'code' => $code,
            'discount_percent' => $validCoupons[$code]
        ];
    }
    
    return false;
}

/**
 * Apply coupon to session
 * 
 * @param string $code Coupon code to apply
 * @return array Result with success status and message
 */
function applyCoupon($code) {
    $couponData = validateCouponCode($code);
    
    if ($couponData) {
        $_SESSION['applied_coupon'] = $couponData;
        return [
            'success' => true,
            'message' => "Coupon {$couponData['code']} applied successfully!",
            'coupon' => $couponData
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Invalid coupon code. Please try again.'
    ];
}

/**
 * Remove coupon from session
 * 
 * @return bool True if removed, false if no coupon was applied
 */
function removeCoupon() {
    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
        return true;
    }
    return false;
}

/**
 * Get currently applied coupon from session
 * 
 * @return array|null Coupon data or null if no coupon applied
 */
function getAppliedCoupon() {
    return isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
}

/**
 * Calculate coupon discount amount
 * 
 * @param float $subtotal Subtotal amount to apply discount to
 * @return float Discount amount
 */
function calculateCouponDiscount($subtotal) {
    $coupon = getAppliedCoupon();
    
    if ($coupon && isset($coupon['discount_percent'])) {
        return round($subtotal * $coupon['discount_percent'] / 100);
    }
    
    return 0;
}

/**
 * Get subtotal after coupon discount
 * 
 * @param float $subtotal Original subtotal
 * @return float Subtotal after coupon discount
 */
function getSubtotalAfterCoupon($subtotal) {
    $discount = calculateCouponDiscount($subtotal);
    return $subtotal - $discount;
}
?>
