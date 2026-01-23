<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Basic validation
    if ($password !== $confirmPassword) {
        setFlashMessage('error', 'Passwords do not match.');
        header('Location: signup.php');
        exit;
    }
    
    // Register user
    $result = registerUser($firstName, $lastName, $email, $password);
    
    if ($result['success']) {
        // Log in the new user
        loginUser($result['user_id'], $result['name'], $email);
        setFlashMessage('success', 'Account created successfully! Welcome to EasyCart.');
        header('Location: index.php');
        exit;
    } else {
        setFlashMessage('error', $result['message']);
        header('Location: signup.php');
        exit;
    }
} else {
    header('Location: signup.php');
    exit;
}
?>
