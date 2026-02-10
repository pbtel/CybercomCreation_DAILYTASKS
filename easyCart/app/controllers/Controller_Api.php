<?php

/**
 * Api Controller
 * Handles all AJAX requests
 */
class Controller_Api extends Controller
{

    /**
     * Add to cart AJAX
     */
    public function cartAdd()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Please login to add items to cart', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $productId = intval($this->post('product_id'));
        $quantity = intval($this->post('quantity', 1));

        // Collect variants dynamically
        $variant = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'variant_') === 0 && !empty($value)) {
                $type = substr($key, 8); // Remove 'variant_' prefix
                $variant[$type] = $value;
            }
        }

        $productModel = $this->model('Model_Product');
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
            $cartModel = $this->model('Model_Cart');
            $cartModel->addToCart($productId, $quantity, $variant);

            return $this->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $cartModel->getCount(),
                'cart_subtotal' => $cartModel->getSubtotal(),
                'product_name' => $product['name']
            ]);
        } catch (Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to add product to cart: ' . $e->getMessage()]);
        }
    }

    /**
     * Update cart quantity AJAX
     */
    public function cartUpdate()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Please login to update cart', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $cartKey = $this->post('cart_key');
        $quantity = intval($this->post('quantity'));

        if (!$cartKey || $quantity < 0) {
            return $this->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $cartModel = $this->model('Model_Cart');
            $success = $cartModel->updateQuantity($cartKey, $quantity);

            if ($success) {
                $subtotal = $cartModel->getSubtotal();
                $couponModel = $this->model('Model_Coupon');
                $appliedCoupon = $couponModel->getApplied();
                $discount = $couponModel->calculateDiscount($subtotal);
                $finalSubtotal = $subtotal - $discount;

                $cartItems = $cartModel->getItemsWithDetails();
                $itemSubtotal = 0;
                if (isset($cartItems[$cartKey])) {
                    $itemSubtotal = $cartItems[$cartKey]['subtotal'];
                }

                return $this->json([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart_count' => $cartModel->getCount(),
                    'subtotal' => $subtotal,
                    'cart_subtotal' => $subtotal,
                    'item_subtotal' => $itemSubtotal,
                    'discount' => $discount,
                    'final_subtotal' => $finalSubtotal,
                    'has_items' => $cartModel->getCount() > 0
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Item not found in cart']);
            }
        } catch (Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to update cart: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove from cart AJAX
     */
    public function cartRemove()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Please login to modify cart', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $cartKey = $this->post('cart_key');

        if (!$cartKey) {
            return $this->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $cartModel = $this->model('Model_Cart');
            $success = $cartModel->removeItem($cartKey);

            if ($success) {
                $subtotal = $cartModel->getSubtotal();
                $couponModel = $this->model('Model_Coupon');
                $discount = $couponModel->calculateDiscount($subtotal);

                return $this->json([
                    'success' => true,
                    'message' => 'Item removed',
                    'cart_count' => $cartModel->getCount(),
                    'subtotal' => $subtotal,
                    'cart_subtotal' => $subtotal,
                    'discount' => $discount,
                    'final_subtotal' => $subtotal - $discount,
                    'has_items' => $cartModel->getCount() > 0
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Item not found in cart']);
            }
        } catch (Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to remove item: ' . $e->getMessage()]);
        }
    }

    /**
     * Get cart summary AJAX
     */
    public function cartSummary()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Unauthorized access', 'login_required' => true]);
        // }

        $cartModel = $this->model('Model_Cart');
        $couponModel = $this->model('Model_Coupon');
        $shippingModel = $this->model('Model_Shipping');

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
    public function couponApply()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Please login to apply coupons', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $code = $this->post('coupon_code');
        if (!$code) {
            return $this->json(['success' => false, 'message' => 'Coupon code is required']);
        }

        $couponModel = $this->model('Model_Coupon');
        $result = $couponModel->apply($code);

        if ($result['success']) {
            $cartModel = $this->model('Model_Cart');
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
    public function couponRemove()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Unauthorized access', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $couponModel = $this->model('Model_Coupon');
        $couponModel->remove();

        $cartModel = $this->model('Model_Cart');
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
    public function shippingCalculate()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Unauthorized access', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $method = $this->post('shipping_method');
        if (!$method) {
            return $this->json(['success' => false, 'message' => 'Shipping method is required']);
        }

        $cartModel = $this->model('Model_Cart');
        $couponModel = $this->model('Model_Coupon');
        $shippingModel = $this->model('Model_Shipping');

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
    public function shippingMethodUpdate()
    {
        // $userModel = $this->model('Model_User');
        // if (!$userModel->isLoggedIn()) {
        //     return $this->json(['success' => false, 'message' => 'Unauthorized access', 'login_required' => true]);
        // }

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }

        $method = $this->post('shipping_method');
        if (!$method) {
            return $this->json(['success' => false, 'message' => 'Shipping method is required']);
        }

        $shippingModel = $this->model('Model_Shipping');
        $shippingModel->setSelected($method);

        return $this->json(['success' => true, 'message' => 'Shipping method updated']);
    }

    /**
     * Get chart data for orders visualization
     */
    public function chartData()
    {
        if (!isset($_SESSION['user'])) {
            return $this->json(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $orderModel = $this->model('Model_Order');
            $chartData = $orderModel->getChartData();

            // Format for Chart.js
            $labels = [];
            $amounts = [];

            foreach ($chartData as $row) {
                $labels[] = date('M d', strtotime($row['date']));
                $amounts[] = (float) $row['total'];
            }

            return $this->json([
                'success' => true,
                'labels' => $labels,
                'data' => $amounts
            ]);
        } catch (Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
