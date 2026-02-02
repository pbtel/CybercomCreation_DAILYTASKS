<?php
/**
 * Orders Data - EasyCart Phase 6
 * Database-driven order management
 */

require_once __DIR__ . '/db.php';

/**
 * Save order to database
 * @param array $orderData Order data
 * @return int|false Order ID or false on failure
 */
function saveOrder($orderData) {
    try {
        beginTransaction();
        
        // Insert order
        $orderId = dbInsert('sales_order', [
            'user_id' => $orderData['user_id'] ?? null,
            'order_number' => $orderData['order_number'],
            'subtotal' => $orderData['subtotal'],
            'shipping_type' => $orderData['shipping_type'] ?? 'Standard',
            'shipping_cost' => $orderData['shipping_cost'] ?? 0,
            'tax' => $orderData['tax'] ?? 0,
            'discount' => $orderData['discount'] ?? 0,
            'final_amount' => $orderData['final_amount'],
            'status' => $orderData['status'] ?? 'pending'
        ]);
        
        if (!$orderId) {
            rollbackTransaction();
            return false;
        }
        
        // Insert order items
        if (isset($orderData['items']) && is_array($orderData['items'])) {
            foreach ($orderData['items'] as $item) {
                dbInsert('sales_order_product', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'variant_data' => json_encode($item['variant'] ?? [])
                ]);
            }
        }
        
        // Insert order address
        if (isset($orderData['address'])) {
            $addr = $orderData['address'];
            dbInsert('sales_order_address', [
                'order_id' => $orderId,
                'full_name' => $addr['full_name'] ?? '',
                'email' => $addr['email'] ?? '',
                'phone' => $addr['phone'] ?? '',
                'address_line1' => $addr['address_line1'] ?? '',
                'address_line2' => $addr['address_line2'] ?? '',
                'city' => $addr['city'] ?? '',
                'state' => $addr['state'] ?? '',
                'postal_code' => $addr['postal_code'] ?? '',
                'country' => $addr['country'] ?? 'India'
            ]);
        }
        
        commitTransaction();
        return $orderId;
        
    } catch (Exception $e) {
        rollbackTransaction();
        error_log("Failed to save order: " . $e->getMessage());
        return false;
    }
}

/**
 * Get order by ID
 * @param int $orderId
 * @return array|null
 */
function getOrderById($orderId) {
    $sql = "SELECT * FROM sales_order WHERE order_id = :order_id";
    $order = fetchOne($sql, ['order_id' => $orderId]);
    
    if ($order) {
        // Get order items
        $itemsSql = "SELECT op.*, pe.name as product_name
                     FROM sales_order_product op
                     JOIN catalog_product_entity pe ON op.product_id = pe.product_id
                     WHERE op.order_id = :order_id";
        $items = fetchAll($itemsSql, ['order_id' => $orderId]);
        
        // Decode variant data
        foreach ($items as &$item) {
            $item['variant'] = json_decode($item['variant_data'], true) ?? [];
        }
        
        $order['items'] = $items;
        
        // Get order address
        $addrSql = "SELECT * FROM sales_order_address WHERE order_id = :order_id";
        $address = fetchOne($addrSql, ['order_id' => $orderId]);
        $order['address'] = $address;
    }
    
    return $order;
}

/**
 * Get orders by user ID
 * @param int $userId
 * @return array
 */
function getUserOrders($userId) {
    $sql = "SELECT * FROM sales_order 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC";
    $orders = fetchAll($sql, ['user_id' => $userId]);
    
    // Get items for each order
    foreach ($orders as &$order) {
        $itemsSql = "SELECT op.*, pe.name as product_name
                     FROM sales_order_product op
                     JOIN catalog_product_entity pe ON op.product_id = pe.product_id
                     WHERE op.order_id = :order_id";
        $items = fetchAll($itemsSql, ['order_id' => $order['order_id']]);
        
        // Decode variant data
        foreach ($items as &$item) {
            $item['variant'] = json_decode($item['variant_data'], true) ?? [];
        }
        
        $order['items'] = $items;
    }
    
    return $orders ?? [];
}

/**
 * Get orders by status
 * @param string $status
 * @return array
 */
function getOrdersByStatus($status) {
    $sql = "SELECT * FROM sales_order 
            WHERE status = :status 
            ORDER BY created_at DESC";
    return fetchAll($sql, ['status' => $status]) ?? [];
}

/**
 * Get order statistics for a user
 * @param int $userId
 * @return array
 */
function getOrderStats($userId) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM sales_order
            WHERE user_id = :user_id";
    
    $stats = fetchOne($sql, ['user_id' => $userId]);
    
    return [
        'total' => (int)($stats['total'] ?? 0),
        'pending' => (int)($stats['pending'] ?? 0),
        'processing' => (int)($stats['processing'] ?? 0),
        'shipped' => (int)($stats['shipped'] ?? 0),
        'delivered' => (int)($stats['delivered'] ?? 0),
        'cancelled' => (int)($stats['cancelled'] ?? 0)
    ];
}

// Backward compatibility - load orders from database
function loadOrders() {
    $sql = "SELECT * FROM sales_order ORDER BY created_at DESC";
    return fetchAll($sql) ?? [];
}
?>