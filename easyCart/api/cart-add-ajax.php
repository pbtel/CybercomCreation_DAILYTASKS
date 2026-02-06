<?php
/**
 * AJAX Endpoint - Add to Cart
 * Handles adding products to cart via AJAX
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
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Get variant data if any
$variant = [];
if (isset($_POST['variant_color'])) {
    $variant['color'] = $_POST['variant_color'];
}
if (isset($_POST['variant_storage'])) {
    $variant['storage'] = $_POST['variant_storage'];
}
if (isset($_POST['variant_size'])) {
    $variant['size'] = $_POST['variant_size'];
}

// Validate product exists
$product = getProductById($productId);

if (!$product) {
    sendJson([
        'success' => false,
        'message' => 'Product not found'
    ]);
}

// Check stock availability
if ($product['stock'] <= 0) {
    sendJson([
        'success' => false,
        'message' => 'Product is out of stock'
    ]);
}

// Validate quantity
if ($quantity <= 0) {
    sendJson([
        'success' => false,
        'message' => 'Invalid quantity'
    ]);
}

if ($quantity > $product['stock']) {
    sendJson([
        'success' => false,
        'message' => 'Requested quantity exceeds available stock'
    ]);
}

// Add to cart
try {
    addToCart($productId, $quantity, $variant);

    // Get updated cart count
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    $product['stock'] -= $quantity; // Update stock for response

    sendJson([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'product_name' => $product['name']
    ]);
} catch (Exception $e) {
    sendJson([
        'success' => false,
        'message' => 'Failed to add product to cart'
    ]);
}