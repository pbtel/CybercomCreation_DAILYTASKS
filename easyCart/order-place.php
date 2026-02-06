<?php
require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/shipping.php';
require_once 'database/orders.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify user is logged in
    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to place an order.');
        header('Location: login.php?redirect=checkout.php');
        exit;
    }

    // Get cart items
    $cartItems = getCartItemsWithDetails();
    $cartId = getCurrentCartId();

    // Check if cart is empty
    if (empty($cartItems)) {
        setFlashMessage('error', 'Your cart is empty. Please add items before placing an order.');
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
    if (
        empty($firstName) || empty($lastName) || empty($email) || empty($phone) ||
        empty($address) || empty($city) || empty($state) || empty($pincode) || empty($country)
    ) {
        setFlashMessage('error', 'Please fill in all required fields.');
        header('Location: checkout.php');
        exit;
    }

    // Calculate order totals
    $subtotal = getCartSubtotal();

    // Apply coupon discount if exists
    $appliedCoupon = getAppliedCoupon();
    $couponCode = null;
    $discountAmount = 0;
    if ($appliedCoupon) {
        $couponCode = $appliedCoupon['code'];
        $discountAmount = calculateCouponDiscount($subtotal);
    }

    // Validate shipping method is available for current cart
    $availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotal);
    if (!in_array($shippingMethod, $availableShippingMethods)) {
        setFlashMessage('error', 'Selected shipping method is not available for your cart. Please select a valid shipping method.');
        header('Location: checkout.php');
        exit;
    }

    // Calculate shipping cost based on method and subtotal (after discount)
    $subtotalAfterDiscount = $subtotal - $discountAmount;
    $shippingCost = calculateShippingCost($subtotalAfterDiscount, $shippingMethod);

    // Calculate tax on (Subtotal after discount + Shipping)
    $tax = calculateTax($subtotalAfterDiscount, $shippingCost);

    // Calculate total
    $total = calculateOrderTotal($subtotalAfterDiscount, $shippingCost, $tax);

    try {
        beginTransaction();

        // Create order in database
        $orderData = [
            'user_id' => $_SESSION['user']['user_id'],
            'cart_id' => $cartId,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'discount_amount' => $discountAmount,
            'final_amount' => $total,
            'status' => 'processing'
        ];

        $order = createOrderDB($orderData);
        $orderId = $order['order_id'];

        // Add order items
        foreach ($cartItems as $key => $item) {
            addOrderItemDB($orderId, [
                'product_id' => $item['product']['id'],
                'product_name' => $item['product']['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['product']['price'],
                'variant' => $item['variant'],
                'subtotal' => $item['subtotal']
            ]);
        }

        // Add shipping address
        addOrderAddressDB($orderId, [
            'full_name' => $firstName . ' ' . $lastName,
            'phone' => $phone,
            'address_line1' => $address,
            'address_line2' => '',
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'country' => $country
        ]);

        // Add billing information with coupon code
        addOrderBillingDB($orderId, [
            'payment_method' => ucfirst($paymentMethod),
            'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'completed',
            'coupon_code' => $couponCode
        ]);

        // Add shipping method
        $shippingTypeInfo = getCartShippingType($cartItems);
        addOrderShippingMethodDB($orderId, [
            'shipping_method' => getShippingMethodName($shippingMethod),
            'shipping_type' => $shippingTypeInfo['type']
        ]);

        commitTransaction();

        // Deactivate cart after successful order (sets is_active = false)
        deactivateCartDB($cartId);

        // Reset session cart ID so a new active cart is created next time
        $_SESSION['cart_id'] = null;

        // Clear applied coupon
        if ($appliedCoupon) {
            removeCoupon();
        }

        // Set success message
        setFlashMessage('success', 'Order placed successfully! Your order number is ' . $order['order_number']);

        // Redirect to orders page
        header('Location: orders.php');
        exit;

    } catch (Exception $e) {
        rollbackTransaction();
        error_log("Order placement failed: " . $e->getMessage());
        setFlashMessage('error', 'Failed to place order. Please try again.');
        header('Location: checkout.php');
        exit;
    }
} else {
    header('Location: cart.php');
    exit;
}
?>