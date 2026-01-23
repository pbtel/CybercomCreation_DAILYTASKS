<?php
/**
 * Orders Data - EasyCart Phase 2
 * Load orders from database file
 */

// Define orders database file path
define('ORDERS_DB_FILE', __DIR__ . '/../data/orders_db.json');

// Load orders from file
function loadOrders() {
    if (file_exists(ORDERS_DB_FILE)) {
        $ordersData = file_get_contents(ORDERS_DB_FILE);
        $orders = json_decode($ordersData, true);
        return is_array($orders) ? $orders : [];
    }
    return [];
}

function getOrderById($orderId) {
    $orders = loadOrders();
    foreach ($orders as $order) {
        if ($order['order_id'] === $orderId) {
            return $order;
        }
    }
    return null;
}

function getUserOrders($userId) {
    $orders = loadOrders();
    return array_filter($orders, function($order) use ($userId) {
        return $order['user_id'] == $userId;
    });
}

function getOrdersByStatus($status) {
    $orders = loadOrders();
    return array_filter($orders, function($order) use ($status) {
        return $order['status'] === $status;
    });
}

function getOrderStats($userId) {
    $userOrders = getUserOrders($userId);
    
    $stats = [
        'total' => count($userOrders),
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0
    ];
    
    foreach ($userOrders as $order) {
        if (isset($stats[$order['status']])) {
            $stats[$order['status']]++;
        }
    }
    
    return $stats;
}
?>