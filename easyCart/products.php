<?php
// Products Controller
// This file handles the products page logic and includes the view

require_once 'includes/session.php';
require_once 'includes/products.php';
require_once 'includes/categories.php';
require_once 'includes/brands.php';

$pageTitle = "Products";

// Get filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$selectedBrand = isset($_GET['brand']) ? $_GET['brand'] : 'all';
$selectedPriceRange = isset($_GET['price']) ? $_GET['price'] : 'all';
$selectedRating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid';

// Get products based on filters
$displayProducts = $products;

// Apply category filter
if ($selectedCategory !== 'all') {
    $displayProducts = getProductsByCategory($selectedCategory);
}

// Apply brand filter
if ($selectedBrand !== 'all') {
    $displayProducts = array_filter($displayProducts, function ($product) use ($selectedBrand) {
        return strtolower($product['brand']) === strtolower($selectedBrand);
    });
}

// Apply price filter
if ($selectedPriceRange !== 'all') {
    switch ($selectedPriceRange) {
        case 'under5k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] < 5000;
            });
            break;
        case '5k-20k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] >= 5000 && $p['price'] <= 20000;
            });
            break;
        case 'above20k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] > 20000;
            });
            break;
    }
}

// Apply rating filter
if ($selectedRating > 0) {
    $displayProducts = array_filter($displayProducts, function ($p) use ($selectedRating) {
        return $p['rating'] >= $selectedRating;
    });
}

// Get all categories for filter
$allCategories = getAllCategories();
// Get all brands for filter
$allBrands = getAllBrands();

// Include the view
require_once 'views/products.view.php';