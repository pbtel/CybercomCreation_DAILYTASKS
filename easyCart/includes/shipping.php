<?php
/**
 * Shipping Cost Calculation - Phase 4
 * Centralized shipping logic for EasyCart
 */

/**
 * Calculate shipping cost based on subtotal and shipping method
 * 
 * @param float $subtotal The cart subtotal amount
 * @param string $shippingMethod The selected shipping method
 * @return int The calculated shipping cost
 */
function calculateShippingCost($subtotal, $shippingMethod) {
    switch ($shippingMethod) {
        case 'standard':
            // Flat ₹3,320
            return 3320;
            
        case 'express':
            // Lower of ₹6,640 or 10% of subtotal
            return min(6640, round($subtotal * 0.10));
            
        case 'whiteglove':
            // Lower of ₹12,450 or 5% of subtotal
            return min(12450, round($subtotal * 0.05));
            
        case 'freight':
            // 3% of subtotal with minimum ₹16,600
            return max(16600, round($subtotal * 0.03));
            
        default:
            // Default to standard shipping
            return 3320;
    }
}

/**
 * Get human-readable shipping method name
 * 
 * @param string $method The shipping method code
 * @return string The display name
 */
function getShippingMethodName($method) {
    $names = [
        'standard' => 'Standard Shipping',
        'express' => 'Express Shipping',
        'whiteglove' => 'White Glove Delivery',
        'freight' => 'Freight Shipping'
    ];
    return $names[$method] ?? 'Standard Shipping';
}

/**
 * Get estimated delivery days for shipping method
 * 
 * @param string $method The shipping method code
 * @return int Number of days for delivery
 */
function getDeliveryDays($method) {
    $days = [
        'standard' => 7,
        'express' => 3,
        'whiteglove' => 5,
        'freight' => 10
    ];
    return $days[$method] ?? 7;
}

/**
 * Get shipping method description
 * 
 * @param string $method The shipping method code
 * @param float $subtotal The cart subtotal (for dynamic cost display)
 * @return string The description with delivery time and cost
 */
function getShippingMethodDescription($method, $subtotal = 0) {
    $days = getDeliveryDays($method);
    $cost = calculateShippingCost($subtotal, $method);
    
    $descriptions = [
        'standard' => "$days business days - ₹" . number_format($cost),
        'express' => "$days business days - ₹" . number_format($cost),
        'whiteglove' => "$days business days - Premium delivery with setup - ₹" . number_format($cost),
        'freight' => "$days business days - For large/heavy items - ₹" . number_format($cost)
    ];
    
    return $descriptions[$method] ?? "$days business days";
}

/**
 * Calculate tax on subtotal + shipping (18% GST)
 * 
 * @param float $subtotal The cart subtotal
 * @param float $shipping The shipping cost
 * @return int The calculated tax amount
 */
function calculateTax($subtotal, $shipping) {
    return round(($subtotal + $shipping) * 0.18);
}

/**
 * Calculate order total
 * 
 * @param float $subtotal The cart subtotal
 * @param float $shipping The shipping cost
 * @param float $tax The tax amount
 * @return float The total amount
 */
function calculateOrderTotal($subtotal, $shipping, $tax) {
    return $subtotal + $shipping + $tax;
}
?>
