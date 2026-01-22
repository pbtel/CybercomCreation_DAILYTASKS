<?php
/**
 * Orders Data - EasyCart Phase 2
 * Static order history for demonstration
 */

$orders = [
    [
        'order_id' => 'ORD-2024-001',
        'user_id' => 1,
        'date' => '2024-12-18',
        'status' => 'shipped',
        'payment_method' => 'Credit Card',
        'shipping_address' => [
            'name' => 'John Doe',
            'address' => '123 Main Street, Apt 4B',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'phone' => '+91 98765 43210'
        ],
        'items' => [
            [
                'product_id' => 1,
                'product_name' => 'Smartphone X',
                'quantity' => 1,
                'price' => 36999,
                'variant' => ['color' => 'Black', 'storage' => '256GB']
            ],
            [
                'product_id' => 6,
                'product_name' => 'Wireless Mouse',
                'quantity' => 2,
                'price' => 799,
                'variant' => ['color' => 'Black']
            ]
        ],
        'subtotal' => 38597,
        'shipping' => 0,
        'tax' => 3860,
        'discount' => 500,
        'total' => 41957,
        'tracking_number' => 'TRK123456789',
        'estimated_delivery' => '2024-12-22'
    ],
    [
        'order_id' => 'ORD-2024-002',
        'user_id' => 1,
        'date' => '2024-12-15',
        'status' => 'processing',
        'payment_method' => 'UPI',
        'shipping_address' => [
            'name' => 'John Doe',
            'address' => '123 Main Street, Apt 4B',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'phone' => '+91 98765 43210'
        ],
        'items' => [
            [
                'product_id' => 3,
                'product_name' => 'Wireless Headphones',
                'quantity' => 1,
                'price' => 1990,
                'variant' => ['color' => 'Black']
            ],
            [
                'product_id' => 18,
                'product_name' => 'Cotton Crew Neck Tee',
                'quantity' => 3,
                'price' => 499,
                'variant' => ['size' => 'L', 'color' => 'White']
            ]
        ],
        'subtotal' => 3487,
        'shipping' => 0,
        'tax' => 349,
        'discount' => 0,
        'total' => 3836,
        'tracking_number' => null,
        'estimated_delivery' => '2024-12-20'
    ],
    [
        'order_id' => 'ORD-2024-003',
        'user_id' => 1,
        'date' => '2024-12-10',
        'status' => 'delivered',
        'payment_method' => 'Credit Card',
        'shipping_address' => [
            'name' => 'John Doe',
            'address' => '123 Main Street, Apt 4B',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'phone' => '+91 98765 43210'
        ],
        'items' => [
            [
                'product_id' => 2,
                'product_name' => 'Laptop Pro',
                'quantity' => 1,
                'price' => 50999,
                'variant' => ['color' => 'Silver', 'storage' => '512GB']
            ],
            [
                'product_id' => 6,
                'product_name' => 'Wireless Mouse',
                'quantity' => 1,
                'price' => 799,
                'variant' => ['color' => 'White']
            ]
        ],
        'subtotal' => 51798,
        'shipping' => 0,
        'tax' => 5180,
        'discount' => 1000,
        'total' => 55978,
        'tracking_number' => 'TRK987654321',
        'estimated_delivery' => '2024-12-14',
        'delivered_date' => '2024-12-14'
    ]
];

function getOrderById($orderId) {
    global $orders;
    foreach ($orders as $order) {
        if ($order['order_id'] === $orderId) {
            return $order;
        }
    }
    return null;
}

function getUserOrders($userId) {
    global $orders;
    return array_filter($orders, function($order) use ($userId) {
        return $order['user_id'] == $userId;
    });
}

function getOrdersByStatus($status) {
    global $orders;
    return array_filter($orders, function($order) use ($status) {
        return $order['status'] === $status;
    });
}

function getOrderStats($userId) {
    $userOrders = getUserOrders($userId);
    
    $stats = [
        'total' => count($userOrders),
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0
    ];
    
    foreach ($userOrders as $order) {
        if (isset($stats[$order['status']])) {
            $stats[$order['status']]++;
        }
    }
    
    return $stats;
}
?>