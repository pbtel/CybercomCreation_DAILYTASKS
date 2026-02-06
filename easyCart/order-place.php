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

    $userId = $_SESSION['user']['user_id'];

    // Get current cart items (could be from sales_cart or a cart-order)
    $cartItems = getCartItemsWithDetails();

    // Find the current active cart order
    $activeOrder = getActiveCartOrderDB($userId);

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

    // Validate shipping method
    $availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotal);
    if (!in_array($shippingMethod, $availableShippingMethods)) {
        setFlashMessage('error', 'Selected shipping method is not available. Please select a valid shipping method.');
        header('Location: checkout.php');
        exit;
    }

    // Calculate costs
    $subtotalAfterDiscount = $subtotal - $discountAmount;
    $shippingCost = calculateShippingCost($subtotalAfterDiscount, $shippingMethod);
    $tax = calculateTax($subtotalAfterDiscount, $shippingCost);
    $total = calculateOrderTotal($subtotalAfterDiscount, $shippingCost, $tax);

    try {
        beginTransaction();

        $orderId = null;
        $orderNumber = null;

        if ($activeOrder) {
            // Update existing cart-order to become a real order
            $orderId = $activeOrder['order_id'];
            $orderNumber = $activeOrder['order_number'];

            dbUpdate('sales_order', [
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount_amount' => $discountAmount,
                'final_amount' => $total,
                'status' => 'processing',
                'customer_email' => $email,
                'customer_phone' => $phone,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'order_id = :id', [':id' => $orderId]);
        } else {
            // Fallback: Create new order (should not normally happen with current logic)
            $order = createOrderDB([
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount_amount' => $discountAmount,
                'final_amount' => $total,
                'status' => 'processing',
                'customer_email' => $email,
                'customer_phone' => $phone
            ]);
            $orderId = $order['order_id'];
            $orderNumber = $order['order_number'];

            // Move items if they weren't in order tables (e.g. if we somehow bypass login migration)
            // But we know getCartItemsWithDetails handles the switch.
            // If activeOrder is null, it means items are still in sales_cart (unlikely ifLoggedIn)
            if (!empty($cartItems)) {
                foreach ($cartItems as $item) {
                    addOrderItemDB($orderId, [
                        'product_id' => $item['product']['id'],
                        'product_name' => $item['product']['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']['price'],
                        'variant' => $item['variant'],
                        'subtotal' => $item['subtotal']
                    ]);
                }
            }
        }

        // Add/Update shipping address
        // Delete old one first if exists to avoid duplicates
        dbDelete('sales_order_address', 'order_id = :id', [':id' => $orderId]);
        addOrderAddressDB($orderId, [
            'full_name' => $firstName . ' ' . $lastName,
            'phone' => $phone,
            'address_line1' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'country' => $country
        ]);

        // Add/Update billing
        dbDelete('sales_order_billing', 'order_id = :id', [':id' => $orderId]);
        addOrderBillingDB($orderId, [
            'payment_method' => ucfirst($paymentMethod),
            'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'completed',
            'coupon_code' => $couponCode
        ]);

        // Add/Update shipping method
        dbDelete('sales_order_shipping_method', 'order_id = :id', [':id' => $orderId]);
        $shippingTypeInfo = getCartShippingType($cartItems);
        addOrderShippingMethodDB($orderId, [
            'shipping_method' => getShippingMethodName($shippingMethod),
            'shipping_type' => $shippingTypeInfo['type']
        ]);

        commitTransaction();

        // If items were in guest cart, deactivate it (though they should have been migrated)
        $guestCartId = $_SESSION['guest_cart_id'] ?? null;
        if ($guestCartId) {
            deactivateCartDB($guestCartId);
            unset($_SESSION['guest_cart_id']);
        }

        // Reset session cart ID
        $_SESSION['cart_id'] = null;

        if ($appliedCoupon) {
            removeCoupon();
        }

        setFlashMessage('success', 'Order placed successfully! Your order number is ' . $orderNumber);
        header('Location: orders.php');
        exit;

    } catch (Exception $e) {
        if (getDBConnection()->inTransaction()) {
            rollbackTransaction();
        }
        error_log("Order placement failed: " . $e->getMessage());
        setFlashMessage('error', 'Failed to place order. ' . $e->getMessage());
        header('Location: checkout.php');
        exit;
    }
} else {
    header('Location: cart.php');
    exit;
}
?>