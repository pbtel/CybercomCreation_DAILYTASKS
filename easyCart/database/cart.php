<?php
/**
 * Cart Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/products.php';

/**
 * Get or create cart for user or session
 */
function getOrCreateCartDB($userId = null, $sessionId = null)
{
    $cart = null;

    // Try to find existing active cart
    if ($userId) {
        $sql = "SELECT * FROM sales_cart WHERE user_id = :user_id AND is_active = TRUE LIMIT 1";
        $cart = fetchOne($sql, [':user_id' => $userId]);
    } else if ($sessionId) {
        $sql = "SELECT * FROM sales_cart WHERE session_id = :session_id AND is_active = TRUE LIMIT 1";
        $cart = fetchOne($sql, [':session_id' => $sessionId]);
    }

    // Create new cart if not found
    if (!$cart) {
        $cartData = [
            'is_active' => 'true',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($userId) {
            $cartData['user_id'] = $userId;
        }
        if ($sessionId) {
            $cartData['session_id'] = $sessionId;
        }

        $cart = dbInsert('sales_cart', $cartData);
    }

    return $cart;
}

/**
 * Get cart items with product details
 */
function getCartItemsDB($cartId)
{
    $sql = "SELECT cp.*, p.name, p.price, p.original_price, p.discount_percent, 
                   p.stock, p.rating, p.reviews_count, p.shipping_type,
                   b.name as brand_name, b.brand_slug
            FROM sales_cart_product cp
            INNER JOIN catalog_product_entity p ON cp.product_id = p.entity_id
            LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            WHERE cp.cart_id = :cart_id
            ORDER BY cp.added_at DESC";

    $items = fetchAll($sql, [':cart_id' => $cartId]);

    // Enrich with product images and categories
    foreach ($items as &$item) {
        $item['images'] = getProductImages($item['product_id']);
        $item['categories'] = getProductCategories($item['product_id']);

        // Decode variant_data JSON
        if ($item['variant_data']) {
            $item['variant'] = json_decode($item['variant_data'], true);
        } else {
            $item['variant'] = [];
        }
    }

    return $items;
}

/**
 * Add item to cart
 */
function addCartItemDB($cartId, $productId, $quantity, $variant = [])
{
    // Check if item with same variant already exists
    $variantJson = json_encode($variant);

    $sql = "SELECT * FROM sales_cart_product 
            WHERE cart_id = :cart_id AND product_id = :product_id AND variant_data = :variant";

    $existing = fetchOne($sql, [
        ':cart_id' => $cartId,
        ':product_id' => $productId,
        ':variant' => $variantJson
    ]);

    if ($existing) {
        // Update quantity
        $newQuantity = $existing['quantity'] + $quantity;
        dbUpdate(
            'sales_cart_product',
            ['quantity' => $newQuantity],
            'id = :id',
            [':id' => $existing['id']]
        );
        return $existing['id'];
    } else {
        // Insert new item
        $item = dbInsert('sales_cart_product', [
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant_data' => $variantJson,
            'added_at' => date('Y-m-d H:i:s')
        ]);
        return $item['id'];
    }
}

/**
 * Update cart item quantity
 */
function updateCartItemDB($cartId, $productId, $quantity, $variant = [])
{
    $variantJson = json_encode($variant);

    if ($quantity <= 0) {
        // Remove item
        return removeCartItemDB($cartId, $productId, $variant);
    }

    dbUpdate(
        'sales_cart_product',
        ['quantity' => $quantity],
        'cart_id = :cart_id AND product_id = :product_id AND variant_data = :variant',
        [
            ':cart_id' => $cartId,
            ':product_id' => $productId,
            ':variant' => $variantJson
        ]
    );

    return true;
}

/**
 * Remove item from cart
 */
function removeCartItemDB($cartId, $productId, $variant = [])
{
    $variantJson = json_encode($variant);

    dbDelete(
        'sales_cart_product',
        'cart_id = :cart_id AND product_id = :product_id AND variant_data = :variant',
        [
            ':cart_id' => $cartId,
            ':product_id' => $productId,
            ':variant' => $variantJson
        ]
    );

    return true;
}

/**
 * Clear all items from cart
 */
function clearCartDB($cartId)
{
    dbDelete('sales_cart_product', 'cart_id = :cart_id', [':cart_id' => $cartId]);
    return true;
}

/**
 * Merge guest cart into user cart
 */
function mergeGuestCartToUserDB($guestCartId, $userCartId)
{
    // Get all items from guest cart
    $guestItems = getCartItemsDB($guestCartId);

    foreach ($guestItems as $item) {
        // Add to user cart (will merge if exists)
        addCartItemDB($userCartId, $item['product_id'], $item['quantity'], $item['variant']);
    }

    // Deactivate guest cart
    deactivateCartDB($guestCartId);

    return true;
}

/**
 * Deactivate cart
 */
function deactivateCartDB($cartId)
{
    dbUpdate(
        'sales_cart',
        ['is_active' => 'false', 'updated_at' => date('Y-m-d H:i:s')],
        'cart_id = :cart_id',
        [':cart_id' => $cartId]
    );
    return true;
}

/**
 * Get cart count
 */
function getCartCountDB($cartId)
{
    $sql = "SELECT COALESCE(SUM(quantity), 0) as total FROM sales_cart_product WHERE cart_id = :cart_id";
    $result = fetchOne($sql, [':cart_id' => $cartId]);
    return (int) $result['total'];
}

/**
 * Update cart timestamp
 */
function updateCartTimestampDB($cartId)
{
    dbUpdate(
        'sales_cart',
        ['updated_at' => date('Y-m-d H:i:s')],
        'cart_id = :cart_id',
        [':cart_id' => $cartId]
    );
}
