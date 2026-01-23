<?php
/**
 * Products Data - EasyCart Phase 2
 * This file contains all product data as PHP arrays
 * Ready for database migration later
 */

$products = [
    // Electronics
    [
        'id' => 1,
        'name' => 'Smartphone X',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 36999,
        'original_price' => 46999,
        'discount_percent' => 21,
        'rating' => 4.8,
        'reviews_count' => 245,
        'stock' => 50,
        'image' => '📱',
        'description' => 'Experience the latest in smartphone technology with the Smartphone X. Features a stunning 6.5-inch OLED display, powerful octa-core processor, and advanced triple-camera system.',
        'specs' => [
            'Display' => '6.5" OLED, 2400 x 1080 pixels',
            'Processor' => 'Octa-core 2.8GHz',
            'RAM' => '8GB LPDDR5',
            'Storage' => '256GB UFS 3.1',
            'Battery' => '5000mAh with 65W Fast Charging',
            'Camera' => '48MP + 12MP + 8MP Triple Camera',
            'OS' => 'Android 14',
            'Weight' => '185g'
        ],
        'variants' => [
            'color' => ['Black', 'Silver', 'Gold'],
            'storage' => ['128GB', '256GB', '512GB']
        ],
        'featured' => true,
        'tags' => ['hot', 'bestseller']
    ],
    [
        'id' => 2,
        'name' => 'Laptop Pro',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 50,999,
        'original_price' => 65999,
        'discount_percent' => 23,
        'rating' => 4.5,
        'reviews_count' => 178,
        'stock' => 30,
        'image' => '💻',
        'description' => 'Professional-grade laptop with powerful performance for work and creativity. Features high-resolution display and all-day battery life.',
        'specs' => [
            'Display' => '14" FHD IPS, 1920 x 1080',
            'Processor' => 'Intel Core i7 12th Gen',
            'RAM' => '16GB DDR5',
            'Storage' => '512GB NVMe SSD',
            'Graphics' => 'Integrated Intel Iris Xe',
            'Battery' => '10 hours',
            'OS' => 'Windows 11 Pro',
            'Weight' => '1.4kg'
        ],
        'variants' => [
            'color' => ['Silver', 'Space Gray'],
            'storage' => ['512GB', '1TB']
        ],
        'featured' => true,
        'tags' => ['new']
    ],
    [
        'id' => 3,
        'name' => 'Wireless Headphones',
        'category' => 'electronics',
        'brand' => 'AudioMax',
        'price' => 1990,
        'original_price' => 2999,
        'discount_percent' => 34,
        'rating' => 5.0,
        'reviews_count' => 412,
        'stock' => 100,
        'image' => '🎧',
        'description' => 'Premium wireless headphones with active noise cancellation and exceptional sound quality.',
        'specs' => [
            'Type' => 'Over-ear, Wireless',
            'Connectivity' => 'Bluetooth 5.2',
            'Battery Life' => '30 hours',
            'Charging' => 'USB-C Fast Charging',
            'Features' => 'ANC, Ambient Mode',
            'Driver Size' => '40mm',
            'Weight' => '250g'
        ],
        'variants' => [
            'color' => ['Black', 'White', 'Blue']
        ],
        'featured' => true,
        'tags' => ['bestseller']
    ],
    [
        'id' => 4,
        'name' => 'Smart Watch',
        'category' => 'electronics',
        'brand' => 'SmartLife',
        'price' => 3000,
        'original_price' => 4500,
        'discount_percent' => 33,
        'rating' => 4.3,
        'reviews_count' => 156,
        'stock' => 75,
        'image' => '⌚',
        'description' => 'Advanced smartwatch with health tracking, fitness features, and smartphone notifications.',
        'specs' => [
            'Display' => '1.4" AMOLED',
            'Battery Life' => '7 days',
            'Water Resistance' => '5ATM',
            'Sensors' => 'Heart Rate, SpO2, GPS',
            'Compatibility' => 'iOS & Android',
            'Weight' => '45g'
        ],
        'variants' => [
            'color' => ['Black', 'Silver', 'Rose Gold'],
            'strap' => ['Sport', 'Leather', 'Metal']
        ],
        'featured' => true,
        'tags' => ['sale']
    ],
    [
        'id' => 5,
        'name' => 'Tablet Air',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 25999,
        'original_price' => 29999,
        'discount_percent' => 13,
        'rating' => 4.6,
        'reviews_count' => 98,
        'stock' => 40,
        'image' => '📱',
        'description' => 'Lightweight tablet perfect for entertainment and productivity on the go.',
        'specs' => [
            'Display' => '10.5" LCD, 2000 x 1200',
            'Processor' => 'Octa-core',
            'RAM' => '6GB',
            'Storage' => '128GB',
            'Battery' => '8000mAh',
            'OS' => 'Android 13'
        ],
        'variants' => [
            'color' => ['Silver', 'Gray'],
            'storage' => ['128GB', '256GB']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 6,
        'name' => 'Wireless Mouse',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 799,
        'original_price' => 1299,
        'discount_percent' => 38,
        'rating' => 4.4,
        'reviews_count' => 234,
        'stock' => 150,
        'image' => '🖱️',
        'description' => 'Ergonomic wireless mouse with precision tracking and long battery life.',
        'specs' => [
            'Type' => 'Wireless',
            'Connectivity' => 'Bluetooth & USB Receiver',
            'DPI' => '1600',
            'Battery Life' => '12 months',
            'Buttons' => '6'
        ],
        'variants' => [
            'color' => ['Black', 'White']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Fashion
    [
        'id' => 17,
        'name' => 'Classic Denim Jacket',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 2499,
        'original_price' => 3999,
        'discount_percent' => 38,
        'rating' => 4.7,
        'reviews_count' => 145,
        'stock' => 60,
        'image' => '🧥',
        'description' => 'Timeless denim jacket perfect for any casual outfit.',
        'specs' => [
            'Material' => '100% Cotton Denim',
            'Fit' => 'Regular',
            'Care' => 'Machine Wash',
            'Origin' => 'India'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL', 'XXL'],
            'color' => ['Blue', 'Black']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 18,
        'name' => 'Cotton Crew Neck Tee',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 499,
        'original_price' => 799,
        'discount_percent' => 38,
        'rating' => 4.8,
        'reviews_count' => 523,
        'stock' => 200,
        'image' => '👕',
        'description' => 'Comfortable cotton t-shirt for everyday wear.',
        'specs' => [
            'Material' => '100% Cotton',
            'Fit' => 'Regular',
            'GSM' => '180',
            'Care' => 'Machine Wash'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL', 'XXL'],
            'color' => ['White', 'Black', 'Gray', 'Navy', 'Red']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 19,
        'name' => 'Slim Fit Jeans',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.5,
        'reviews_count' => 287,
        'stock' => 80,
        'image' => '👖',
        'description' => 'Modern slim fit jeans with stretch comfort.',
        'specs' => [
            'Material' => 'Denim with Stretch',
            'Fit' => 'Slim',
            'Rise' => 'Mid Rise',
            'Care' => 'Machine Wash'
        ],
        'variants' => [
            'size' => ['28', '30', '32', '34', '36', '38'],
            'color' => ['Blue', 'Black', 'Gray']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 20,
        'name' => 'Running Sneakers',
        'category' => 'fashion',
        'brand' => 'SportsPro',
        'price' => 1899,
        'original_price' => 2999,
        'discount_percent' => 37,
        'rating' => 4.6,
        'reviews_count' => 198,
        'stock' => 100,
        'image' => '👟',
        'description' => 'Lightweight running shoes with excellent cushioning.',
        'specs' => [
            'Type' => 'Running Shoes',
            'Upper Material' => 'Mesh',
            'Sole' => 'EVA',
            'Closure' => 'Lace-up'
        ],
        'variants' => [
            'size' => ['6', '7', '8', '9', '10', '11'],
            'color' => ['Black', 'White', 'Blue', 'Red']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Home & Garden
    [
        'id' => 21,
        'name' => 'Modern Table Lamp',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.4,
        'reviews_count' => 87,
        'stock' => 45,
        'image' => '💡',
        'description' => 'Contemporary table lamp with adjustable brightness.',
        'specs' => [
            'Type' => 'LED Table Lamp',
            'Power' => '12W',
            'Color Temperature' => 'Adjustable',
            'Material' => 'Metal & Plastic'
        ],
        'variants' => [
            'color' => ['White', 'Black', 'Silver']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 22,
        'name' => 'Soft Shaggy Rug',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 1499,
        'original_price' => 2499,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 134,
        'stock' => 30,
        'image' => '🏠',
        'description' => 'Plush shaggy rug perfect for living room or bedroom.',
        'specs' => [
            'Size' => '5x7 feet',
            'Material' => 'Polyester',
            'Pile Height' => '2 inches',
            'Care' => 'Vacuum Clean'
        ],
        'variants' => [
            'size' => ['4x6', '5x7', '6x9'],
            'color' => ['Gray', 'Beige', 'White', 'Brown']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Sports
    [
        'id' => 25,
        'name' => 'Pro Yoga Mat',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 799,
        'original_price' => 1299,
        'discount_percent' => 38,
        'rating' => 4.9,
        'reviews_count' => 312,
        'stock' => 120,
        'image' => '🧘',
        'description' => 'Premium non-slip yoga mat for comfortable practice.',
        'specs' => [
            'Size' => '72 x 24 inches',
            'Thickness' => '6mm',
            'Material' => 'TPE',
            'Features' => 'Non-slip, Eco-friendly'
        ],
        'variants' => [
            'color' => ['Purple', 'Blue', 'Green', 'Pink']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 26,
        'name' => 'Adjustable Dumbbells',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 1999,
        'original_price' => 2999,
        'discount_percent' => 33,
        'rating' => 4.5,
        'reviews_count' => 89,
        'stock' => 50,
        'image' => '🏋️',
        'description' => 'Space-saving adjustable dumbbells for home workouts.',
        'specs' => [
            'Weight Range' => '2.5kg to 12.5kg per dumbbell',
            'Material' => 'Cast Iron with Rubber Coating',
            'Includes' => 'Storage Tray'
        ],
        'variants' => [
            'weight' => ['5-25kg Set', '10-50kg Set']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Books
    [
        'id' => 29,
        'name' => 'The Great Adventure',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 299,
        'original_price' => 499,
        'discount_percent' => 40,
        'rating' => 4.8,
        'reviews_count' => 567,
        'stock' => 200,
        'image' => '📚',
        'description' => 'Epic adventure novel that takes you on an unforgettable journey.',
        'specs' => [
            'Pages' => '420',
            'Language' => 'English',
            'Publisher' => 'Classic Books',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 30,
        'name' => 'Future World (Sci-Fi)',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 349,
        'original_price' => 599,
        'discount_percent' => 42,
        'rating' => 4.6,
        'reviews_count' => 234,
        'stock' => 150,
        'image' => '📖',
        'description' => 'Mind-bending science fiction exploring future technologies.',
        'specs' => [
            'Pages' => '380',
            'Language' => 'English',
            'Publisher' => 'Future Press',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover', 'eBook']
        ],
        'featured' => false,
        'tags' => ['new']
    ],

    // Toys
    [
        'id' => 33,
        'name' => 'Building Blocks Set',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.9,
        'reviews_count' => 445,
        'stock' => 80,
        'image' => '🧱',
        'description' => 'Creative building blocks set for hours of imaginative play.',
        'specs' => [
            'Pieces' => '500+',
            'Age' => '3+ years',
            'Material' => 'High-quality Plastic',
            'Safety' => 'Non-toxic, BIS Certified'
        ],
        'variants' => [
            'set' => ['Basic 500pc', 'Deluxe 1000pc']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 34,
        'name' => 'Plush Teddy Bear',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 5.0,
        'reviews_count' => 678,
        'stock' => 150,
        'image' => '🧸',
        'description' => 'Soft and cuddly teddy bear, perfect gift for kids.',
        'specs' => [
            'Size' => '16 inches',
            'Material' => 'Soft Plush',
            'Filling' => 'Polyester Fiber',
            'Care' => 'Hand Wash'
        ],
        'variants' => [
            'color' => ['Brown', 'White', 'Pink'],
            'size' => ['Small 12"', 'Medium 16"', 'Large 24"']
        ],
        'featured' => false,
        'tags' => []
    ]
];

// Helper functions
function getProductById($id) {
    global $products;
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}

function getProductsByCategory($category) {
    global $products;
    if ($category === 'all') {
        return $products;
    }
    return array_filter($products, function($product) use ($category) {
        return $product['category'] === $category;
    });
}

function getFeaturedProducts() {
    global $products;
    return array_filter($products, function($product) {
        return $product['featured'] === true;
    });
}

function getProductsByPriceRange($min, $max) {
    global $products;
    return array_filter($products, function($product) use ($min, $max) {
        return $product['price'] >= $min && $product['price'] <= $max;
    });
}

function getProductsByRating($minRating) {
    global $products;
    return array_filter($products, function($product) use ($minRating) {
        return $product['rating'] >= $minRating;
    });
}
?>