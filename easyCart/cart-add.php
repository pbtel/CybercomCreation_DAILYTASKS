<?php
require_once 'includes/session.php';
require_once 'includes/products.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    if ($product && $product['stock'] > 0) {
        // Add to cart
        addToCart($productId, $quantity, $variant);
        setFlashMessage('success', 'Product added to cart successfully!');
    } else {
        setFlashMessage('error', 'Product not available or out of stock.');
    }
}

// Redirect back to previous page or products page
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
header('Location: ' . $redirect);
exit;
?>
