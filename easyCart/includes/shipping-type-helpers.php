<?php
/**
 * Shipping Type Helpers - EasyCart
 * Determines shipping types and available shipping methods based on product prices
 */

/**
 * Get shipping type for a product based on its price
 * 
 * @param float $price The product price
 * @return string 'express' or 'freight'
 */
function getProductShippingType($price) {
    return ($price < 300) ? 'express' : 'freight';
}

/**
 * Analyze cart to determine shipping constraints
 * Returns the most restrictive shipping type
 * 
 * @param array $cartItems Cart items with product details
 * @return array ['type' => 'express'|'freight', 'hasFreight' => bool]
 */
function getCartShippingType($cartItems) {
    $hasFreight = false;
    
    foreach ($cartItems as $item) {
        $productPrice = $item['product']['price'];
        $shippingType = getProductShippingType($productPrice);
        
        if ($shippingType === 'freight') {
            $hasFreight = true;
            break; // Freight takes precedence
        }
    }
    
    return [
        'type' => $hasFreight ? 'freight' : 'express',
        'hasFreight' => $hasFreight
    ];
}

/**
 * Get available shipping methods based on cart subtotal after coupon
 * 
 * @param array $cartItems Cart items with product details (not used in new logic)
 * @param float $subtotal Cart subtotal after applying coupon discount
 * @return array Array of available shipping method codes
 */
function getAvailableShippingMethods($cartItems, $subtotal) {
    // New logic: Based solely on subtotal after coupon discount
    // Ignore individual product prices
    
    if ($subtotal >= 300) {
        // High-value cart (>= ₹300): Only Freight and White Glove available
        return ['freight', 'whiteglove'];
    } else {
        // Low-value cart (< ₹300): Only Express and Standard available
        return ['express', 'standard'];
    }
}

/**
 * Get default shipping method based on subtotal after coupon
 * 
 * @param array $cartItems Cart items with product details (not used in new logic)
 * @param float $subtotal Cart subtotal after applying coupon discount
 * @return string Default shipping method code
 */
function getDefaultShippingMethod($cartItems, $subtotal) {
    // New logic: Based solely on subtotal after coupon discount
    
    if ($subtotal >= 300) {
        // High-value cart (>= ₹300): Default to Freight
        return 'freight';
    } else {
        // Low-value cart (< ₹300): Default to Express
        return 'express';
    }
}

/**
 * Check if a shipping method is available for the current cart
 * 
 * @param string $method Shipping method to check
 * @param array $cartItems Cart items with product details
 * @param float $subtotal Cart subtotal
 * @return bool True if method is available
 */
function isShippingMethodAvailable($method, $cartItems, $subtotal) {
    $availableMethods = getAvailableShippingMethods($cartItems, $subtotal);
    return in_array($method, $availableMethods);
}


?>
