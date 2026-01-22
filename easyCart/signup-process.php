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
    
    // In production, save to database and hash password
    // For demo, we'll just log them in
    $fullName = $firstName . ' ' . $lastName;
    $newUserId = rand(1000, 9999); // In production, get from database
    
    loginUser($newUserId, $fullName, $email);
    setFlashMessage('success', 'Account created successfully! Welcome to EasyCart.');
    header('Location: index.php');
    exit;
} else {
    header('Location: signup.php');
    exit;
}
?>
