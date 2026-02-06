<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Verify user credentials
    $result = verifyUserLogin($email, $password);
    
    if ($result['success']) {
        loginUser($result['user_id'], $result['name'], $email);
        setFlashMessage('success', 'Welcome back! You are now logged in.');
        
        // Redirect to original page if specified
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header('Location: ' . $redirect);
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

