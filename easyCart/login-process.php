<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Demo login (In production, verify against database)
    if ($email === 'demo@easycart.com' && $password === 'demo123') {
        loginUser(1, 'Demo User', $email);
        setFlashMessage('success', 'Welcome back! You are now logged in.');
        header('Location: index.php');
        exit;
    } else {
        setFlashMessage('error', 'Invalid email or password.');
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
