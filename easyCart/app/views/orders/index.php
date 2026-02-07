<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="section-title-lg">My Orders</h1>

    <!-- ORDER STATS -->
    <div class="stat-container">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-value color-warning"><?php echo $stats['processing']; ?></div>
            <div class="stat-label">Processing</div>
        </div>
        <div class="stat-card">
            <div class="stat-value color-info"><?php echo $stats['shipped']; ?></div>
            <div class="stat-label">Shipped</div>
        </div>
        <div class="stat-card">
            <div class="stat-value color-accent"><?php echo $stats['delivered']; ?></div>
            <div class="stat-label">Delivered</div>
        </div>
    </div>

    <!-- ORDERS LIST -->
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h2 class="fs-1-5 mb-1">No orders yet</h2>
            <p class="text-muted-sm mb-2rem">Start shopping to see your orders here</p>
            <a href="<?php echo BASE_URL; ?>/products" class="btn-primary-lg">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-1-5">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <h3 class="mb-0-5">
                            Order #<?php echo $order['order_number']; ?>
                        </h3>
                        <p class="text-muted-sm">
                            Placed on <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="order-status-badge <?php
                        $statusClasses = [
                            'pending' => 'status-pending',
                            'processing' => 'status-processing',
                            'shipped' => 'status-shipped',
                            'delivered' => 'status-delivered'
                        ];
                        echo $statusClasses[$order['status']] ?? 'status-pending';
                        ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                        <p class="product-current-price mt-0-5">
                            ₹<?php echo number_format($order['final_amount']); ?>
                        </p>
                        <?php if ($order['discount'] > 0): ?>
                            <p class="text-success-sm font-500 fs-0-875">
                                <?php if (!empty($order['applied_coupon'])): ?>
                                    <span class="badge badge-success-soft fs-0-75">
                                        <?php echo htmlspecialchars($order['applied_coupon']); ?>
                                    </span>
                                <?php endif; ?>
                                Discount: -₹<?php echo number_format($order['discount']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items (Limited Preview) -->
                <div class="order-item-list">
                    <?php
                    $previewItems = array_slice($order['items'], 0, 2);
                    foreach ($previewItems as $item):
                        ?>
                        <div class="order-item-row">
                            <div class="order-item-thumb">
                                <?php if (isset($item['image']) && strpos($item['image'], 'assets/images') === 0): ?>
                                    <img src="<?php echo BASE_URL . '/public/' . $item['image']; ?>"
                                        alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <?php else: ?>
                                    <span class="fs-1-5">📦</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-600 mb-0-25"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p class="text-muted-sm">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-700 color-primary">₹<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($order['items']) > 2): ?>
                        <p class="text-center text-muted-sm mt-0-5">
                            and <?php echo count($order['items']) - 2; ?> more items...
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Order Actions -->
                <div class="card-footer border-t mt-1-5 pt-1-5 flex-end">
                    <a href="<?php echo BASE_URL; ?>/order/<?php echo $order['order_id']; ?>"
                        class="btn-outline-primary btn-padding-lg font-600">
                        View Details
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>