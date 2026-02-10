<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Discount extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Discount';
    }

    public function getActiveForProduct($productId)
    {
        require_once 'Collection_Discount.php';
        return (new Collection_Discount())
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('is_active', true)
            ->getData();
    }

    public function calculateItemTotal($price, $quantity)
    {
        if (file_exists(__DIR__ . '/../../includes/discount-helpers.php')) {
            require_once __DIR__ . '/../../includes/discount-helpers.php';
            return calculateItemTotalWithDiscount($price, $quantity);
        }

        // Fallback if helper doesn't exist
        return [
            'total' => $price * $quantity,
            'discount_percent' => 0,
            'unit_price_original' => $price,
            'unit_price_discounted' => $price,
            'first_unit_savings' => 0,
            'total_savings' => 0,
            'full_price_total' => $price * $quantity
        ];
    }
}
