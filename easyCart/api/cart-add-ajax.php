<?php
/**
 * AJAX Endpoint - Add to Cart
 * Handles adding products to cart via AJAX
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
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}

// Check stock availability
if ($product['stock'] <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Product is out of stock'
    ]);
    exit;
}

// Validate quantity
if ($quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid quantity'
    ]);
    exit;
}

if ($quantity > $product['stock']) {
    echo json_encode([
        'success' => false,
        'message' => 'Requested quantity exceeds available stock'
    ]);
    exit;
}

// Add to cart
try {
    addToCart($productId, $quantity, $variant);
    
    // Get updated cart count
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();
    $product['stock'] -= $quantity; // Update stock for response
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'product_name' => $product['name']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add product to cart'
    ]);
}
?>
