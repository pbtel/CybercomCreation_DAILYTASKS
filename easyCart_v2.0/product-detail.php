<?php
// Product Detail Controller
// This file handles the product detail page logic and includes the view

require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/categories.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($productId);

// If product not found, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'];
$category = getCategoryById($product['category']);

// Include the view
require_once 'views/product-detail.view.php';