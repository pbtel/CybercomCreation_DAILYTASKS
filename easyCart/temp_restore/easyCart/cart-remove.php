<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';
    
    if ($cartKey) {
        removeFromCart($cartKey);
        setFlashMessage('success', 'Item removed from cart!');
    }
}

header('Location: cart.php');
exit;
?>
