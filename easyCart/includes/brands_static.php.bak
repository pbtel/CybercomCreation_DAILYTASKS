<?php
/**
 * Brands Data - EasyCart Phase 2
 */

$brands = [
    [
        'id' => 'technogear',
        'name' => 'TechnoGear',
        'logo' => '⚙️',
        'description' => 'Premium Technology Products'
    ],
    [
        'id' => 'audiomax',
        'name' => 'AudioMax',
        'logo' => '🎵',
        'description' => 'High-Quality Audio Equipment'
    ],
    [
        'id' => 'smartlife',
        'name' => 'SmartLife',
        'logo' => '🏠',
        'description' => 'Smart Home and Wearables'
    ],
    [
        'id' => 'fashionhub',
        'name' => 'FashionHub',
        'logo' => '👔',
        'description' => 'Trendy Fashion and Apparel'
    ],
    [
        'id' => 'sportspro',
        'name' => 'SportsPro',
        'logo' => '⚽',
        'description' => 'Professional Sports Equipment'
    ],
    [
        'id' => 'homeessentials',
        'name' => 'HomeEssentials',
        'logo' => '🛋️',
        'description' => 'Quality Home Products'
    ],
    [
        'id' => 'bookworld',
        'name' => 'BookWorld',
        'logo' => '📖',
        'description' => 'Books and Literature'
    ],
    [
        'id' => 'toyland',
        'name' => 'ToyLand',
        'logo' => '🎮',
        'description' => 'Fun Toys for All Ages'
    ]
];

function getBrandById($id) {
    global $brands;
    foreach ($brands as $brand) {
        if ($brand['id'] === $id) {
            return $brand;
        }
    }
    return null;
}

function getAllBrands() {
    global $brands;
    return $brands;
}

function getProductsByBrand($brandId) {
    global $products;
    return array_filter($products, function($product) use ($brandId) {
        return strtolower($product['brand']) === strtolower($brandId);
    });
}
?>