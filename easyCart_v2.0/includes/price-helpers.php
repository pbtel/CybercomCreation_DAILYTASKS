<?php
/**
 * Price Formatting Helper
 * Provides consistent currency formatting across the application
 */

/**
 * Format price with INR currency symbol
 * Uses HTML entity for rupee symbol to ensure proper display
 * 
 * @param float $price The price to format
 * @param bool $showDecimals Whether to show decimal places (default: false)
 * @return string Formatted price with currency symbol
 */
function formatPrice($price, $showDecimals = false)
{
    if ($showDecimals) {
        return '&#8377;' . number_format($price, 2);
    }
    return '&#8377;' . number_format($price);
}

/**
 * Format price range
 * 
 * @param float $minPrice Minimum price
 * @param float $maxPrice Maximum price
 * @return string Formatted price range
 */
function formatPriceRange($minPrice, $maxPrice)
{
    return formatPrice($minPrice) . ' - ' . formatPrice($maxPrice);
}

/**
 * Get currency symbol as HTML entity
 * 
 * @return string Rupee symbol HTML entity
 */
function getCurrencySymbol()
{
    return '&#8377;';
}
