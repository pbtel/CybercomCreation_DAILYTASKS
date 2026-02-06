<?php
/**
 * Order Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';

/**
 * Create a new order
 */
function createOrderDB($orderData)
{
    // Generate unique order number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

    $order = dbInsert('sales_order', [
        'user_id' => $orderData['user_id'] ?? null,
        'order_number' => $orderNumber,
        'subtotal' => $orderData['subtotal'],
        'shipping_cost' => $orderData['shipping_cost'] ?? 0,
        'tax' => $orderData['tax'] ?? 0,
        'discount_amount' => $orderData['discount_amount'] ?? 0,
        'final_amount' => $orderData['final_amount'],
        'status' => $orderData['status'] ?? 'pending',
        'customer_email' => $orderData['customer_email'] ?? null,
        'customer_phone' => $orderData['customer_phone'] ?? null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    return $order;
}

/**
 * Add order item
 */
function addOrderItemDB($orderId, $itemData)
{
    return dbInsert('sales_order_product', [
        'order_id' => $orderId,
        'product_id' => $itemData['product_id'] ?? null,
        'product_name' => $itemData['product_name'],
        'quantity' => $itemData['quantity'],
        'unit_price' => $itemData['unit_price'],
        'variant_data' => json_encode($itemData['variant'] ?? []),
        'subtotal' => $itemData['subtotal'],
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Add order address
 */
function addOrderAddressDB($orderId, $addressData)
{
    return dbInsert('sales_order_address', [
        'order_id' => $orderId,
        'full_name' => $addressData['full_name'],
        'phone' => $addressData['phone'],
        'address_line1' => $addressData['address_line1'],
        'address_line2' => $addressData['address_line2'] ?? '',
        'city' => $addressData['city'],
        'state' => $addressData['state'],
        'pincode' => $addressData['pincode'],
        'country' => $addressData['country'] ?? 'India',
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Add order billing
 */
function addOrderBillingDB($orderId, $billingData)
{
    return dbInsert('sales_order_billing', [
        'order_id' => $orderId,
        'payment_method' => $billingData['payment_method'],
        'transaction_id' => $billingData['transaction_id'] ?? null,
        'payment_status' => $billingData['payment_status'] ?? 'pending',
        'coupon_code' => $billingData['coupon_code'] ?? null,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Add order shipping method
 */
function addOrderShippingMethodDB($orderId, $shippingData)
{
    return dbInsert('sales_order_shipping_method', [
        'order_id' => $orderId,
        'shipping_method' => $shippingData['shipping_method'],
        'shipping_type' => $shippingData['shipping_type'],
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Get order by ID
 */
function getOrderByIdDB($orderId)
{
    $sql = "SELECT * FROM sales_order WHERE order_id = :id";
    $order = fetchOne($sql, [':id' => $orderId]);

    if ($order) {
        $order['items'] = getOrderItemsDB($orderId);
        $order['address'] = getOrderAddressDB($orderId);
        $order['billing'] = getOrderBillingDB($orderId);
        $order['shipping'] = getOrderShippingMethodDB($orderId);
    }

    return $order;
}

/**
 * Get order by order number
 */
function getOrderByNumberDB($orderNumber)
{
    $sql = "SELECT * FROM sales_order WHERE order_number = :number";
    $order = fetchOne($sql, [':number' => $orderNumber]);

    if ($order) {
        $order['items'] = getOrderItemsDB($order['order_id']);
        $order['address'] = getOrderAddressDB($order['order_id']);
        $order['billing'] = getOrderBillingDB($order['order_id']);
        $order['shipping'] = getOrderShippingMethodDB($order['order_id']);
    }

    return $order;
}

/**
 * Get user orders
 */
function getUserOrdersDB($userId)
{
    $sql = "SELECT * FROM sales_order WHERE user_id = :user_id AND status != 'cart' ORDER BY created_at DESC";
    $orders = fetchAll($sql, [':user_id' => $userId]);

    foreach ($orders as &$order) {
        $order['items'] = getOrderItemsDB($order['order_id']);

        // Fetch billing info for coupon code
        $billing = getOrderBillingDB($order['order_id']);
        $order['coupon_code'] = $billing['coupon_code'] ?? null;
    }

    return $orders;
}

/**
 * Get order items
 */
function getOrderItemsDB($orderId)
{
    $sql = "SELECT * FROM sales_order_product WHERE order_id = :order_id";
    $items = fetchAll($sql, [':order_id' => $orderId]);

    foreach ($items as &$item) {
        if ($item['variant_data']) {
            $item['variant'] = json_decode($item['variant_data'], true);
        }
    }

    return $items;
}

/**
 * Get order address
 */
function getOrderAddressDB($orderId)
{
    $sql = "SELECT * FROM sales_order_address WHERE order_id = :order_id LIMIT 1";
    return fetchOne($sql, [':order_id' => $orderId]);
}

/**
 * Get order billing
 */
function getOrderBillingDB($orderId)
{
    $sql = "SELECT * FROM sales_order_billing WHERE order_id = :order_id LIMIT 1";
    return fetchOne($sql, [':order_id' => $orderId]);
}

/**
 * Get order shipping method
 */
function getOrderShippingMethodDB($orderId)
{
    $sql = "SELECT * FROM sales_order_shipping_method WHERE order_id = :order_id LIMIT 1";
    return fetchOne($sql, [':order_id' => $orderId]);
}

/**
 * Update order status
 */
function updateOrderStatusDB($orderId, $status)
{
    dbUpdate(
        'sales_order',
        ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')],
        'order_id = :id',
        [':id' => $orderId]
    );
    return true;
}

/**
 * Get active cart order for user
 */
function getActiveCartOrderDB($userId)
{
    $sql = "SELECT * FROM sales_order WHERE user_id = :user_id AND status = 'cart' LIMIT 1";
    return fetchOne($sql, [':user_id' => $userId]);
}

/**
 * Get or create active cart order for user
 */
function getOrCreateActiveCartOrderDB($userId)
{
    $order = getActiveCartOrderDB($userId);
    if (!$order) {
        $order = createOrderDB([
            'user_id' => $userId,
            'subtotal' => 0,
            'shipping_cost' => 0,
            'tax' => 0,
            'discount_amount' => 0,
            'final_amount' => 0,
            'status' => 'cart'
        ]);
    }
    return $order;
}

/**
 * Add or update item in an order-based cart
 */
function addItemToOrderCartDB($orderId, $productId, $quantity, $variant = [])
{
    $variantJson = json_encode($variant);

    // Check if item exists
    $sql = "SELECT * FROM sales_order_product 
            WHERE order_id = :order_id AND product_id = :product_id AND variant_data = :variant";
    $existing = fetchOne($sql, [
        ':order_id' => $orderId,
        ':product_id' => $productId,
        ':variant' => $variantJson
    ]);

    require_once __DIR__ . '/products.php';
    $product = getProductByIdFromDB($productId);

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $newSubtotal = $newQty * $existing['unit_price'];
        dbUpdate('sales_order_product', [
            'quantity' => $newQty,
            'subtotal' => $newSubtotal,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', [':id' => $existing['id']]);
    } else {
        addOrderItemDB($orderId, [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'quantity' => $quantity,
            'unit_price' => $product['price'],
            'variant' => $variant,
            'subtotal' => $quantity * $product['price']
        ]);
    }

    updateOrderTotalsDB($orderId);
}

/**
 * Update item quantity in an order-based cart
 */
function updateOrderCartItemDB($orderId, $productId, $quantity, $variant = [])
{
    $variantJson = json_encode($variant);

    if ($quantity <= 0) {
        dbDelete(
            'sales_order_product',
            'order_id = :order_id AND product_id = :product_id AND variant_data = :variant',
            [':order_id' => $orderId, ':product_id' => $productId, ':variant' => $variantJson]
        );
    } else {
        $sql = "SELECT unit_price FROM sales_order_product 
                WHERE order_id = :order_id AND product_id = :product_id AND variant_data = :variant";
        $item = fetchOne($sql, [':order_id' => $orderId, ':product_id' => $productId, ':variant' => $variantJson]);

        if ($item) {
            dbUpdate(
                'sales_order_product',
                [
                    'quantity' => $quantity,
                    'subtotal' => $quantity * $item['unit_price'],
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'order_id = :order_id AND product_id = :product_id AND variant_data = :variant',
                [':order_id' => $orderId, ':product_id' => $productId, ':variant' => $variantJson]
            );
        }
    }
    updateOrderTotalsDB($orderId);
}

/**
 * Remove item from an order-based cart
 */
function removeOrderCartItemDB($orderId, $productId, $variant = [])
{
    $variantJson = json_encode($variant);
    dbDelete(
        'sales_order_product',
        'order_id = :order_id AND product_id = :product_id AND variant_data = :variant',
        [':order_id' => $orderId, ':product_id' => $productId, ':variant' => $variantJson]
    );
    updateOrderTotalsDB($orderId);
}

/**
 * Update order coupon info
 */
function updateOrderCartCouponDB($orderId, $couponCode, $discountAmount)
{
    // Update discount in main order table
    dbUpdate('sales_order', [
        'discount_amount' => $discountAmount,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'order_id = :id', [':id' => $orderId]);

    // Update coupon in billing table (ensure row exists first)
    $sql = "SELECT id FROM sales_order_billing WHERE order_id = :id LIMIT 1";
    $billing = fetchOne($sql, [':id' => $orderId]);

    if ($billing) {
        dbUpdate('sales_order_billing', [
            'coupon_code' => $couponCode,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'order_id = :id', [':id' => $orderId]);
    } else {
        addOrderBillingDB($orderId, [
            'payment_method' => 'TBD',
            'coupon_code' => $couponCode
        ]);
    }
}

/**
 * Recalculate and update order totals
 */
function updateOrderTotalsDB($orderId)
{
    $sql = "SELECT SUM(subtotal) as total FROM sales_order_product WHERE order_id = :id";
    $result = fetchOne($sql, [':id' => $orderId]);
    $subtotal = $result['total'] ?? 0;

    // Fetch existing discount if any
    $sql = "SELECT discount_amount FROM sales_order WHERE order_id = :id";
    $order = fetchOne($sql, [':id' => $orderId]);
    $discount = $order['discount_amount'] ?? 0;

    $finalAmount = $subtotal - $discount;
    if ($finalAmount < 0)
        $finalAmount = 0;

    // Simple logic: final = subtotal - discount for cart state (tax/shipping calculated at checkout)
    dbUpdate('sales_order', [
        'subtotal' => $subtotal,
        'final_amount' => $finalAmount,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'order_id = :id', [':id' => $orderId]);
}

/**
 * Get order chart data (aggregated by date)
 */
function getUserOrderChartDataDB($userId)
{
    // PostgreSQL uses CAST(x AS DATE) or x::DATE
    $sql = "SELECT CAST(created_at AS DATE) as date, SUM(final_amount) as total 
            FROM sales_order 
            WHERE user_id = :user_id AND status NOT IN ('cancelled', 'cart') 
            GROUP BY CAST(created_at AS DATE) 
            ORDER BY date ASC";
    return fetchAll($sql, [':user_id' => $userId]);
}
?>