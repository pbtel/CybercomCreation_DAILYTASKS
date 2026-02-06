<?php
/**
 * Discount Helper Functions
 * Handles first-unit discount calculations
 */

/**
 * Calculate discount percentage based on product price
 * Tiered discount structure:
 * - Price > ₹1500: 15% off
 * - Price > ₹1000: 10% off
 * - Price > ₹500: 5% off
 * - Price <= ₹500: No discount
 * 
 * @param float $price Product price
 * @return int Discount percentage (0, 5, 10, or 15)
 */
function calculateFirstUnitDiscount($price) {
    if ($price > 1500) {
        return 15;
    } elseif ($price > 1000) {
        return 10;
    } elseif ($price > 500) {
        return 5;
    }
    return 0;
}

/**
 * Get discounted price after applying first-unit discount
 * 
 * @param float $price Original product price
 * @return float Discounted price
 */
function getDiscountedPrice($price) {
    $discountPercent = calculateFirstUnitDiscount($price);
    if ($discountPercent > 0) {
        return $price - ($price * $discountPercent / 100);
    }
    return $price;
}

/**
 * Calculate total price for an item with first-unit discount
 * First unit gets discount, additional units at full price
 * 
 * @param float $price Original product price
 * @param int $quantity Quantity of items
 * @return array Array with total, discount info, and breakdown
 */
function calculateItemTotalWithDiscount($price, $quantity) {
    $discountPercent = calculateFirstUnitDiscount($price);
    $discountedPrice = getDiscountedPrice($price);
    
    // First unit at discounted price, rest at full price
    if ($quantity > 1) {
        $total = $discountedPrice + ($price * ($quantity - 1));
    } else {
        $total = $discountedPrice;
    }
    
    // Calculate savings
    $fullPriceTotal = $price * $quantity;
    $savings = $fullPriceTotal - $total;
    
    return [
        'total' => $total,
        'discount_percent' => $discountPercent,
        'unit_price_original' => $price,
        'unit_price_discounted' => $discountedPrice,
        'first_unit_savings' => $price - $discountedPrice,
        'total_savings' => $savings,
        'full_price_total' => $fullPriceTotal
    ];
}

/**
 * Format discount display text
 * 
 * @param float $savings Amount saved
 * @param int $discountPercent Discount percentage
 * @return string Formatted savings text
 */
function formatDiscountText($savings, $discountPercent) {
    if ($savings > 0 && $discountPercent > 0) {
        return "Save ₹" . number_format($savings) . " ({$discountPercent}% off)";
    }
    return "";
}
?>
