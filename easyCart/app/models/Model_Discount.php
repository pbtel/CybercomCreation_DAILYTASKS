<?php

/**
 * Discount Model
 * Handles first-unit discount calculations
 */
class Model_Discount {
    
    /**
     * Calculate discount percentage based on product price
     * Tiered discount structure:
     * - Price > ₹1500: 15% off
     * - Price > ₹1000: 10% off
     * - Price > ₹500: 5% off
     * - Price <= ₹500: No discount
     */
    public function calculateFirstUnitDiscount($price) {
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
     */
    public function getDiscountedPrice($price) {
        $discountPercent = $this->calculateFirstUnitDiscount($price);
        if ($discountPercent > 0) {
            return $price - ($price * $discountPercent / 100);
        }
        return $price;
    }

    /**
     * Calculate total price for an item with first-unit discount
     * First unit gets discount, additional units at full price
     */
    public function calculateItemTotal($price, $quantity) {
        $discountPercent = $this->calculateFirstUnitDiscount($price);
        $discountedPrice = $this->getDiscountedPrice($price);
        
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
     */
    public function formatDiscountText($savings, $discountPercent) {
        if ($savings > 0 && $discountPercent > 0) {
            return "Save ₹" . number_format($savings) . " ({$discountPercent}% off)";
        }
        return "";
    }
}
