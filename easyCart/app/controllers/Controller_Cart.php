<?php

/**
 * Cart Controller
 * Handles shopping cart operations
 */
class Controller_Cart extends Controller
{

    /**
     * Display cart page
     */
    public function index()
    {
        // Guests allowed to view cart
        $userModel = $this->model('Model_User');
        // $userModel->requireLogin('cart');

        // Load models
        $cartModel = $this->model('Model_Cart');
        $couponModel = $this->model('Model_Coupon');
        $shippingModel = $this->model('Model_Shipping');

        // Get cart data
        $cartItems = $cartModel->getItemsWithDetails();
        $subtotal = $cartModel->getSubtotal();

        // Calculate subtotal after applying coupon discount
        $appliedCoupon = $couponModel->getApplied();
        $couponDiscount = 0;
        if ($appliedCoupon) {
            $couponDiscount = $couponModel->calculateDiscount($subtotal);
        }
        $subtotalAfterCoupon = $subtotal - $couponDiscount;

        // Get available shipping methods
        $availableShippingMethods = $shippingModel->getAvailableMethods($cartItems, $subtotalAfterCoupon);

        // Pass data to view
        $data = [
            'pageTitle' => 'Shopping Cart',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'appliedCoupon' => $appliedCoupon,
            'couponDiscount' => $couponDiscount,
            'subtotalAfterCoupon' => $subtotalAfterCoupon,
            'availableShippingMethods' => $availableShippingMethods,
            'availableCoupons' => $couponModel->getAllCoupons(),
            'shippingNote' => 'Calculated at checkout',
            'taxNote' => 'Calculated at checkout'
        ];

        $this->view('cart/index', $data);
    }

    /**
     * Add item to cart
     */
    public function add()
    {
        // Guests allowed to add to cart
        $userModel = $this->model('Model_User');
        // $userModel->requireLogin('cart'); // Remove this line

        if (!$this->isPost()) {
            $this->redirect('products');
            return;
        }

        $productId = $this->post('product_id');
        $quantity = (int) $this->post('quantity', 1);
        // Collect variants dynamically
        $variant = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'variant_') === 0 && !empty($value)) {
                $type = substr($key, 8); // Remove 'variant_' prefix
                $variant[$type] = $value;
            }
        }

        if (!$productId) {
            Session::setFlash('error', 'Invalid product');
            $this->redirect('products');
            return;
        }

        // Add to cart
        $cartModel = $this->model('Model_Cart');
        $success = $cartModel->addToCart($productId, $quantity, $variant);

        if ($success) {
            Session::setFlash('success', 'Product added to cart!');
        } else {
            Session::setFlash('error', 'Failed to add product to cart');
        }

        // Redirect back or to cart
        $redirect = $this->post('redirect', 'cart');
        $this->redirect($redirect);
    }

    /**
     * Update cart item quantity
     */
    public function update()
    {
        // Guests allowed to update cart
        $userModel = $this->model('Model_User');
        // $userModel->requireLogin('cart'); // Remove this line

        if (!$this->isPost()) {
            $this->redirect('cart');
            return;
        }

        $cartItemKey = $this->post('cart_key');
        $quantity = (int) $this->post('quantity', 1);

        if (!$cartItemKey) {
            Session::setFlash('error', 'Invalid cart item');
            $this->redirect('cart');
            return;
        }

        // Update cart
        $cartModel = $this->model('Model_Cart');
        $success = $cartModel->updateQuantity($cartItemKey, $quantity);

        if ($success) {
            Session::setFlash('success', 'Cart updated!');
        } else {
            Session::setFlash('error', 'Failed to update cart');
        }

        $this->redirect('cart');
    }

    /**
     * Remove item from cart
     */
    public function remove()
    {
        // Guests allowed to remove from cart
        $userModel = $this->model('Model_User');
        // $userModel->requireLogin('cart'); // Remove this line

        if (!$this->isPost()) {
            $this->redirect('cart');
            return;
        }

        $cartItemKey = $this->post('cart_key');

        if (!$cartItemKey) {
            Session::setFlash('error', 'Invalid cart item');
            $this->redirect('cart');
            return;
        }

        // Remove from cart
        $cartModel = $this->model('Model_Cart');
        $success = $cartModel->removeItem($cartItemKey);

        if ($success) {
            Session::setFlash('success', 'Item removed from cart');
        } else {
            Session::setFlash('error', 'Failed to remove item');
        }

        $this->redirect('cart');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        // Guests allowed to clear cart
        $userModel = $this->model('Model_User');
        // $userModel->requireLogin('cart'); // Remove this line

        if (!$this->isPost()) {
            $this->redirect('cart');
            return;
        }

        // Clear cart
        $cartModel = $this->model('Model_Cart');
        $cartModel->clearCart();

        Session::setFlash('success', 'Cart cleared');
        $this->redirect('cart');
    }
}
