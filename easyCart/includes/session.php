<?php
/**
 * Session Configuration - EasyCart Phase 6
 * Handles cart and user session management with database
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database helpers
require_once __DIR__ . '/db.php';

// Include discount helpers
require_once __DIR__ . '/discount-helpers.php';

// Include coupon helpers
require_once __DIR__ . '/coupon-helpers.php';

// Include shipping type helpers
require_once __DIR__ . '/shipping-type-helpers.php';

// Initialize session type tracking
if (!isset($_SESSION['session_type'])) {
    $_SESSION['session_type'] = 'guest';
    $_SESSION['guest_id'] = 'guest_' . session_id();
}

// Initialize guest cart if not exists
if (!isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
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
    $_SESSION['selected_shipping_method'] = null; // Will be set based on cart contents
}

// Initialize session type tracking
if (!isset($_SESSION['session_type'])) {
    $_SESSION['session_type'] = 'guest';
    $_SESSION['guest_id'] = 'guest_' . session_id();
}

// Initialize guest cart if not exists
if (!isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
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
    $_SESSION['selected_shipping_method'] = null; // Will be set based on cart contents
}

// Helper Functions for Session Management

/**
 * Initialize a new guest session
 */
function initGuestSession() {
    $_SESSION['session_type'] = 'guest';
    $_SESSION['guest_id'] = 'guest_' . session_id();
    $_SESSION['guest_cart'] = [];
}

/**
 * Get the current active cart based on session type
 */
function getCurrentCart() {
    if ($_SESSION['session_type'] === 'user' && isset($_SESSION['user']['user_id'])) {
        $userId = $_SESSION['user']['user_id'];
        return getUserCart($userId);
    } else {
        return $_SESSION['guest_cart'];
    }
}

/**
 * Set the current active cart
 */
function setCurrentCart($cart) {
    if ($_SESSION['session_type'] === 'user' && isset($_SESSION['user']['user_id'])) {
        $userId = $_SESSION['user']['user_id'];
        saveUserCart($userId, $cart);
    } else {
        $_SESSION['guest_cart'] = $cart;
    }
}

/**
 * Load user cart from session
 * Note: For Phase 6, we keep cart in session for simplicity
 * Cart will be persisted to database on order placement
 */
function getUserCart($userId) {
    if (!isset($_SESSION['user_carts'])) {
        $_SESSION['user_carts'] = [];
    }
    if (isset($_SESSION['user_carts'][$userId])) {
        return $_SESSION['user_carts'][$userId];
    }
    return [];
}

/**
 * Save user cart to session
 */
function saveUserCart($userId, $cart) {
    if (!isset($_SESSION['user_carts'])) {
        $_SESSION['user_carts'] = [];
    }
    $_SESSION['user_carts'][$userId] = $cart;
}

/**
 * Merge guest cart into user cart when logging in
 */
function mergeGuestCartWithUser($userId) {
    $guestCart = $_SESSION['guest_cart'];
    $userCart = getUserCart($userId);
    
    // Merge guest cart items into user cart
    foreach ($guestCart as $key => $item) {
        if (isset($userCart[$key])) {
            // Item already exists, add quantities
            $userCart[$key]['quantity'] += $item['quantity'];
        } else {
            // New item, add to user cart
            $userCart[$key] = $item;
        }
    }
    
    // Save merged cart
    saveUserCart($userId, $userCart);
    
    // Clear guest cart
    $_SESSION['guest_cart'] = [];
    
    return $userCart;
}


// Cart Functions



function addToCart($productId, $quantity = 1, $variant = []) {
    $cart = getCurrentCart();
    $cartItemKey = $productId . '_' . md5(serialize($variant));
    
    if (isset($cart[$cartItemKey])) {
        $cart[$cartItemKey]['quantity'] += $quantity;
    } else {
        $cart[$cartItemKey] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant' => $variant,
            'added_at' => time()
        ];
    }
    
    setCurrentCart($cart);
    return true;
}

function updateCartQuantity($cartItemKey, $quantity) {
    $cart = getCurrentCart();
    
    if (isset($cart[$cartItemKey])) {
        if ($quantity <= 0) {
            unset($cart[$cartItemKey]);
        } else {
            $cart[$cartItemKey]['quantity'] = $quantity;
        }
        setCurrentCart($cart);
        return true;
    }
    return false;
}

function removeFromCart($cartItemKey) {
    $cart = getCurrentCart();
    
    if (isset($cart[$cartItemKey])) {
        unset($cart[$cartItemKey]);
        setCurrentCart($cart);
        return true;
    }
    return false;
}

function clearCart() {
    setCurrentCart([]);
    return true;
}

function getCartItems() {
    return getCurrentCart();
}

function getCartCount() {
    $cart = getCurrentCart();
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function getCartTotal() {
    global $products;
    $cart = getCurrentCart();
    $total = 0;
    
    foreach ($cart as $item) {
        $product = getProductById($item['product_id']);
        if ($product) {
            // Calculate with first-unit discount
            $discountInfo = calculateItemTotalWithDiscount($product['price'], $item['quantity']);
            $total += $discountInfo['total'];
        }
    }
    
    return $total;
}

function getCartSubtotal() {
    return getCartTotal();
}

function getCartItemsWithDetails() {
    global $products;
    $cart = getCurrentCart();
    $cartDetails = [];
    
    foreach ($cart as $key => $item) {
        $product = getProductById($item['product_id']);
        if ($product) {
            // Calculate first-unit discount
            $discountInfo = calculateItemTotalWithDiscount($product['price'], $item['quantity']);
            
            $cartDetails[$key] = [
                'product' => $product,
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
    }
    
    return $cartDetails;
}


// User Session Functions

function isLoggedIn() {
    return isset($_SESSION['user']['logged_in']) && $_SESSION['user']['logged_in'] === true;
}

function getUserData() {
    return $_SESSION['user'];
}

function loginUser($userId, $name, $email) {
    // Merge guest cart into user cart before logging in
    mergeGuestCartWithUser($userId);
    
    // Set user session
    $_SESSION['user'] = [
        'logged_in' => true,
        'user_id' => $userId,
        'name' => $name,
        'email' => $email
    ];
    
    // Switch to user session type
    $_SESSION['session_type'] = 'user';
    
    return true;
}

function logoutUser() {
    // User cart is already saved in carts_db, no need to do anything
    
    // Clear user session
    $_SESSION['user'] = [
        'logged_in' => false,
        'user_id' => null,
        'name' => null,
        'email' => null
    ];
    
    // Initialize new guest session
    initGuestSession();
    
    return true;
}

// User Registration Function
function registerUser($firstName, $lastName, $email, $password) {
    // Check if user already exists
    $existingUser = fetchOne("SELECT user_id FROM users WHERE email = :email", ['email' => $email]);
    if ($existingUser) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Create new user
    $fullName = $firstName . ' ' . $lastName;
    
    $userId = dbInsert('users', [
        'email' => $email,
        'password' => $password,
        'name' => $fullName
    ]);
    
    if ($userId) {
        return ['success' => true, 'user_id' => $userId, 'name' => $fullName];
    }
    
    return ['success' => false, 'message' => 'Failed to create user'];
}

// User Login Verification Function
function verifyUserLogin($email, $password) {
    $user = fetchOne(
        "SELECT user_id, name, email FROM users WHERE email = :email AND password = :password",
        ['email' => $email, 'password' => $password]
    );
    
    if ($user) {
        return [
            'success' => true, 
            'user_id' => $user['user_id'], 
            'name' => $user['name']
        ];
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

// Flash Messages

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function hasFlashMessage() {
    return isset($_SESSION['flash']);
}

// Shipping Method Session Functions

/**
 * Get the currently selected shipping method
 * Returns null if not set or auto-determines based on cart
 */
function getSelectedShippingMethod() {
    return $_SESSION['selected_shipping_method'] ?? null;
}

/**
 * Set the selected shipping method
 * Validates that the method is available for current cart
 */
function setSelectedShippingMethod($method) {
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
 * If no method is selected or selected method is invalid, auto-select default
 */
function getOrSetDefaultShippingMethod() {
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
?>