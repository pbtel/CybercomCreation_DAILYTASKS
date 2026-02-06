<?php
/**
 * Orders Data - EasyCart Phase 6
 * Database-backed order management
 */

// Include database order functions
require_once __DIR__ . '/../database/orders.php';

/**
 * Load orders from database
 */
function loadOrders()
{
    // Get all orders - for admin view
    // In production, this should be paginated
    $sql = "SELECT * FROM sales_order ORDER BY created_at DESC LIMIT 100";
    return fetchAll($sql);
}

/**
 * Get order by ID
 */
function getOrderById($orderId)
{
    return getOrderByIdDB($orderId);
}

/**
 * Get user orders
 */
function getUserOrders($userId)
{
    return getUserOrdersDB($userId);
}

/**
 * Get orders by status
 */
function getOrdersByStatus($status)
{
    $sql = "SELECT * FROM sales_order WHERE status = :status ORDER BY created_at DESC";
    return fetchAll($sql, [':status' => $status]);
}

/**
 * Get order stats for a user
 */
function getOrderStats($userId)
{
    $userOrders = getUserOrders($userId);

    $stats = [
        'total' => count($userOrders),
        'total_spent' => 0,
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
        // Only count valid orders towards total spent (exclude cancelled if desired, but user asked for "Total amount spent", usually implies net or gross. Let's exclude cancelled for accuracy)
        if ($order['status'] !== 'cancelled') {
            $stats['total_spent'] += $order['final_amount'];
        }
    }

    return $stats;
}
?>