<?php
// Homepage Controller
// This file handles the homepage logic and includes the view

require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/categories.php';
require_once 'includes/brands.php';

$pageTitle = "Home";

// Get featured products
$featuredProducts = getFeaturedProducts();
// Get all categories with product counts
$allCategories = getAllCategories();
// Get all brands
$allBrands = getAllBrands();

// Include the view
require_once 'views/index.view.php';