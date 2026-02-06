<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    clearCart();
    setFlashMessage('success', 'Cart cleared successfully!');
}

header('Location: cart.php');
exit;
?>
