<?php

/**
 * Shipping Model
 * Handles shipping cost calculations and methods
 */
class ShippingModel
{

    /**
     * Calculate shipping cost based on subtotal and method
     */
    public function calculateCost($subtotal, $shippingMethod)
    {
        switch ($shippingMethod) {
            case 'standard':
                return 40;

            case 'express':
                return min(80, round($subtotal * 0.10));

            case 'whiteglove':
                return min(150, round($subtotal * 0.05));

            case 'freight':
                return max(200, round($subtotal * 0.03));

            default:
                return 40;
        }
    }

    /**
     * Get shipping method name
     */
    public function getMethodName($method)
    {
        $names = [
            'standard' => 'Standard Shipping',
            'express' => 'Express Shipping',
            'whiteglove' => 'White Glove Delivery',
            'freight' => 'Freight Shipping'
        ];
        return $names[$method] ?? 'Standard Shipping';
    }

    /**
     * Get estimated delivery days
     */
    public function getDeliveryDays($method)
    {
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
     */
    public function getMethodDescription($method, $subtotal = 0)
    {
        $days = $this->getDeliveryDays($method);
        $cost = $this->calculateCost($subtotal, $method);

        $descriptions = [
            'standard' => "$days business days - ₹" . number_format($cost),
            'express' => "$days business days - ₹" . number_format($cost),
            'whiteglove' => "$days business days - Premium delivery with setup - ₹" . number_format($cost),
            'freight' => "$days business days - For large/heavy items - ₹" . number_format($cost)
        ];

        return $descriptions[$method] ?? "$days business days";
    }

    /**
     * Calculate tax (18% GST on subtotal + shipping)
     */
    public function calculateTax($subtotal, $shipping)
    {
        return round(($subtotal + $shipping) * 0.18);
    }

    /**
     * Calculate order total
     */
    public function calculateOrderTotal($subtotal, $shipping, $tax)
    {
        return $subtotal + $shipping + $tax;
    }

    /**
     * Get available shipping methods based on cart
     */
    public function getAvailableMethods($cartItems, $subtotal)
    {
        // Load shipping type helpers
        require_once __DIR__ . '/../../includes/shipping-type-helpers.php';
        return getAvailableShippingMethods($cartItems, $subtotal);
    }

    /**
     * Get selected shipping method from session
     */
    public function getSelected()
    {
        return Session::get('selected_shipping_method', null);
    }

    /**
     * Set selected shipping method
     */
    public function setSelected($method)
    {
        Session::set('selected_shipping_method', $method);
        return true;
    }

    /**
     * Get or set default shipping method
     */
    public function getOrSetDefault($cartItems, $subtotal)
    {
        $currentMethod = $this->getSelected();
        $availableMethods = $this->getAvailableMethods($cartItems, $subtotal);

        if ($currentMethod && in_array($currentMethod, $availableMethods)) {
            return $currentMethod;
        }

        // Auto-select default
        require_once __DIR__ . '/../../includes/shipping-type-helpers.php';
        $defaultMethod = getDefaultShippingMethod($cartItems, $subtotal);
        $this->setSelected($defaultMethod);
        return $defaultMethod;
    }
}
