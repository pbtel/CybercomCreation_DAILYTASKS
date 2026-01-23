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
        'price' => 49990,
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
    ],

    // Additional Electronics Products
    [
        'id' => 7,
        'name' => 'Bluetooth Speaker',
        'category' => 'electronics',
        'brand' => 'AudioMax',
        'price' => 1499,
        'original_price' => 2499,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 189,
        'stock' => 85,
        'image' => '🔊',
        'description' => 'Portable Bluetooth speaker with powerful bass and 12-hour battery life.',
        'specs' => [
            'Power Output' => '20W',
            'Battery Life' => '12 hours',
            'Connectivity' => 'Bluetooth 5.0',
            'Water Resistance' => 'IPX7',
            'Weight' => '450g'
        ],
        'variants' => [
            'color' => ['Black', 'Blue', 'Red']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 8,
        'name' => 'Mechanical Keyboard',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 2999,
        'original_price' => 4999,
        'discount_percent' => 40,
        'rating' => 4.8,
        'reviews_count' => 267,
        'stock' => 60,
        'image' => '⌨️',
        'description' => 'RGB mechanical gaming keyboard with customizable keys.',
        'specs' => [
            'Switch Type' => 'Blue Mechanical',
            'Backlighting' => 'RGB',
            'Connectivity' => 'USB-C Wired',
            'Keys' => '104 Keys'
        ],
        'variants' => [
            'switch' => ['Blue', 'Red', 'Brown']
        ],
        'featured' => true,
        'tags' => ['hot']
    ],
    [
        'id' => 9,
        'name' => 'Webcam HD Pro',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 1799,
        'original_price' => 2999,
        'discount_percent' => 40,
        'rating' => 4.5,
        'reviews_count' => 145,
        'stock' => 70,
        'image' => '📹',
        'description' => '1080p HD webcam perfect for video calls and streaming.',
        'specs' => [
            'Resolution' => '1920x1080 @ 30fps',
            'Field of View' => '90 degrees',
            'Microphone' => 'Built-in Stereo',
            'Connection' => 'USB 2.0'
        ],
        'variants' => [
            'color' => ['Black']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 10,
        'name' => 'Power Bank 20000mAh',
        'category' => 'electronics',
        'brand' => 'SmartLife',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.6,
        'reviews_count' => 312,
        'stock' => 150,
        'image' => '🔋',
        'description' => 'High-capacity power bank with fast charging support.',
        'specs' => [
            'Capacity' => '20000mAh',
            'Input' => 'USB-C 18W',
            'Output' => 'Dual USB 22.5W',
            'Weight' => '380g'
        ],
        'variants' => [
            'color' => ['Black', 'White', 'Blue']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 11,
        'name' => 'USB-C Hub 7-in-1',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 1599,
        'original_price' => 2499,
        'discount_percent' => 36,
        'rating' => 4.4,
        'reviews_count' => 98,
        'stock' => 90,
        'image' => '🔌',
        'description' => 'Multiport USB-C hub with HDMI, USB 3.0, and SD card reader.',
        'specs' => [
            'Ports' => '7 (HDMI, 3xUSB, SD, microSD, PD)',
            'HDMI Output' => '4K @ 30Hz',
            'Data Transfer' => 'USB 3.0 5Gbps',
            'Power Delivery' => '100W'
        ],
        'variants' => [
            'color' => ['Gray', 'Silver']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 12,
        'name' => 'Gaming Mouse RGB',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.7,
        'reviews_count' => 234,
        'stock' => 110,
        'image' => '🖱️',
        'description' => 'Professional gaming mouse with programmable buttons and RGB lighting.',
        'specs' => [
            'DPI' => 'Up to 16000',
            'Buttons' => '8 Programmable',
            'Polling Rate' => '1000Hz',
            'Lighting' => 'RGB'
        ],
        'variants' => [
            'color' => ['Black', 'White']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 13,
        'name' => 'External SSD 1TB',
        'category' => 'electronics',
        'brand' => 'TechnoGear',
        'price' => 4999,
        'original_price' => 6999,
        'discount_percent' => 29,
        'rating' => 4.9,
        'reviews_count' => 178,
        'stock' => 45,
        'image' => '💾',
        'description' => 'Portable SSD with ultra-fast read/write speeds.',
        'specs' => [
            'Capacity' => '1TB',
            'Interface' => 'USB 3.2 Gen 2',
            'Read Speed' => 'Up to 1050MB/s',
            'Write Speed' => 'Up to 1000MB/s'
        ],
        'variants' => [
            'capacity' => ['500GB', '1TB', '2TB']
        ],
        'featured' => true,
        'tags' => ['new']
    ],
    [
        'id' => 14,
        'name' => 'Wireless Earbuds Pro',
        'category' => 'electronics',
        'brand' => 'AudioMax',
        'price' => 2499,
        'original_price' => 3999,
        'discount_percent' => 38,
        'rating' => 4.6,
        'reviews_count' => 456,
        'stock' => 120,
        'image' => '🎧',
        'description' => 'True wireless earbuds with active noise cancellation.',
        'specs' => [
            'Driver' => '10mm Dynamic',
            'Battery Life' => '6hrs + 24hrs case',
            'Charging' => 'USB-C + Wireless',
            'Features' => 'ANC, Touch Control'
        ],
        'variants' => [
            'color' => ['Black', 'White', 'Blue']
        ],
        'featured' => true,
        'tags' => ['hot', 'bestseller']
    ],
    [
        'id' => 15,
        'name' => 'Ring Light 10 inch',
        'category' => 'electronics',
        'brand' => 'SmartLife',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.5,
        'reviews_count' => 267,
        'stock' => 95,
        'image' => '💡',
        'description' => 'LED ring light perfect for photography and video calls.',
        'specs' => [
            'Size' => '10 inches',
            'Brightness' => '3 Modes, 10 Levels',
            'Color Temperature' => '3000K-6500K',
            'Mount' => 'Tripod Stand Included'
        ],
        'variants' => [
            'size' => ['10 inch', '12 inch', '18 inch']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 16,
        'name' => 'Smart TV Streaming Stick',
        'category' => 'electronics',
        'brand' => 'SmartLife',
        'price' => 1999,
        'original_price' => 2999,
        'discount_percent' => 33,
        'rating' => 4.4,
        'reviews_count' => 189,
        'stock' => 80,
        'image' => '📺',
        'description' => 'Turn any TV into a smart TV with 4K streaming support.',
        'specs' => [
            'Resolution' => '4K HDR',
            'Processor' => 'Quad-core',
            'RAM' => '2GB',
            'Storage' => '8GB'
        ],
        'variants' => [
            'version' => ['Standard', '4K']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Additional Fashion Products
    [
        'id' => 35,
        'name' => 'Leather Wallet',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 699,
        'original_price' => 1299,
        'discount_percent' => 46,
        'rating' => 4.7,
        'reviews_count' => 234,
        'stock' => 120,
        'image' => '👛',
        'description' => 'Genuine leather wallet with multiple card slots.',
        'specs' => [
            'Material' => 'Genuine Leather',
            'Card Slots' => '8',
            'Coin Pocket' => 'Yes',
            'Dimensions' => '11 x 9 cm'
        ],
        'variants' => [
            'color' => ['Black', 'Brown', 'Tan']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 36,
        'name' => 'Sunglasses Aviator',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 899,
        'original_price' => 1599,
        'discount_percent' => 44,
        'rating' => 4.6,
        'reviews_count' => 178,
        'stock' => 100,
        'image' => '🕶️',
        'description' => 'Classic aviator sunglasses with UV protection.',
        'specs' => [
            'Frame' => 'Metal',
            'Lens' => 'Polarized',
            'UV Protection' => 'UV400',
            'Size' => 'Medium'
        ],
        'variants' => [
            'color' => ['Gold-Green', 'Silver-Gray', 'Black']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 37,
        'name' => 'Casual Backpack',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.8,
        'reviews_count' => 345,
        'stock' => 90,
        'image' => '🎒',
        'description' => 'Stylish backpack with laptop compartment.',
        'specs' => [
            'Capacity' => '25L',
            'Laptop Fit' => 'Up to 15.6 inch',
            'Material' => 'Water-resistant Polyester',
            'Pockets' => 'Multiple compartments'
        ],
        'variants' => [
            'color' => ['Black', 'Gray', 'Navy', 'Olive']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 38,
        'name' => 'Formal Shirt',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 999,
        'original_price' => 1699,
        'discount_percent' => 41,
        'rating' => 4.5,
        'reviews_count' => 198,
        'stock' => 110,
        'image' => '👔',
        'description' => 'Premium cotton formal shirt for office wear.',
        'specs' => [
            'Material' => '100% Cotton',
            'Fit' => 'Slim Fit',
            'Collar' => 'Spread',
            'Care' => 'Machine Wash'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL', 'XXL'],
            'color' => ['White', 'Blue', 'Pink', 'Black']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 39,
        'name' => 'Sports Cap',
        'category' => 'fashion',
        'brand' => 'SportsPro',
        'price' => 399,
        'original_price' => 699,
        'discount_percent' => 43,
        'rating' => 4.4,
        'reviews_count' => 267,
        'stock' => 150,
        'image' => '🧢',
        'description' => 'Breathable sports cap with adjustable strap.',
        'specs' => [
            'Material' => 'Cotton Blend',
            'Type' => 'Baseball Cap',
            'Adjustable' => 'Yes',
            'UV Protection' => 'Yes'
        ],
        'variants' => [
            'color' => ['Black', 'White', 'Red', 'Blue', 'Gray']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 40,
        'name' => 'Leather Belt',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 4.6,
        'reviews_count' => 156,
        'stock' => 130,
        'image' => '👖',
        'description' => 'Classic leather belt with metal buckle.',
        'specs' => [
            'Material' => 'Genuine Leather',
            'Width' => '3.5 cm',
            'Buckle' => 'Metal',
            'Sizes' => '28-44 inches'
        ],
        'variants' => [
            'color' => ['Black', 'Brown'],
            'size' => ['28-32', '32-36', '36-40', '40-44']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 41,
        'name' => 'Hoodie Sweatshirt',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 1199,
        'original_price' => 1999,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 289,
        'stock' => 95,
        'image' => '🧥',
        'description' => 'Comfortable cotton hoodie for casual wear.',
        'specs' => [
            'Material' => '80% Cotton, 20% Polyester',
            'Fit' => 'Regular',
            'Features' => 'Kangaroo Pocket, Drawstring Hood',
            'GSM' => '280'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL', 'XXL'],
            'color' => ['Black', 'Gray', 'Navy', 'Maroon']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 42,
        'name' => 'Ankle Socks Pack',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 299,
        'original_price' => 499,
        'discount_percent' => 40,
        'rating' => 4.5,
        'reviews_count' => 456,
        'stock' => 200,
        'image' => '🧦',
        'description' => 'Comfortable ankle socks pack of 6 pairs.',
        'specs' => [
            'Material' => 'Cotton Blend',
            'Pack' => '6 Pairs',
            'Size' => 'Free Size',
            'Care' => 'Machine Wash'
        ],
        'variants' => [
            'color' => ['Multicolor', 'Black', 'White']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 43,
        'name' => 'Formal Trousers',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 1499,
        'original_price' => 2499,
        'discount_percent' => 40,
        'rating' => 4.6,
        'reviews_count' => 178,
        'stock' => 85,
        'image' => '👖',
        'description' => 'Wrinkle-free formal trousers for office wear.',
        'specs' => [
            'Material' => 'Poly-Viscose',
            'Fit' => 'Slim Fit',
            'Features' => 'Wrinkle-free, Easy Care',
            'Pockets' => '4'
        ],
        'variants' => [
            'size' => ['28', '30', '32', '34', '36', '38'],
            'color' => ['Black', 'Navy', 'Gray', 'Khaki']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 44,
        'name' => 'Polo T-Shirt',
        'category' => 'fashion',
        'brand' => 'FashionHub',
        'price' => 699,
        'original_price' => 1199,
        'discount_percent' => 42,
        'rating' => 4.7,
        'reviews_count' => 312,
        'stock' => 140,
        'image' => '👕',
        'description' => 'Classic polo t-shirt with collar.',
        'specs' => [
            'Material' => 'Cotton Pique',
            'Fit' => 'Regular',
            'Collar' => 'Ribbed',
            'Buttons' => '3'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL', 'XXL'],
            'color' => ['White', 'Black', 'Navy', 'Red', 'Green']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Additional Home & Garden Products
    [
        'id' => 23,
        'name' => 'Wall Clock Modern',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 699,
        'original_price' => 1199,
        'discount_percent' => 42,
        'rating' => 4.5,
        'reviews_count' => 145,
        'stock' => 70,
        'image' => '🕐',
        'description' => 'Silent wall clock with modern design.',
        'specs' => [
            'Size' => '12 inches',
            'Movement' => 'Silent Quartz',
            'Material' => 'Plastic Frame',
            'Battery' => '1 AA (not included)'
        ],
        'variants' => [
            'color' => ['White', 'Black', 'Wood']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 24,
        'name' => 'Cushion Covers Set',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 499,
        'original_price' => 899,
        'discount_percent' => 44,
        'rating' => 4.6,
        'reviews_count' => 234,
        'stock' => 110,
        'image' => '🛋️',
        'description' => 'Decorative cushion covers set of 5.',
        'specs' => [
            'Size' => '16x16 inches',
            'Material' => 'Cotton Blend',
            'Pack' => '5 Pieces',
            'Care' => 'Machine Wash'
        ],
        'variants' => [
            'design' => ['Geometric', 'Floral', 'Solid', 'Abstract']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 45,
        'name' => 'Bedsheet Set King',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 1299,
        'original_price' => 2199,
        'discount_percent' => 41,
        'rating' => 4.7,
        'reviews_count' => 267,
        'stock' => 60,
        'image' => '🛏️',
        'description' => 'Premium cotton bedsheet set with 2 pillow covers.',
        'specs' => [
            'Size' => 'King (90x100 inches)',
            'Material' => '100% Cotton',
            'Thread Count' => '200 TC',
            'Includes' => '1 Bedsheet + 2 Pillow Covers'
        ],
        'variants' => [
            'color' => ['White', 'Blue', 'Pink', 'Gray'],
            'size' => ['Queen', 'King']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 46,
        'name' => 'Kitchen Organizer Rack',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.4,
        'reviews_count' => 189,
        'stock' => 75,
        'image' => '🍽️',
        'description' => 'Multi-tier kitchen storage rack.',
        'specs' => [
            'Tiers' => '3',
            'Material' => 'Stainless Steel',
            'Dimensions' => '45 x 30 x 60 cm',
            'Weight Capacity' => '15 kg per tier'
        ],
        'variants' => [
            'tiers' => ['2-Tier', '3-Tier', '4-Tier']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 47,
        'name' => 'Bathroom Mirror',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 1199,
        'original_price' => 1999,
        'discount_percent' => 40,
        'rating' => 4.6,
        'reviews_count' => 123,
        'stock' => 50,
        'image' => '🪞',
        'description' => 'Frameless bathroom mirror with beveled edges.',
        'specs' => [
            'Size' => '18 x 24 inches',
            'Type' => 'Frameless',
            'Thickness' => '5mm',
            'Mounting' => 'Wall Mount'
        ],
        'variants' => [
            'size' => ['18x24', '24x36', '30x40']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 48,
        'name' => 'Curtains Blackout',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 999,
        'original_price' => 1699,
        'discount_percent' => 41,
        'rating' => 4.5,
        'reviews_count' => 178,
        'stock' => 80,
        'image' => '🪟',
        'description' => 'Blackout curtains for bedroom, set of 2 panels.',
        'specs' => [
            'Size' => '5 x 7 feet per panel',
            'Material' => 'Polyester',
            'Features' => 'Blackout, Thermal Insulated',
            'Pack' => '2 Panels'
        ],
        'variants' => [
            'color' => ['Gray', 'Beige', 'Navy', 'Brown'],
            'size' => ['5x7', '7x9']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 49,
        'name' => 'Flower Vase Ceramic',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 156,
        'stock' => 90,
        'image' => '🏺',
        'description' => 'Elegant ceramic flower vase for home decor.',
        'specs' => [
            'Material' => 'Ceramic',
            'Height' => '10 inches',
            'Style' => 'Modern',
            'Color' => 'Glazed Finish'
        ],
        'variants' => [
            'color' => ['White', 'Blue', 'Gray', 'Gold']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 50,
        'name' => 'Storage Baskets Set',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 799,
        'original_price' => 1299,
        'discount_percent' => 38,
        'rating' => 4.6,
        'reviews_count' => 201,
        'stock' => 100,
        'image' => '🧺',
        'description' => 'Woven storage baskets set of 3.',
        'specs' => [
            'Material' => 'Cotton Rope',
            'Sizes' => 'Small, Medium, Large',
            'Pack' => '3 Pieces',
            'Use' => 'Multipurpose Storage'
        ],
        'variants' => [
            'color' => ['Natural', 'Gray', 'White']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 51,
        'name' => 'Desk Organizer',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 499,
        'original_price' => 899,
        'discount_percent' => 44,
        'rating' => 4.5,
        'reviews_count' => 234,
        'stock' => 120,
        'image' => '📎',
        'description' => 'Multi-compartment desk organizer for office supplies.',
        'specs' => [
            'Material' => 'Plastic',
            'Compartments' => '6',
            'Dimensions' => '25 x 15 x 10 cm',
            'Color' => 'Solid'
        ],
        'variants' => [
            'color' => ['Black', 'White', 'Gray']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 52,
        'name' => 'Trash Can Pedal',
        'category' => 'home',
        'brand' => 'HomeEssentials',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.4,
        'reviews_count' => 167,
        'stock' => 70,
        'image' => '🗑️',
        'description' => 'Stainless steel pedal trash can with lid.',
        'specs' => [
            'Capacity' => '12 Liters',
            'Material' => 'Stainless Steel',
            'Type' => 'Pedal Operated',
            'Liner' => 'Removable Inner Bucket'
        ],
        'variants' => [
            'size' => ['8L', '12L', '20L']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Additional Sports Products
    [
        'id' => 27,
        'name' => 'Resistance Bands Set',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 699,
        'original_price' => 1199,
        'discount_percent' => 42,
        'rating' => 4.7,
        'reviews_count' => 234,
        'stock' => 100,
        'image' => '🏋️',
        'description' => 'Set of 5 resistance bands for strength training.',
        'specs' => [
            'Levels' => '5 (X-Light to X-Heavy)',
            'Material' => 'Natural Latex',
            'Length' => '12 inches',
            'Includes' => 'Carry Bag'
        ],
        'variants' => [
            'set' => ['5-Band Set', '11-Piece Set']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 28,
        'name' => 'Jump Rope Speed',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 399,
        'original_price' => 699,
        'discount_percent' => 43,
        'rating' => 4.6,
        'reviews_count' => 189,
        'stock' => 150,
        'image' => '🤸',
        'description' => 'Adjustable speed jump rope for cardio workouts.',
        'specs' => [
            'Length' => 'Adjustable up to 9 feet',
            'Material' => 'Steel Wire with PVC Coating',
            'Handles' => 'Anti-slip Foam',
            'Bearings' => 'Ball Bearing System'
        ],
        'variants' => [
            'color' => ['Black', 'Blue', 'Red']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 53,
        'name' => 'Football Size 5',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 699,
        'original_price' => 1199,
        'discount_percent' => 42,
        'rating' => 4.8,
        'reviews_count' => 312,
        'stock' => 90,
        'image' => '⚽',
        'description' => 'Professional football for matches and training.',
        'specs' => [
            'Size' => '5 (Official)',
            'Material' => 'PU Synthetic Leather',
            'Bladder' => 'Butyl',
            'Use' => 'Match/Training'
        ],
        'variants' => [
            'color' => ['White-Black', 'White-Blue', 'White-Red']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 54,
        'name' => 'Cricket Bat Kashmir Willow',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.5,
        'reviews_count' => 178,
        'stock' => 60,
        'image' => '🏏',
        'description' => 'Kashmir willow cricket bat for leather ball.',
        'specs' => [
            'Wood' => 'Kashmir Willow',
            'Size' => 'Full (Short Handle)',
            'Weight' => '1100-1200g',
            'Use' => 'Leather Ball'
        ],
        'variants' => [
            'size' => ['Size 6', 'Full Size']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 55,
        'name' => 'Badminton Racket',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.6,
        'reviews_count' => 234,
        'stock' => 80,
        'image' => '🏸',
        'description' => 'Lightweight badminton racket with cover.',
        'specs' => [
            'Material' => 'Aluminum Alloy',
            'Weight' => '95g',
            'Grip' => 'G4',
            'Includes' => 'Cover'
        ],
        'variants' => [
            'color' => ['Blue', 'Red', 'Green']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 56,
        'name' => 'Tennis Ball Can',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 399,
        'original_price' => 699,
        'discount_percent' => 43,
        'rating' => 4.7,
        'reviews_count' => 267,
        'stock' => 120,
        'image' => '🎾',
        'description' => 'Professional tennis balls can of 3.',
        'specs' => [
            'Pack' => '3 Balls',
            'Type' => 'Pressurized',
            'Surface' => 'All Court',
            'Approved' => 'ITF Approved'
        ],
        'variants' => [
            'pack' => ['3 Balls', '6 Balls']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 57,
        'name' => 'Gym Gloves',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 499,
        'original_price' => 899,
        'discount_percent' => 44,
        'rating' => 4.5,
        'reviews_count' => 189,
        'stock' => 110,
        'image' => '🧤',
        'description' => 'Padded gym gloves for weight training.',
        'specs' => [
            'Material' => 'Synthetic Leather',
            'Padding' => 'Foam Padded Palm',
            'Closure' => 'Velcro Wrist Strap',
            'Breathable' => 'Mesh Back'
        ],
        'variants' => [
            'size' => ['S', 'M', 'L', 'XL']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 58,
        'name' => 'Water Bottle Sports',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 299,
        'original_price' => 599,
        'discount_percent' => 50,
        'rating' => 4.6,
        'reviews_count' => 345,
        'stock' => 180,
        'image' => '💧',
        'description' => 'BPA-free sports water bottle 1 liter.',
        'specs' => [
            'Capacity' => '1000ml',
            'Material' => 'BPA-free Plastic',
            'Features' => 'Leak-proof, Easy Grip',
            'Mouth' => 'Wide Mouth'
        ],
        'variants' => [
            'color' => ['Blue', 'Black', 'Green', 'Red']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 59,
        'name' => 'Cycling Gloves',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 399,
        'original_price' => 799,
        'discount_percent' => 50,
        'rating' => 4.4,
        'reviews_count' => 156,
        'stock' => 95,
        'image' => '🚴',
        'description' => 'Half-finger cycling gloves with gel padding.',
        'specs' => [
            'Type' => 'Half Finger',
            'Padding' => 'Gel Palm',
            'Material' => 'Lycra + Microfiber',
            'Closure' => 'Hook & Loop'
        ],
        'variants' => [
            'size' => ['M', 'L', 'XL']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 60,
        'name' => 'Skipping Mat',
        'category' => 'sports',
        'brand' => 'SportsPro',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 201,
        'stock' => 75,
        'image' => '🏃',
        'description' => 'Shock-absorbing exercise mat for jumping rope.',
        'specs' => [
            'Size' => '6 x 4 feet',
            'Thickness' => '6mm',
            'Material' => 'High-density PVC',
            'Features' => 'Non-slip, Portable'
        ],
        'variants' => [
            'color' => ['Black', 'Gray']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Additional Books
    [
        'id' => 31,
        'name' => 'Mystery at Midnight',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 279,
        'original_price' => 499,
        'discount_percent' => 44,
        'rating' => 4.7,
        'reviews_count' => 289,
        'stock' => 170,
        'image' => '📕',
        'description' => 'Gripping mystery thriller that keeps you on edge.',
        'specs' => [
            'Pages' => '350',
            'Language' => 'English',
            'Publisher' => 'Mystery House',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 32,
        'name' => 'Self-Help Success Guide',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 399,
        'original_price' => 699,
        'discount_percent' => 43,
        'rating' => 4.8,
        'reviews_count' => 456,
        'stock' => 140,
        'image' => '📗',
        'description' => 'Practical guide to achieving personal and professional success.',
        'specs' => [
            'Pages' => '280',
            'Language' => 'English',
            'Publisher' => 'Success Books',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover', 'eBook']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 61,
        'name' => 'Cookbook Indian Cuisine',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 449,
        'original_price' => 799,
        'discount_percent' => 44,
        'rating' => 4.9,
        'reviews_count' => 378,
        'stock' => 100,
        'image' => '📙',
        'description' => 'Comprehensive Indian cooking recipes with step-by-step instructions.',
        'specs' => [
            'Pages' => '320',
            'Language' => 'English',
            'Publisher' => 'Food Press',
            'Format' => 'Hardcover'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 62,
        'name' => 'Fantasy Epic Series Vol 1',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 399,
        'original_price' => 699,
        'discount_percent' => 43,
        'rating' => 4.8,
        'reviews_count' => 512,
        'stock' => 130,
        'image' => '📘',
        'description' => 'First book in epic fantasy series with dragons and magic.',
        'specs' => [
            'Pages' => '480',
            'Language' => 'English',
            'Publisher' => 'Fantasy World',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover', 'eBook']
        ],
        'featured' => true,
        'tags' => ['hot']
    ],
    [
        'id' => 63,
        'name' => 'Biography Great Leaders',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 349,
        'original_price' => 599,
        'discount_percent' => 42,
        'rating' => 4.6,
        'reviews_count' => 234,
        'stock' => 110,
        'image' => '📔',
        'description' => 'Inspiring biographies of world-changing leaders.',
        'specs' => [
            'Pages' => '400',
            'Language' => 'English',
            'Publisher' => 'History Books',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 64,
        'name' => 'Romance Novel Collection',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 299,
        'original_price' => 549,
        'discount_percent' => 46,
        'rating' => 4.5,
        'reviews_count' => 389,
        'stock' => 150,
        'image' => '💝',
        'description' => 'Heartwarming romance stories collection.',
        'specs' => [
            'Pages' => '320',
            'Language' => 'English',
            'Publisher' => 'Romance Press',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'eBook']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 65,
        'name' => 'Business Strategy Book',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 499,
        'original_price' => 899,
        'discount_percent' => 44,
        'rating' => 4.7,
        'reviews_count' => 267,
        'stock' => 90,
        'image' => '💼',
        'description' => 'Modern business strategies for entrepreneurs.',
        'specs' => [
            'Pages' => '360',
            'Language' => 'English',
            'Publisher' => 'Business World',
            'Format' => 'Hardcover'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover', 'eBook']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 66,
        'name' => 'Children Story Book',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 249,
        'original_price' => 449,
        'discount_percent' => 45,
        'rating' => 4.9,
        'reviews_count' => 567,
        'stock' => 200,
        'image' => '📚',
        'description' => 'Illustrated children stories with moral lessons.',
        'specs' => [
            'Pages' => '120',
            'Language' => 'English',
            'Publisher' => 'Kids Books',
            'Format' => 'Hardcover'
        ],
        'variants' => [
            'format' => ['Hardcover']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 67,
        'name' => 'Horror Stories Anthology',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 329,
        'original_price' => 599,
        'discount_percent' => 45,
        'rating' => 4.4,
        'reviews_count' => 178,
        'stock' => 120,
        'image' => '👻',
        'description' => 'Collection of spine-chilling horror stories.',
        'specs' => [
            'Pages' => '300',
            'Language' => 'English',
            'Publisher' => 'Dark Tales',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'eBook']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 68,
        'name' => 'Poetry Collection Modern',
        'category' => 'books',
        'brand' => 'BookWorld',
        'price' => 279,
        'original_price' => 499,
        'discount_percent' => 44,
        'rating' => 4.6,
        'reviews_count' => 201,
        'stock' => 100,
        'image' => '✍️',
        'description' => 'Contemporary poetry exploring life and emotions.',
        'specs' => [
            'Pages' => '200',
            'Language' => 'English',
            'Publisher' => 'Poetry House',
            'Format' => 'Paperback'
        ],
        'variants' => [
            'format' => ['Paperback', 'Hardcover']
        ],
        'featured' => false,
        'tags' => []
    ],

    // Additional Toys
    [
        'id' => 69,
        'name' => 'Remote Control Car',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 1499,
        'original_price' => 2499,
        'discount_percent' => 40,
        'rating' => 4.8,
        'reviews_count' => 312,
        'stock' => 70,
        'image' => '🚗',
        'description' => 'High-speed remote control racing car.',
        'specs' => [
            'Speed' => 'Up to 20 km/h',
            'Battery' => 'Rechargeable',
            'Range' => '30 meters',
            'Age' => '6+ years'
        ],
        'variants' => [
            'color' => ['Red', 'Blue', 'Green']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 70,
        'name' => 'Puzzle 1000 Pieces',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 4.7,
        'reviews_count' => 234,
        'stock' => 90,
        'image' => '🧩',
        'description' => 'Challenging jigsaw puzzle for family fun.',
        'specs' => [
            'Pieces' => '1000',
            'Size' => '27 x 20 inches when complete',
            'Age' => '8+ years',
            'Material' => 'Premium Cardboard'
        ],
        'variants' => [
            'theme' => ['Landscape', 'Animals', 'Cities']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 71,
        'name' => 'Board Game Family',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 899,
        'original_price' => 1499,
        'discount_percent' => 40,
        'rating' => 4.9,
        'reviews_count' => 456,
        'stock' => 80,
        'image' => '🎲',
        'description' => 'Classic board game for 2-6 players.',
        'specs' => [
            'Players' => '2-6',
            'Age' => '6+ years',
            'Duration' => '30-60 minutes',
            'Type' => 'Strategy Game'
        ],
        'variants' => [
            'edition' => ['Classic', 'Deluxe']
        ],
        'featured' => false,
        'tags' => ['bestseller']
    ],
    [
        'id' => 72,
        'name' => 'Action Figure Superhero',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 799,
        'original_price' => 1299,
        'discount_percent' => 38,
        'rating' => 4.6,
        'reviews_count' => 289,
        'stock' => 100,
        'image' => '🦸',
        'description' => 'Poseable superhero action figure with accessories.',
        'specs' => [
            'Height' => '12 inches',
            'Material' => 'Plastic',
            'Articulation' => '20 Points',
            'Includes' => 'Accessories'
        ],
        'variants' => [
            'character' => ['Hero A', 'Hero B', 'Hero C']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 73,
        'name' => 'Doll House Wooden',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 2499,
        'original_price' => 3999,
        'discount_percent' => 38,
        'rating' => 4.8,
        'reviews_count' => 178,
        'stock' => 40,
        'image' => '🏠',
        'description' => 'Beautiful wooden dollhouse with furniture.',
        'specs' => [
            'Levels' => '3',
            'Material' => 'Wood',
            'Rooms' => '6',
            'Includes' => 'Furniture Set'
        ],
        'variants' => [
            'size' => ['Medium', 'Large']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 74,
        'name' => 'Art & Craft Kit',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 699,
        'original_price' => 1199,
        'discount_percent' => 42,
        'rating' => 4.7,
        'reviews_count' => 267,
        'stock' => 110,
        'image' => '🎨',
        'description' => 'Complete art and craft kit for creative kids.',
        'specs' => [
            'Items' => '100+ pieces',
            'Includes' => 'Colors, Brushes, Paper, Glue',
            'Age' => '5+ years',
            'Type' => 'Art Supplies'
        ],
        'variants' => [
            'set' => ['Basic', 'Premium']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 75,
        'name' => 'Musical Keyboard Toy',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 1299,
        'original_price' => 1999,
        'discount_percent' => 35,
        'rating' => 4.5,
        'reviews_count' => 189,
        'stock' => 60,
        'image' => '🎹',
        'description' => 'Electronic keyboard with 37 keys and demo songs.',
        'specs' => [
            'Keys' => '37',
            'Sounds' => '8 Tones',
            'Rhythms' => '8 Rhythms',
            'Power' => 'Battery/Adapter'
        ],
        'variants' => [
            'color' => ['Black', 'Pink']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 76,
        'name' => 'Toy Kitchen Set',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 1899,
        'original_price' => 2999,
        'discount_percent' => 37,
        'rating' => 4.8,
        'reviews_count' => 345,
        'stock' => 50,
        'image' => '🍳',
        'description' => 'Pretend play kitchen set with accessories.',
        'specs' => [
            'Pieces' => '30+',
            'Material' => 'Plastic',
            'Features' => 'Lights & Sounds',
            'Age' => '3+ years'
        ],
        'variants' => [
            'color' => ['Pink', 'Blue']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 77,
        'name' => 'Doctor Play Set',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 599,
        'original_price' => 999,
        'discount_percent' => 40,
        'rating' => 4.6,
        'reviews_count' => 234,
        'stock' => 95,
        'image' => '⚕️',
        'description' => 'Medical kit toy for pretend doctor play.',
        'specs' => [
            'Pieces' => '15',
            'Includes' => 'Stethoscope, Syringe, Tools',
            'Material' => 'Plastic',
            'Age' => '3+ years'
        ],
        'variants' => [
            'color' => ['Blue', 'Pink']
        ],
        'featured' => false,
        'tags' => []
    ],
    [
        'id' => 78,
        'name' => 'Soft Ball Pool Set',
        'category' => 'toys',
        'brand' => 'ToyLand',
        'price' => 1499,
        'original_price' => 2499,
        'discount_percent' => 40,
        'rating' => 4.9,
        'reviews_count' => 412,
        'stock' => 55,
        'image' => '⚾',
        'description' => 'Colorful soft ball pool with 100 balls.',
        'specs' => [
            'Balls' => '100',
            'Pool Size' => '120 x 120 cm',
            'Material' => 'Soft Plastic',
            'Age' => '1+ years'
        ],
        'variants' => [
            'balls' => ['100 Balls', '200 Balls']
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