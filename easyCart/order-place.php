<?php
require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/shipping.php';

// Define orders database file path
define('ORDERS_DB_FILE', __DIR__ . '/data/orders_db.json');

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
        setFlashMessage('error', 'Your cart is empty (no raw cart data). Please add items to your cart first. <a href="cart-debug.php" style="color: white; text-decoration: underline;">Debug Cart</a>');
        header('Location: cart.php');
        exit;
    }
    
    // Check if cart is empty
    if (empty($cartItems)) {
        setFlashMessage('error', 'Your cart is empty (no cart items with details). Please add items before placing an order. <a href="cart-debug.php" style="color: white; text-decoration: underline;">Debug Cart</a>');
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
    
    // Calculate estimated delivery using new shipping methods
    $deliveryDays = getDeliveryDays($shippingMethod);
    $estimatedDelivery = date('Y-m-d', strtotime("+$deliveryDays days"));
    
    // Create order object
    $order = [
        'order_id' => $orderNumber,
        'user_id' => $_SESSION['user']['user_id'],
        'date' => date('Y-m-d H:i:s'),
        'status' => 'processing',
        'payment_method' => ucfirst($paymentMethod),
        'shipping_method' => getShippingMethodName($shippingMethod),
        'shipping_address' => [
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'country' => $country
        ],
        'items' => $orderItems,
        'subtotal' => $subtotal,
        'shipping' => $shippingCost,
        'tax' => $tax,
        'discount' => 0,
        'total' => $total,
        'tracking_number' => null,
        'estimated_delivery' => $estimatedDelivery
    ];
    
    // Load existing orders
    $orders = [];
    if (file_exists(ORDERS_DB_FILE)) {
        $ordersData = file_get_contents(ORDERS_DB_FILE);
        $orders = json_decode($ordersData, true) ?: [];
    }
    
    // Add new order to the beginning of the array
    array_unshift($orders, $order);
    
    // Save orders to file
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    file_put_contents(ORDERS_DB_FILE, json_encode($orders, JSON_PRETTY_PRINT));
    
    // Clear cart
    clearCart();
    
    // Set success message
    setFlashMessage('success', 'Order placed successfully! Your order number is ' . $orderNumber);
    
    // Redirect to orders page
    header('Location: orders.php');
    exit;
} else {
    header('Location: cart.php');
    exit;
}
?>
