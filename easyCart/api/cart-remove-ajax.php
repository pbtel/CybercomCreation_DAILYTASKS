<?php
/**
 * AJAX Endpoint - Remove Cart Item
 * Handles removing items from cart via AJAX
 */

header('Content-Type: application/json');
require_once '../includes/session.php';
require_once '../includes/products.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';

// Validate cart key
if (empty($cartKey)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid cart item'
    ]);
    exit;
}

// Remove from cart
try {
    $removed = removeFromCart($cartKey);
    
    if (!$removed) {
        echo json_encode([
            'success' => false,
            'message' => 'Cart item not found'
        ]);
        exit;
    }
    
    // Get updated cart data
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    $cartItems = getCartItems();
    $isEmpty = empty($cartItems);
    
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'is_empty' => $isEmpty
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove item from cart'
    ]);
}
?>
