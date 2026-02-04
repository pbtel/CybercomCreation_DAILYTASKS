<?php

/**
 * Api Controller
 * Handles all AJAX requests
 */
class ApiController extends Controller {

    /**
     * Add to cart AJAX
     */
    public function cartAdd() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $productId = intval($this->post('product_id'));
        $quantity = intval($this->post('quantity', 1));

        $variant = [];
        if ($this->post('variant_color')) $variant['color'] = $this->post('variant_color');
        if ($this->post('variant_storage')) $variant['storage'] = $this->post('variant_storage');
        if ($this->post('variant_size')) $variant['size'] = $this->post('variant_size');

        $productModel = $this->model('ProductModel');
        $product = $productModel->getById($productId);

        if (!$product) {
            return $this->json(['success' => false, 'message' => 'Product not found']);
        }

        if ($product['stock'] <= 0) {
            return $this->json(['success' => false, 'message' => 'Product is out of stock']);
        }

        if ($quantity <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid quantity']);
        }

        if ($quantity > $product['stock']) {
            return $this->json(['success' => false, 'message' => 'Requested quantity exceeds available stock']);
        }

        try {
            $cartModel = $this->model('CartModel');
            $cartModel->addToCart($productId, $quantity, $variant);
            
            return $this->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $cartModel->getCount(),
                'cart_subtotal' => $cartModel->getSubtotal(),
                'product_name' => $product['name']
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to add product to cart']);
        }
    }

    /**
     * Update cart quantity AJAX
     */
    public function cartUpdate() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $cartKey = $this->post('cart_key');
        $quantity = intval($this->post('quantity'));

        if (!$cartKey || $quantity < 0) {
            return $this->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $cartModel = $this->model('CartModel');
            $success = $cartModel->updateQuantity($cartKey, $quantity);

            if ($success) {
                $subtotal = $cartModel->getSubtotal();
                $couponModel = $this->model('CouponModel');
                $appliedCoupon = $couponModel->getApplied();
                $discount = $couponModel->calculateDiscount($subtotal);
                $finalSubtotal = $subtotal - $discount;

                return $this->json([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart_count' => $cartModel->getCount(),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'final_subtotal' => $finalSubtotal,
                    'has_items' => $cartModel->getCount() > 0
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Item not found in cart']);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to update cart']);
        }
    }

    /**
     * Remove from cart AJAX
     */
    public function cartRemove() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $cartKey = $this->post('cart_key');

        if (!$cartKey) {
            return $this->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $cartModel = $this->model('CartModel');
            $success = $cartModel->removeItem($cartKey);

            if ($success) {
                $subtotal = $cartModel->getSubtotal();
                $couponModel = $this->model('CouponModel');
                $discount = $couponModel->calculateDiscount($subtotal);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Item removed',
                    'cart_count' => $cartModel->getCount(),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'final_subtotal' => $subtotal - $discount,
                    'has_items' => $cartModel->getCount() > 0
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Item not found in cart']);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to remove item']);
        }
    }

    /**
     * Get cart summary AJAX
     */
    public function cartSummary() {
        $cartModel = $this->model('CartModel');
        $couponModel = $this->model('CouponModel');
        $shippingModel = $this->model('ShippingModel');

        $subtotal = $cartModel->getSubtotal();
        $discount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $discount;

        $shippingMethod = $shippingModel->getSelected();
        $shippingCost = 0;
        if ($shippingMethod) {
            $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        }

        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $total = $subtotalAfterCoupon + $shippingCost + $tax;

        return $this->json([
            'success' => true,
            'cart_count' => $cartModel->getCount(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'subtotal_after_coupon' => $subtotalAfterCoupon,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'total' => $total
        ]);
    }

    /**
     * Apply coupon AJAX
     */
    public function couponApply() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $code = $this->post('coupon_code');
        if (!$code) {
            return $this->json(['success' => false, 'message' => 'Coupon code is required']);
        }

        $couponModel = $this->model('CouponModel');
        $result = $couponModel->apply($code);

        if ($result['success']) {
            $cartModel = $this->model('CartModel');
            $subtotal = $cartModel->getSubtotal();
            $discount = $couponModel->calculateDiscount($subtotal);

            return $this->json([
                'success' => true,
                'message' => $result['message'],
                'discount_percent' => $result['coupon']['discount_percent'],
                'discount_amount' => $discount,
                'new_subtotal' => $subtotal - $discount
            ]);
        } else {
            return $this->json(['success' => false, 'message' => $result['message']]);
        }
    }

    /**
     * Remove coupon AJAX
     */
    public function couponRemove() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $couponModel = $this->model('CouponModel');
        $couponModel->remove();

        $cartModel = $this->model('CartModel');
        $subtotal = $cartModel->getSubtotal();

        return $this->json([
            'success' => true,
            'message' => 'Coupon removed',
            'new_subtotal' => $subtotal
        ]);
    }

    /**
     * Calculate shipping AJAX
     */
    public function shippingCalculate() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $method = $this->post('shipping_method');
        if (!$method) {
            return $this->json(['success' => false, 'message' => 'Shipping method is required']);
        }

        $cartModel = $this->model('CartModel');
        $couponModel = $this->model('CouponModel');
        $shippingModel = $this->model('ShippingModel');

        $subtotal = $cartModel->getSubtotal();
        $discount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $discount;

        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $method);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $total = $subtotalAfterCoupon + $shippingCost + $tax;

        return $this->json([
            'success' => true,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
            'method_name' => $shippingModel->getMethodName($method)
        ]);
    }

    /**
     * Update shipping method AJAX
     */
    public function shippingMethodUpdate() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $method = $this->post('shipping_method');
        if (!$method) {
            return $this->json(['success' => false, 'message' => 'Shipping method is required']);
        }

        $shippingModel = $this->model('ShippingModel');
        $shippingModel->setSelected($method);

        return $this->json(['success' => true, 'message' => 'Shipping method updated']);
    }
}
