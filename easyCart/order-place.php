<?php
require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/shipping.php';
require_once 'includes/orders.php';
require_once 'includes/cart-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify user is logged in
    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to place an order.');
        header('Location: login.php?redirect=checkout.php');
        exit;
    }
    
    // Get cart items
    $cartItems = getCartItemsWithDetails();
    $rawCart = getCurrentCart();
    
    // Debug: Check cart status
    if (empty($rawCart)) {
        setFlashMessage('error', 'Your cart is empty (no raw cart data). Please add items to your cart first. <a href="cart-debug.php" class="debug-link-white">Debug Cart</a>');
        header('Location: cart.php');
        exit;
    }
    
    // Check if cart is empty
    if (empty($cartItems)) {
        setFlashMessage('error', 'Your cart is empty (no cart items with details). Please add items before placing an order. <a href="cart-debug.php" class="debug-link-white">Debug Cart</a>');
        header('Location: cart.php');
        exit;
    }
    
    // Get form data
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '';
    $pincode = isset($_POST['pincode']) ? trim($_POST['pincode']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'standard';
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
        empty($address) || empty($city) || empty($state) || empty($pincode) || empty($country)) {
        setFlashMessage('error', 'Please fill in all required fields.');
        header('Location: checkout.php');
        exit;
    }
    
    // Calculate order totals using Phase 4 logic
    $subtotal = getCartSubtotal();
    $shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'standard';
    
    // Validate shipping method is available for current cart
    $availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotal);
    if (!in_array($shippingMethod, $availableShippingMethods)) {
        setFlashMessage('error', 'Selected shipping method is not available for your cart. Please select a valid shipping method.');
        header('Location: checkout.php');
        exit;
    }
    
    // Calculate shipping cost based on method and subtotal
    $shippingCost = calculateShippingCost($subtotal, $shippingMethod);
    
    // Calculate tax on (Subtotal + Shipping) - Phase 4 requirement
    $tax = calculateTax($subtotal, $shippingCost);
    
    // Calculate total
    $total = calculateOrderTotal($subtotal, $shippingCost, $tax);
    
    // Generate order number
    $orderNumber = 'ORD-' . date('Y') . '-' . rand(1000, 9999);
    
    // Prepare order items
    $orderItems = [];
    foreach ($cartItems as $key => $item) {
        $orderItems[] = [
            'product_id' => $item['product']['id'],
            'product_name' => $item['product']['name'],
            'quantity' => $item['quantity'],
            'price' => $item['product']['price'],
            'variant' => $item['variant']
        ];
    }
    
    // Prepare order data for database
    $orderData = [
        'user_id' => $_SESSION['user']['user_id'],
        'order_number' => $orderNumber,
        'subtotal' => $subtotal,
        'shipping_type' => getShippingMethodName($shippingMethod),
        'shipping_cost' => $shippingCost,
        'tax' => $tax,
        'discount' => 0,
        'final_amount' => $total,
        'status' => 'processing',
        'items' => $orderItems,
        'payment_method' => strtoupper($paymentMethod),
        'estimated_delivery' => ($shippingMethod === 'standard') ? '3-5 Business Days' : '1-2 Business Days',
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
        ]
    ];
    
    // Save order to database
    $orderId = saveOrder($orderData);
    
    if ($orderId) {
        // Clear cart session and deactivate in DB
        clearCart();
        deactivateDbCart(session_id());
        
        // Set success message
        setFlashMessage('success', 'Order placed successfully! Your order number is ' . $orderNumber);
        
        // Redirect to orders page
        header('Location: orders.php');
        exit;
    } else {
        setFlashMessage('error', 'Failed to place order. Please try again.');
        header('Location: checkout.php');
        exit;
    }
} else {
    header('Location: cart.php');
    exit;
}
?>
