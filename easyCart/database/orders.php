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
        'cart_id' => $orderData['cart_id'] ?? null,
        'order_number' => $orderNumber,
        'subtotal' => $orderData['subtotal'],
        'shipping_cost' => $orderData['shipping_cost'],
        'tax' => $orderData['tax'],
        'discount_amount' => $orderData['discount_amount'] ?? 0,
        'final_amount' => $orderData['final_amount'],
        'status' => $orderData['status'] ?? 'pending',
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
        'tracking_number' => $shippingData['tracking_number'] ?? null,
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
    $sql = "SELECT * FROM sales_order WHERE user_id = :user_id ORDER BY created_at DESC";
    $orders = fetchAll($sql, [':user_id' => $userId]);

    foreach ($orders as &$order) {
        $order['items'] = getOrderItemsDB($order['order_id']);

        // Fetch shipping info for tracking number
        $shipping = getOrderShippingMethodDB($order['order_id']);
        $order['tracking_number'] = $shipping['tracking_number'] ?? null;

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
 * Get order chart data (aggregated by date)
 */
function getUserOrderChartDataDB($userId)
{
    // PostgreSQL uses CAST(x AS DATE) or x::DATE
    $sql = "SELECT CAST(created_at AS DATE) as date, SUM(final_amount) as total 
            FROM sales_order 
            WHERE user_id = :user_id AND status != 'cancelled' 
            GROUP BY CAST(created_at AS DATE) 
            ORDER BY date ASC";
    return fetchAll($sql, [':user_id' => $userId]);
}
?>