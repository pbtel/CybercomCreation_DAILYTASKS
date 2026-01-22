<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    // In production, this would:
    // 1. Validate all form data
    // 2. Save order to database
    // 3. Clear cart
    // 4. Send confirmation email
    // 5. Redirect to order confirmation page
    
    // For demo, we'll just clear cart and show success message
    $orderNumber = 'ORD-' . date('Y') . '-' . rand(1000, 9999);
    clearCart();
    setFlashMessage('success', 'Order placed successfully! Your order number is ' . $orderNumber);
    header('Location: orders.php');
    exit;
} else {
    header('Location: cart.php');
    exit;
}
?>
