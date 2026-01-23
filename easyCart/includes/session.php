<?php
/**
 * Session Configuration - EasyCart Phase 2
 * Handles cart and user session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize user database (in session) if not exists
if (!isset($_SESSION['users_db'])) {
    $_SESSION['users_db'] = [
        [
            'user_id' => 1,
            'email' => 'demo@easycart.com',
            'password' => 'demo123',
            'name' => 'Demo User'
        ]
    ];
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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

// Cart Functions

function addToCart($productId, $quantity = 1, $variant = []) {
    $cartItemKey = $productId . '_' . md5(serialize($variant));
    
    if (isset($_SESSION['cart'][$cartItemKey])) {
        $_SESSION['cart'][$cartItemKey]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$cartItemKey] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant' => $variant,
            'added_at' => time()
        ];
    }
    return true;
}

function updateCartQuantity($cartItemKey, $quantity) {
    if (isset($_SESSION['cart'][$cartItemKey])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$cartItemKey]);
        } else {
            $_SESSION['cart'][$cartItemKey]['quantity'] = $quantity;
        }
        return true;
    }
    return false;
}

function removeFromCart($cartItemKey) {
    if (isset($_SESSION['cart'][$cartItemKey])) {
        unset($_SESSION['cart'][$cartItemKey]);
        return true;
    }
    return false;
}

function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}

function getCartItems() {
    return $_SESSION['cart'];
}

function getCartCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function getCartTotal() {
    global $products;
    $total = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $product = getProductById($item['product_id']);
        if ($product) {
            $total += $product['price'] * $item['quantity'];
        }
    }
    
    return $total;
}

function getCartSubtotal() {
    return getCartTotal();
}

function getCartItemsWithDetails() {
    global $products;
    $cartDetails = [];
    
    foreach ($_SESSION['cart'] as $key => $item) {
        $product = getProductById($item['product_id']);
        if ($product) {
            $cartDetails[$key] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'variant' => $item['variant'],
                'subtotal' => $product['price'] * $item['quantity']
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
    $_SESSION['user'] = [
        'logged_in' => true,
        'user_id' => $userId,
        'name' => $name,
        'email' => $email
    ];
    return true;
}

function logoutUser() {
    $_SESSION['user'] = [
        'logged_in' => false,
        'user_id' => null,
        'name' => null,
        'email' => null
    ];
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
    $userId = max(array_column($_SESSION['users_db'], 'user_id')) + 1;
    $fullName = $firstName . ' ' . $lastName;
    
    $_SESSION['users_db'][] = [
        'user_id' => $userId,
        'email' => $email,
        'password' => $password,
        'name' => $fullName
    ];
    
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