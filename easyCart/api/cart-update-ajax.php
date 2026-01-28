<?php
/**
 * AJAX Endpoint - Update Cart Quantity
 * Handles updating cart item quantity via AJAX
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
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate cart key
if (empty($cartKey)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid cart item'
    ]);
    exit;
}

// Validate quantity
if ($quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quantity must be at least 1'
    ]);
    exit;
}

// Get cart items to validate stock
$cart = getCurrentCart();
if (!isset($cart[$cartKey])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item not found'
    ]);
    exit;
}

// Check stock availability
$productId = $cart[$cartKey]['product_id'];
$product = getProductById($productId);

if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}

if ($quantity > $product['stock']) {
    echo json_encode([
        'success' => false,
        'message' => 'Requested quantity exceeds available stock (' . $product['stock'] . ' available)'
    ]);
    exit;
}

// Update cart quantity
try {
    updateCartQuantity($cartKey, $quantity);
    
    // Get updated cart data
    $cartItems = getCartItemsWithDetails();
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    
    // Calculate item subtotal
    $itemSubtotal = $product['price'] * $quantity;
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully!',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'item_subtotal' => $itemSubtotal,
        'quantity' => $quantity
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update cart'
    ]);
}
?>
