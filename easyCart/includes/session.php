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

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database helpers
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../database/cart.php';
require_once __DIR__ . '/../database/users.php';

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
        $cart = getOrCreateCartDB($userId, null);
    } else {
        $sessionId = session_id();
        $cart = getOrCreateCartDB(null, $sessionId);
    }

    $_SESSION['cart_id'] = $cart['cart_id'];
    return $cart;
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
    // deep copy to avoid modifying original
    $v = $variant;
    // Sort by key to ensure consistency
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
    $cartId = getCurrentCartId();
    addCartItemDB($cartId, $productId, $quantity, $variant);
    updateCartTimestampDB($cartId);
    return true;
}

/**
 * Update cart quantity
 */
function updateCartQuantity($cartItemKey, $quantity)
{
    // For backward compatibility, we need to parse the cart item key
    // Old format: productId_variantHash
    // We'll update based on cart ID and product info

    $cartId = getCurrentCartId();

    error_log("updateCartQuantity - Looking for key: $cartItemKey in cart ID: $cartId");

    // Get all cart items to find the matching one
    $items = getCartItemsDB($cartId);

    error_log("updateCartQuantity - Found " . count($items) . " items in cart");

    foreach ($items as $item) {
        $itemKey = getCartItemKey($item['product_id'], $item['variant']);
        error_log("updateCartQuantity - Comparing: '$itemKey' with '$cartItemKey'");

        if ($itemKey === $cartItemKey) {
            error_log("updateCartQuantity - Match found! Updating product_id: {$item['product_id']}, quantity: $quantity");
            updateCartItemDB($cartId, $item['product_id'], $quantity, $item['variant']);
            updateCartTimestampDB($cartId);
            return true;
        }
    }

    error_log("updateCartQuantity - No match found for key: $cartItemKey");
    return false;
}

/**
 * Remove from cart
 */
function removeFromCart($cartItemKey)
{
    $cartId = getCurrentCartId();

    // Get all cart items to find the matching one
    $items = getCartItemsDB($cartId);

    foreach ($items as $item) {
        $itemKey = getCartItemKey($item['product_id'], $item['variant']);
        if ($itemKey === $cartItemKey) {
            removeCartItemDB($cartId, $item['product_id'], $item['variant']);
            updateCartTimestampDB($cartId);
            return true;
        }
    }

    return false;
}

/**
 * Clear cart
 */
function clearCart()
{
    $cartId = getCurrentCartId();
    clearCartDB($cartId);
    updateCartTimestampDB($cartId);
    return true;
}

/**
 * Get current cart (for backward compatibility)
 */
function getCurrentCart()
{
    return getCartItemsWithDetails();
}

/**
 * Get cart items
 */
function getCartItems()
{
    $cartId = getCurrentCartId();
    return getCartItemsDB($cartId);
}

/**
 * Get cart count
 */
function getCartCount()
{
    $cartId = getCurrentCartId();
    return getCartCountDB($cartId);
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
    require_once __DIR__ . '/products.php';

    $cartId = getCurrentCartId();
    $items = getCartItemsDB($cartId);
    $cartDetails = [];

    foreach ($items as $item) {
        // Create cart item key for backward compatibility
        $key = getCartItemKey($item['product_id'], $item['variant']);

        // Calculate discount info
        $discountInfo = calculateItemTotalWithDiscount($item['price'], $item['quantity']);

        // Get primary image
        $image = '📦';
        if (!empty($item['images'])) {
            foreach ($item['images'] as $img) {
                if ($img['is_primary'] === 't' || $img['is_primary'] === true) {
                    $image = $img['image_emoji'];
                    break;
                }
            }
            if ($image === '📦' && !empty($item['images'])) {
                $image = $item['images'][0]['image_emoji'];
            }
        }

        // Get category
        $category = 'general';
        if (!empty($item['categories'])) {
            $category = $item['categories'][0]['category_slug'];
        }

        $cartDetails[$key] = [
            'product' => [
                'id' => $item['product_id'],
                'name' => $item['name'],
                'price' => (float) $item['price'],
                'original_price' => (float) $item['original_price'],
                'discount_percent' => (int) $item['discount_percent'],
                'rating' => (float) $item['rating'],
                'reviews_count' => (int) $item['reviews_count'],
                'stock' => (int) $item['stock'],
                'shipping_type' => $item['shipping_type'],
                'brand' => $item['brand_name'] ?? '',
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
    // Get guest cart ID before logging in
    $guestCartId = isset($_SESSION['cart_id']) ? $_SESSION['cart_id'] : null;

    // Set user session
    $_SESSION['user'] = [
        'logged_in' => true,
        'user_id' => $userId,
        'name' => $name,
        'email' => $email
    ];

    // Get or create user cart
    $userCart = getOrCreateCartDB($userId, null);

    // Merge guest cart into user cart if guest cart exists
    if ($guestCartId) {
        mergeGuestCartToUserDB($guestCartId, $userCart['cart_id']);
    }

    // Update session cart ID
    $_SESSION['cart_id'] = $userCart['cart_id'];

    return true;
}

/**
 * Logout user
 */
function logoutUser()
{
    // Clear user session
    $_SESSION['user'] = [
        'logged_in' => false,
        'user_id' => null,
        'name' => null,
        'email' => null
    ];

    // Create new guest cart
    $_SESSION['cart_id'] = null;
    getOrCreateSessionCart();

    return true;
}

/**
 * Register user
 */
function registerUser($firstName, $lastName, $email, $password)
{
    // Check if user already exists
    $existing = getUserByEmailDB($email);
    if ($existing) {
        return ['success' => false, 'message' => 'Email already registered'];
    }

    // Create new user
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

    // Calculate subtotal after coupon discount
    $appliedCoupon = getAppliedCoupon();
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($subtotal);
        $subtotal = $subtotal - $couponDiscount;
    }

    // Validate method is available
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

    // Calculate subtotal after coupon discount
    $appliedCoupon = getAppliedCoupon();
    if ($appliedCoupon) {
        $couponDiscount = calculateCouponDiscount($subtotal);
        $subtotal = $subtotal - $couponDiscount;
    }

    $currentMethod = getSelectedShippingMethod();

    // If current method is valid, return it
    if ($currentMethod && isShippingMethodAvailable($currentMethod, $cartItems, $subtotal)) {
        return $currentMethod;
    }

    // Otherwise, auto-select default and save it
    $defaultMethod = getDefaultShippingMethod($cartItems, $subtotal);
    $_SESSION['selected_shipping_method'] = $defaultMethod;
    return $defaultMethod;
}
