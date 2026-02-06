<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($cartKey && $quantity > 0) {
        updateCartQuantity($cartKey, $quantity);
        setFlashMessage('success', 'Cart updated successfully!');
    }
}

header('Location: cart.php');
exit;
?>
