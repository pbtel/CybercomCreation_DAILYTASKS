<?php

/**
 * Coupon Model
 * Handles coupon validation and discount calculations
 */
class Model_Coupon
{
    private $validCoupons = [
        'SAVE5' => 5,
        'SAVE10' => 10,
        'SAVE15' => 15,
        'SAVE20' => 20
    ];

    /**
     * Get all currently valid coupons
     */
    public function getAllCoupons()
    {
        return $this->validCoupons;
    }

    /**
     * Validate coupon code
     */
    public function validate($code)
    {
        $code = strtoupper(trim($code));

        if (isset($this->validCoupons[$code])) {
            return [
                'code' => $code,
                'discount_percent' => $this->validCoupons[$code]
            ];
        }

        return false;
    }

    /**
     * Apply coupon to session
     */
    public function apply($code)
    {
        $couponData = $this->validate($code);

        if ($couponData) {
            Session::set('applied_coupon', $couponData);
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
     */
    public function remove()
    {
        if (Session::has('applied_coupon')) {
            Session::remove('applied_coupon');
            return true;
        }
        return false;
    }

    /**
     * Get currently applied coupon
     */
    public function getApplied()
    {
        return Session::get('applied_coupon', null);
    }

    /**
     * Calculate coupon discount amount
     */
    public function calculateDiscount($subtotal)
    {
        $coupon = $this->getApplied();

        if ($coupon && isset($coupon['discount_percent'])) {
            return ($subtotal * $coupon['discount_percent'] / 100);
        }

        return 0;
    }

    /**
     * Get subtotal after coupon discount
     */
    public function getSubtotalAfterDiscount($subtotal)
    {
        $discount = $this->calculateDiscount($subtotal);
        return $subtotal - $discount;
    }
}
