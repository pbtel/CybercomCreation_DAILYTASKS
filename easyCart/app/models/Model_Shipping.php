<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Shipping extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Shipping';
    }

    public function getAllActive()
    {
        require_once 'Collection_Shipping.php';
        return (new Collection_Shipping())
            ->addFieldToFilter('is_active', true)
            ->getData();
    }

    public function calculateCost($subtotal, $method)
    {
        if (file_exists(__DIR__ . '/../../includes/shipping.php')) {
            require_once __DIR__ . '/../../includes/shipping.php';
            return calculateShippingCost($subtotal, $method);
        }
        return 40; // Fallback
    }

    public function calculateTax($subtotal, $shipping)
    {
        if (file_exists(__DIR__ . '/../../includes/shipping.php')) {
            require_once __DIR__ . '/../../includes/shipping.php';
            return calculateTax($subtotal, $shipping);
        }
        return round(($subtotal + $shipping) * 0.18);
    }

    public function getMethodName($method)
    {
        if (file_exists(__DIR__ . '/../../includes/shipping.php')) {
            require_once __DIR__ . '/../../includes/shipping.php';
            return getShippingMethodName($method);
        }
        return $method;
    }

    public function getAvailableMethods($cartItems, $subtotal)
    {
        if (file_exists(__DIR__ . '/../../includes/shipping-type-helpers.php')) {
            require_once __DIR__ . '/../../includes/shipping-type-helpers.php';
            return getAvailableShippingMethods($cartItems, $subtotal);
        }
        return [];
    }

    public function getSelected()
    {
        return Session::get('selected_shipping_method', null);
    }

    public function getOrSetDefault($cartItems, $subtotal)
    {
        $selected = $this->getSelected();

        if (file_exists(__DIR__ . '/../../includes/shipping-type-helpers.php')) {
            require_once __DIR__ . '/../../includes/shipping-type-helpers.php';
            $available = getAvailableShippingMethods($cartItems, $subtotal);

            if (!$selected || !in_array($selected, $available)) {
                $selected = getDefaultShippingMethod($cartItems, $subtotal);
                $this->setSelected($selected);
            }
        } else {
            $selected = $selected ?: 'standard';
        }

        return $selected;
    }

    public function setSelected($method)
    {
        Session::set('selected_shipping_method', $method);
        return $this;
    }
}
