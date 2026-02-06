<?php
// Orders Controller
// This file handles the orders page logic and includes the view

$pageTitle = "My Orders";
require_once 'includes/auth-middleware.php'; // Require login to access orders
require_once 'includes/orders.php';

// Get logged-in user's ID
$userId = isLoggedIn() ? $_SESSION['user']['user_id'] : 0;
$userOrders = getUserOrders($userId);
$stats = getOrderStats($userId);

// Include the view
require_once 'views/orders.view.php';