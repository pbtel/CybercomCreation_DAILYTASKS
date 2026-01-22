<?php
/**
 * Session Configuration - EasyCart Phase 2
 * Handles cart and user session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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