<?php
/**
 * AJAX Endpoint - Remove Cart Item
 * Handles removing items from cart via AJAX
 */

// Start output buffering to catch any stray output
ob_start();

require_once '../includes/session.php';
require_once '../includes/products.php';

// Helper to send clean JSON response
function sendJson($data)
{
    if (ob_get_length())
        ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Invalid request method']);
}

// Get POST data
$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';

// Validate cart key
if (empty($cartKey)) {
    sendJson([
        'success' => false,
        'message' => 'Invalid cart item'
    ]);
}

// Remove from cart
try {
    $removed = removeFromCart($cartKey);

    if (!$removed) {
        sendJson([
            'success' => false,
            'message' => 'Cart item not found'
        ]);
    }

    // Get updated cart data
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    $cartItems = getCartItems();
    $isEmpty = empty($cartItems);

    sendJson([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'is_empty' => $isEmpty
    ]);
} catch (Exception $e) {
    sendJson([
        'success' => false,
        'message' => 'Failed to remove item from cart'
    ]);
}