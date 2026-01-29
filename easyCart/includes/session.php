<?php
/**
 * Session Configuration - EasyCart Phase 2
 * Handles cart and user session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include discount helpers
require_once __DIR__ . '/discount-helpers.php';

// Include coupon helpers
require_once __DIR__ . '/coupon-helpers.php';

// Define JSON file paths
define('USERS_DB_FILE', __DIR__ . '/../data/users_db.json');
define('CARTS_DB_FILE', __DIR__ . '/../data/carts_db.json');

// Initialize user database (in session) if not exists
if (!isset($_SESSION['users_db'])) {
    // Try to load from JSON file
    if (file_exists(USERS_DB_FILE)) {
        $usersData = file_get_contents(USERS_DB_FILE);
        $_SESSION['users_db'] = json_decode($usersData, true) ?: [];
    } else {
        $_SESSION['users_db'] = [
            [
                'user_id' => 1,
                'email' => 'demo@easycart.com',
                'password' => 'demo123',
                'name' => 'Demo User'
            ]
        ];
    }
}

// Initialize cart database for persistent user carts
if (!isset($_SESSION['carts_db'])) {
    // Try to load from JSON file
    if (file_exists(CARTS_DB_FILE)) {
        $cartsData = file_get_contents(CARTS_DB_FILE);
        $_SESSION['carts_db'] = json_decode($cartsData, true) ?: [];
    } else {
        $_SESSION['carts_db'] = [];
    }
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
 * Load user cart from database
 */
function getUserCart($userId) {
    if (isset($_SESSION['carts_db'][$userId])) {
        return $_SESSION['carts_db'][$userId];
    }
    return [];
}

/**
 * Save user cart to database
 */
function saveUserCart($userId, $cart) {
    $_SESSION['carts_db'][$userId] = $cart;
    // Persist to JSON files
    persistToJSON();
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

/**
 * Persist data to JSON files
 */
function persistToJSON() {
    // Ensure data directory exists
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Save users database to JSON file
    $usersJson = json_encode($_SESSION['users_db'], JSON_PRETTY_PRINT);
    file_put_contents(USERS_DB_FILE, $usersJson);
    
    // Save carts database to JSON file
    $cartsJson = json_encode($_SESSION['carts_db'], JSON_PRETTY_PRINT);
    file_put_contents(CARTS_DB_FILE, $cartsJson);
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
    global $_SESSION;
    
    // Check if user already exists
    foreach ($_SESSION['users_db'] as $user) {
        if ($user['email'] === $email) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
    }
    
    // Create new user
    // Handle empty users_db array to avoid max() error
    if (empty($_SESSION['users_db'])) {
        $userId = 1;
    } else {
        $userId = max(array_column($_SESSION['users_db'], 'user_id')) + 1;
    }
    $fullName = $firstName . ' ' . $lastName;
    
    $_SESSION['users_db'][] = [
        'user_id' => $userId,
        'email' => $email,
        'password' => $password,
        'name' => $fullName
    ];
    
    // Initialize empty cart for new user
    $_SESSION['carts_db'][$userId] = [];
    
    // Persist to JSON files
    persistToJSON();
    
    return ['success' => true, 'user_id' => $userId, 'name' => $fullName];
}

// User Login Verification Function
function verifyUserLogin($email, $password) {
    global $_SESSION;
    
    foreach ($_SESSION['users_db'] as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            return ['success' => true, 'user_id' => $user['user_id'], 'name' => $user['name']];
        }
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
?>