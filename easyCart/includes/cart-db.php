<?php
/**
 * Cart Database Helpers - EasyCart
 * Dedicated functions for sales_cart tables
 */

require_once __DIR__ . '/db.php';

/**
 * Get or create a cart in the database (Guest only)
 * @param string|null $sessionId
 * @return int|false Cart ID
 */
function getOrCreateDbCart($sessionId = null) {
    if (!$sessionId) return false;

    // Try to find an active cart for this session
    $sql = "SELECT cart_id FROM sales_cart WHERE session_id = :session_id AND is_active = TRUE ORDER BY updated_at DESC LIMIT 1";
    $cart = fetchOne($sql, ['session_id' => $sessionId]);

    if ($cart) {
        // Update updated_at
        dbUpdate('sales_cart', ['updated_at' => date('Y-m-d H:i:s')], ['cart_id' => $cart['cart_id']]);
        return $cart['cart_id'];
    }

    // Create new cart
    return dbInsert('sales_cart', [
        'session_id' => $sessionId,
        'is_active' => 1
    ]);
}

/**
 * Synchronize session cart to database
 * @param int $cartId
 * @param array $sessionCart
 */
function syncSessionCartToDb($cartId, $sessionCart) {
    try {
        beginTransaction();

        // Remove existing items to ensure clean sync
        // Alternatively, we could update/insert, but clean sync is easier for now
        dbDelete('sales_cart_product', ['cart_id' => $cartId]);

        foreach ($sessionCart as $item) {
            dbInsert('sales_cart_product', [
                'cart_id' => $cartId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ]);
        }

        commitTransaction();
    } catch (Exception $e) {
        rollbackTransaction();
        error_log("Failed to sync cart to DB: " . $e->getMessage());
    }
}

/**
 * Load cart from DB into session format
 * @param int $cartId
 * @return array
 */
function loadCartFromDb($cartId) {
    $sql = "SELECT cp.*, pe.name 
            FROM sales_cart_product cp
            JOIN catalog_product_entity pe ON cp.product_id = pe.product_id
            WHERE cp.cart_id = :cart_id";
    $items = fetchAll($sql, ['cart_id' => $cartId]);
    
    $cart = [];
    foreach ($items as $item) {
        $variant = []; // No variant data in new guest cart schema
        $key = $item['product_id'] . '_guest';
        $cart[$key] = [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'variant' => $variant,
            'added_at' => strtotime($item['added_at'] ?? 'now')
        ];
    }
    
    return $cart;
}

/**
 * Deactivate guest cart (usually after login or order)
 * @param string $sessionId
 */
function deactivateDbCart($sessionId) {
    dbUpdate('sales_cart', ['is_active' => 0], ['session_id' => $sessionId, 'is_active' => 1]);
}
