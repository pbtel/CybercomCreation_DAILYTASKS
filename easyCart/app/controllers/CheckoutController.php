<?php

/**
 * Checkout Controller
 * Handles checkout process and order placement
 */
class CheckoutController extends Controller {
    
    /**
     * Display checkout page
     */
    public function index() {
        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('checkout');
        
        // Load models
        $cartModel = $this->model('CartModel');
        $couponModel = $this->model('CouponModel');
        $shippingModel = $this->model('ShippingModel');
        
        // Get cart data
        $cartItems = $cartModel->getItemsWithDetails();
        $subtotal = $cartModel->getSubtotal();
        
        // Redirect if cart is empty
        if (empty($cartItems)) {
            $this->redirect('cart');
            return;
        }
        
        // Apply coupon discount if available
        $appliedCoupon = $couponModel->getApplied();
        $couponDiscount = 0;
        if ($appliedCoupon) {
            $couponDiscount = $couponModel->calculateDiscount($subtotal);
        }
        $subtotalAfterCoupon = $subtotal - $couponDiscount;
        
        // Get available shipping methods and auto-select default if needed
        $availableShippingMethods = $shippingModel->getAvailableMethods($cartItems, $subtotalAfterCoupon);
        $selectedShippingMethod = $shippingModel->getOrSetDefault($cartItems, $subtotalAfterCoupon);
        
        // Validate selected method is available
        if (!in_array($selectedShippingMethod, $availableShippingMethods)) {
            require_once __DIR__ . '/../../includes/shipping-type-helpers.php';
            $selectedShippingMethod = getDefaultShippingMethod($cartItems, $subtotalAfterCoupon);
            $shippingModel->setSelected($selectedShippingMethod);
        }
        
        // Calculate costs
        $shipping = $shippingModel->calculateCost($subtotalAfterCoupon, $selectedShippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shipping);
        $total = $shippingModel->calculateOrderTotal($subtotalAfterCoupon, $shipping, $tax);
        
        // Pass data to view
        $data = [
            'pageTitle' => 'Checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'appliedCoupon' => $appliedCoupon,
            'couponDiscount' => $couponDiscount,
            'subtotalAfterCoupon' => $subtotalAfterCoupon,
            'availableShippingMethods' => $availableShippingMethods,
            'selectedShippingMethod' => $selectedShippingMethod,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ];
        
        $this->view('checkout/index', $data);
    }
    
    /**
     * Place order
     */
    public function place() {
        if (!$this->isPost()) {
            $this->redirect('checkout');
            return;
        }
        
        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('checkout');
        
        // Load models
        $cartModel = $this->model('CartModel');
        $orderModel = $this->model('OrderModel');
        $couponModel = $this->model('CouponModel');
        $shippingModel = $this->model('ShippingModel');
        
        // Get cart data
        $cartItems = $cartModel->getItemsWithDetails();
        $subtotal = $cartModel->getSubtotal();
        
        if (empty($cartItems)) {
            Session::setFlash('error', 'Your cart is empty');
            $this->redirect('cart');
            return;
        }
        
        // Get form data
        $firstName = $this->post('first_name');
        $lastName = $this->post('last_name');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $address = $this->post('address');
        $city = $this->post('city');
        $state = $this->post('state');
        $pincode = $this->post('pincode');
        $country = $this->post('country', 'IN');
        $paymentMethod = $this->post('payment_method', 'cod');
        $shippingMethod = $this->post('shipping_method');
        
        // Validate required fields
        if (!$firstName || !$lastName || !$email || !$phone || !$address || !$city || !$state || !$pincode) {
            Session::setFlash('error', 'Please fill all required fields');
            $this->redirect('checkout');
            return;
        }
        
        // Calculate costs
        $appliedCoupon = $couponModel->getApplied();
        $couponDiscount = 0;
        if ($appliedCoupon) {
            $couponDiscount = $couponModel->calculateDiscount($subtotal);
        }
        $subtotalAfterCoupon = $subtotal - $couponDiscount;
        
        $shipping = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shipping);
        $total = $shippingModel->calculateOrderTotal($subtotalAfterCoupon, $shipping, $tax);
        
        // Prepare order data
        $user = $userModel->getCurrentUser();
        $orderNumber = $orderModel->generateOrderNumber();
        
        $orderData = [
            'user_id' => $user['user_id'],
            'order_number' => $orderNumber,
            'subtotal' => $subtotal,
            'discount' => $couponDiscount,
            'tax' => $tax,
            'final_amount' => $total,
            'applied_coupon' => $appliedCoupon ? $appliedCoupon['code'] : null,
            'status' => 'pending',
            'shipping_type' => $shippingMethod,
            'shipping_cost' => $shipping,
            'estimated_delivery' => $shippingModel->getDeliveryDays($shippingMethod) . ' business days',
            'payment_method' => $paymentMethod,
            'address' => [
                'full_name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'phone' => $phone,
                'address_line1' => $address,
                'address_line2' => '',
                'city' => $city,
                'state' => $state,
                'postal_code' => $pincode,
                'country' => $country
            ],
            'items' => []
        ];
        
        // Add cart items to order
        foreach ($cartItems as $item) {
            $orderData['items'][] = [
                'product_id' => $item['product']['id'],
                'product_name' => $item['product']['name'],
                'quantity' => $item['quantity'],
                'price' => $item['unit_price_discounted'],
                'variant' => $item['variant']
            ];
        }
        
        // Save order
        $orderId = $orderModel->save($orderData);
        
        if ($orderId) {
            // Clear cart
            $cartModel->clearCart();
            
            // Remove coupon
            $couponModel->remove();
            
            Session::setFlash('success', 'Order placed successfully! Order #' . $orderNumber);
            $this->redirect('orders');
        } else {
            Session::setFlash('error', 'Failed to place order. Please try again.');
            $this->redirect('checkout');
        }
    }
}
