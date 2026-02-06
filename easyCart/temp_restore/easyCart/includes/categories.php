<?php
/**
 * Categories Data - EasyCart Phase 2
 */

$categories = [
    [
        'id' => 'electronics',
        'name' => 'Electronics',
        'icon' => '📱',
        'description' => 'Smartphones, Laptops, and Tech Gadgets',
        'product_count' => 0 // Will be calculated dynamically
    ],
    [
        'id' => 'fashion',
        'name' => 'Fashion',
        'icon' => '👕',
        'description' => 'Clothing, Footwear, and Accessories',
        'product_count' => 0
    ],
    [
        'id' => 'home',
        'name' => 'Home & Garden',
        'icon' => '🏠',
        'description' => 'Furniture, Decor, and Home Essentials',
        'product_count' => 0
    ],
    [
        'id' => 'sports',
        'name' => 'Sports',
        'icon' => '⚽',
        'description' => 'Sports Equipment and Fitness Gear',
        'product_count' => 0
    ],
    [
        'id' => 'books',
        'name' => 'Books',
        'icon' => '📚',
        'description' => 'Fiction, Non-Fiction, and Educational Books',
        'product_count' => 0
    ],
    [
        'id' => 'toys',
        'name' => 'Toys',
        'icon' => '🧸',
        'description' => 'Toys and Games for Kids',
        'product_count' => 0
    ]
];

function getCategoryById($id) {
    global $categories;
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            return $category;
        }
    }
    return null;
}

function getAllCategories() {
    global $categories, $products;
    // Calculate product count for each category
    $categoriesWithCount = $categories;
    foreach ($categoriesWithCount as &$category) {
        $count = 0;
        foreach ($products as $product) {
            if ($product['category'] === $category['id']) {
                $count++;
            }
        }
        $category['product_count'] = $count;
    }
    return $categoriesWithCount;
}
?>