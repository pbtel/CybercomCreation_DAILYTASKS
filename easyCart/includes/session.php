<?php
/**
 * Session Configuration - EasyCart Phase 6
 * Database-backed cart and user session management
 */

// Set UTF-8 encoding for all pages
header('Content-Type: text/html; charset=UTF-8');

// Set PHP internal encoding to UTF-8
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
ini_set('default_charset', 'UTF-8');

// Define BASE_URL if not already defined (for legacy root scripts)
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptPath = dirname($scriptName);

    // If scriptPath is effectively root (windows \ or linux /), make it empty
    if ($scriptPath === '\\' || $scriptPath === '/') {
        $scriptPath = '';
    }

    $baseUrl = $protocol . '://' . $host . str_replace('\\', '/', $scriptPath);
    // Remove /public if it's at the end
    $baseUrl = preg_replace('/\/public$/', '', $baseUrl);
    define('BASE_URL', rtrim($baseUrl, '/'));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database helpers
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../database/cart.php';
require_once __DIR__ . '/../database/users.php';
require_once __DIR__ . '/../database/orders.php';

// Include discount helpers
require_once __DIR__ . '/discount-helpers.php';

// Include price formatting helpers
require_once __DIR__ . '/price-helpers.php';

// Include coupon helpers
require_once __DIR__ . '/coupon-helpers.php';

// Include shipping type helpers
require_once __DIR__ . '/shipping-type-helpers.php';

// Initialize session cart ID
if (!isset($_SESSION['cart_id'])) {
    $_SESSION['cart_id'] = null;
}

// Initialize user session data if not exists
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'logged_in' => false,
        'user_id' => null,
        'name' => null,
        'email' => null
    ];
}

// Initialize selected shipping method if not exists
if (!isset($_SESSION['selected_shipping_method'])) {
    $_SESSION['selected_shipping_method'] = null;
}

// Helper Functions for Session Management

/**
 * Get or create cart for current session
 */
function getOrCreateSessionCart()
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getOrCreateActiveCartOrderDB($userId);
        $_SESSION['cart_id'] = $order['order_id'];
        return ['cart_id' => $order['order_id'], 'is_order' => true];
    } else {
        $sessionId = session_id();
        $cart = getOrCreateCartDB(null, $sessionId);
        $_SESSION['cart_id'] = $cart['cart_id'];
        return ['cart_id' => $cart['cart_id'], 'is_order' => false];
    }
}

/**
 * Get current cart ID
 */
function getCurrentCartId()
{
    if (!isset($_SESSION['cart_id']) || $_SESSION['cart_id'] === null) {
        $cart = getOrCreateSessionCart();
        return $cart['cart_id'];
    }
    return $_SESSION['cart_id'];
}

/**
 * Generate consistent cart item key
 */
function getCartItemKey($productId, $variant)
{
    $v = $variant;
    if (is_array($v)) {
        ksort($v);
    }
    return $productId . '_' . md5(json_encode($v));
}

// Cart Functions

/**
 * Add to cart
 */
function addToCart($productId, $quantity = 1, $variant = [])
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getOrCreateActiveCartOrderDB($userId);
        addItemToOrderCartDB($order['order_id'], $productId, $quantity, $variant);
    } else {
        $cartId = getCurrentCartId();
        addCartItemDB($cartId, $productId, $quantity, $variant);
        updateCartTimestampDB($cartId);
    }
    return true;
}

/**
 * Update cart quantity
 */
function updateCartQuantity($cartItemKey, $quantity)
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getActiveCartOrderDB($userId);
        if (!$order)
            return false;

        $items = getOrderItemsDB($order['order_id']);
        foreach ($items as $item) {
            $itemKey = getCartItemKey($item['product_id'], $item['variant']);
            if ($itemKey === $cartItemKey) {
                updateOrderCartItemDB($order['order_id'], $item['product_id'], $quantity, $item['variant']);
                return true;
            }
        }
    } else {
        $cartId = getCurrentCartId();
        $items = getCartItemsDB($cartId);
        foreach ($items as $item) {
            $itemKey = getCartItemKey($item['product_id'], $item['variant']);
            if ($itemKey === $cartItemKey) {
                updateCartItemDB($cartId, $item['product_id'], $quantity, $item['variant']);
                updateCartTimestampDB($cartId);
                return true;
            }
        }
    }
    return false;
}

/**
 * Remove from cart
 */
function removeFromCart($cartItemKey)
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getActiveCartOrderDB($userId);
        if (!$order)
            return false;

        $items = getOrderItemsDB($order['order_id']);
        foreach ($items as $item) {
            $itemKey = getCartItemKey($item['product_id'], $item['variant']);
            if ($itemKey === $cartItemKey) {
                removeOrderCartItemDB($order['order_id'], $item['product_id'], $item['variant']);
                return true;
            }
        }
    } else {
        $cartId = getCurrentCartId();
        $items = getCartItemsDB($cartId);
        foreach ($items as $item) {
            $itemKey = getCartItemKey($item['product_id'], $item['variant']);
            if ($itemKey === $cartItemKey) {
                removeCartItemDB($cartId, $item['product_id'], $item['variant']);
                updateCartTimestampDB($cartId);
                return true;
            }
        }
    }
    return false;
}

/**
 * Clear cart
 */
function clearCart()
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getActiveCartOrderDB($userId);
        if ($order) {
            dbDelete('sales_order_product', 'order_id = :id', [':id' => $order['order_id']]);
            updateOrderTotalsDB($order['order_id']);
        }
    } else {
        $cartId = getCurrentCartId();
        clearCartDB($cartId);
        updateCartTimestampDB($cartId);
    }
    return true;
}

/**
 * Get current cart
 */
function getCurrentCart()
{
    return getCartItemsWithDetails();
}

/**
 * Get cart items (raw)
 */
function getCartItems()
{
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['user_id'];
        $order = getActiveCartOrderDB($userId);
        if (!$order)
            return [];
        return getOrderItemsDB($order['order_id']);
    } else {
        $cartId = getCurrentCartId();
        return getCartItemsDB($cartId);
    }
}

/**
 * Get cart count
 */
function getCartCount()
{
    $items = getCartItems();
    $count = 0;
    foreach ($items as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Get cart total
 */
function getCartTotal()
{
    $cart = getCartItemsWithDetails();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

/**
 * Get cart subtotal
 */
function getCartSubtotal()
{
    return getCartTotal();
}

/**
 * Get cart items with details
 */
function getCartItemsWithDetails()
{
    require_once __DIR__ . '/../database/products.php';

    $items = getCartItems();
    $cartDetails = [];

    foreach ($items as $item) {
        $key = getCartItemKey($item['product_id'], $item['variant']);
        $product = getProductByIdFromDB($item['product_id']);

        if (!$product)
            continue;

        // Calculate discount info
        $discountInfo = calculateItemTotalWithDiscount($product['price'], $item['quantity']);

        // Get primary image
        $image = '📦';
        if (!empty($product['images'])) {
            foreach ($product['images'] as $img) {
                if ($img['is_primary'] === 't' || $img['is_primary'] === true) {
                    $image = $img['image_emoji'];
                    break;
                }
            }
            if ($image === '📦' && !empty($product['images'])) {
                $image = $product['images'][0]['image_emoji'];
            }
        }

        // Get category
        $category = 'general';
        if (!empty($product['categories'])) {
            $category = $product['categories'][0]['category_slug'];
        }

        $cartDetails[$key] = [
            'product' => [
                'id' => $item['product_id'],
                'name' => $product['name'],
                'price' => (float) $product['price'],
                'original_price' => (float) $product['original_price'],
                'discount_percent' => (int) $product['discount_percent'],
                'rating' => (float) $product['rating'],
                'reviews_count' => (int) $product['reviews_count'],
                'stock' => (int) $product['stock'],
                'shipping_type' => $product['shipping_type'],
                'brand' => $product['brand_name'] ?? '',
                'category' => $category,
                'image' => $image
            ],
            'quantity' => $item['quantity'],
            'variant' => $item['variant'],
            'subtotal' => $discountInfo['total'],
            'discount_percent' => $discountInfo['discount_percent'],
            'unit_price_original' => $discountInfo['unit_price_original'],
            'unit_price_discounted' => $discountInfo['unit_price_discounted'],
            'first_unit_savings' => $discountInfo['first_unit_savings'],
            'total_savings' => $discountInfo['total_savings'],
            'full_price_total' => $discountInfo['full_price_total']
        ];
    }

    return $cartDetails;
}

// User Session Functions

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user']['logged_in']) && $_SESSION['user']['logged_in'] === true;
}

/**
 * Get user data
 */
function getUserData()
{
    return $_SESSION['user'];
}

/**
 * Login user
 */
function loginUser($userId, $name, $email)
{
    $guestCartId = isset($_SESSION['cart_id']) ? $_SESSION['cart_id'] : null;

    $_SESSION['user'] = [
        'logged_in' => true,
        'user_id' => $userId,
        'name' => $name,
        'email' => $email
    ];

    if ($guestCartId) {
        $orderId = mergeGuestCartToOrderDB($guestCartId, $userId);
        $_SESSION['cart_id'] = $orderId;
    } else {
        // Only get existing cart, do not create one yet
        $order = getActiveCartOrderDB($userId);
        $_SESSION['cart_id'] = $order ? $order['order_id'] : null;
    }

    return true;
}

/**
 * Logout user
 */
function logoutUser()
{
    $_SESSION['user'] = [
        'logged_in' => false,
        'user_id' => null,
        'name' => null,
        'email' => null
    ];

    $_SESSION['cart_id'] = null;
    getOrCreateSessionCart();

    return true;
}

/**
 * Register user
 */
function registerUser($firstName, $lastName, $email, $password)
{
    $existing = getUserByEmailDB($email);
    if ($existing) {
        return ['success' => false, 'message' => 'Email already registered'];
    }

    $fullName = $firstName . ' ' . $lastName;
    $user = createUserDB($email, $password, $fullName);

    return [
        'success' => true,
        'user_id' => $user['entity_id'],
        'name' => $fullName
    ];
}

/**
 * Verify user login
 */
function verifyUserLogin($email, $password)
{
    return verifyUserCredentialsDB($email, $password);
}

// Flash Messages

/**
 * Set flash message
 */
function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if flash message exists
 */
function hasFlashMessage()
{
    return isset($_SESSION['flash']);
}

// Shipping Method Session Functions

/**
 * Get the currently selected shipping method
 */
function getSelectedShippingMethod()
{
    return $_SESSION['selected_shipping_method'] ?? null;
}

/**
 * Set the selected shipping method
 */
function setSelectedShippingMethod($method)
{
    $cartItems = getCartItemsWithDetails();
    $subtotal = getCartSubtotal();

    $appliedCoupon = getAppliedCoupon();
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($subtotal);
        $subtotal = $subtotal - $couponDiscount;
    }

    if (isShippingMethodAvailable($method, $cartItems, $subtotal)) {
        $_SESSION['selected_shipping_method'] = $method;
        return true;
    }

    return false;
}

/**
 * Get or auto-determine shipping method based on cart
 */
function getOrSetDefaultShippingMethod()
{
    $cartItems = getCartItemsWithDetails();
    $subtotal = getCartSubtotal();

    $appliedCoupon = getAppliedCoupon();
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($subtotal);
        $subtotal = $subtotal - $couponDiscount;
    }

    $currentMethod = getSelectedShippingMethod();

    if ($currentMethod && isShippingMethodAvailable($currentMethod, $cartItems, $subtotal)) {
        return $currentMethod;
    }

    $defaultMethod = getDefaultShippingMethod($cartItems, $subtotal);
    $_SESSION['selected_shipping_method'] = $defaultMethod;
    return $defaultMethod;
}
