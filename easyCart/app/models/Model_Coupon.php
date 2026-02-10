<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Coupon extends Core_Model
{
    protected $_validCoupons = [
        'SAVE5' => 5,
        'SAVE10' => 10,
        'SAVE15' => 15,
        'SAVE20' => 20
    ];

    protected function _init()
    {
        $this->_resourceName = 'Resource_Coupon';
    }

    public function getByCode($code)
    {
        $this->load($code, 'code');
        return $this->getData();
    }

    public function getAllActive()
    {
        require_once 'Collection_Coupon.php';
        return (new Collection_Coupon())->addFieldToFilter('is_active', true)->getData();
    }

    public function getApplied()
    {
        return isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
    }

    public function calculateDiscount($subtotal)
    {
        $coupon = $this->getApplied();
        if ($coupon && isset($coupon['discount_percent'])) {
            return ($subtotal * $coupon['discount_percent'] / 100);
        }
        return 0;
    }

    public function apply($code)
    {
        $code = strtoupper(trim($code));
        if (isset($this->_validCoupons[$code])) {
            $couponData = [
                'code' => $code,
                'discount_percent' => $this->_validCoupons[$code]
            ];
            $_SESSION['applied_coupon'] = $couponData;
            return [
                'success' => true,
                'message' => "Coupon {$code} applied successfully!",
                'coupon' => $couponData
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid coupon code. Please try again.'
        ];
    }

    public function remove()
    {
        if (isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
            return true;
        }
        return false;
    }

    public function getAllCoupons()
    {
        return $this->_validCoupons;
    }
}
