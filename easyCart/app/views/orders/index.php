<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container orders-container">
    <div class="flex-between mb-2">
        <div>
            <h1 class="orders-title">My Orders</h1>
            <p class="text-secondary">Track and manage your recent purchases</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/products" class="btn-continue-shopping">
            🛍️ Continue Shopping
        </a>
    </div>

    <!-- ORDER STATS GRID -->
    <div class="order-stats-grid">
        <div class="stat-card-premium">
            <span class="stat-icon">📦</span>
            <div class="stat-number"><?php echo $stats['total']; ?></div>
            <div class="stat-label-modern">Total Orders</div>
        </div>
        <div class="stat-card-premium">
            <span class="stat-icon">⏳</span>
            <div class="stat-number color-primary"><?php echo $stats['processing']; ?></div>
            <div class="stat-label-modern">In Progress</div>
        </div>
        <div class="stat-card-premium">
            <span class="stat-icon">🚛</span>
            <div class="stat-number color-accent"><?php echo $stats['shipped']; ?></div>
            <div class="stat-label-modern">In Transit</div>
        </div>
        <div class="stat-card-premium">
            <span class="stat-icon">✅</span>
            <div class="stat-number text-success"><?php echo $stats['delivered']; ?></div>
            <div class="stat-label-modern">Delivered</div>
        </div>
    </div>

    <!-- ORDERS LIST -->
    <?php if (empty($orders)): ?>
        <div class="empty-cart-container">
            <div class="empty-cart-icon">🔎</div>
            <h2 class="empty-cart-text">No orders found</h2>
            <p class="empty-cart-subtext">Looks like you haven't placed any orders yet. Try our latest electronics!</p>
            <a href="<?php echo BASE_URL; ?>/products" class="checkout-btn"
                style="max-width: 300px; margin: 0 auto; display: block;">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div class="mb-2rem">
            <?php require __DIR__ . '/../partials/pagination.php'; ?>
        </div>

        <?php foreach ($orders as $order):
            $orderDate = date('M d, Y', strtotime($order['created_at']));
            $discount = $order['discount_amount'] ?? $order['discount'] ?? 0;
            ?>
            <div class="order-card-modern">
                <!-- Premium Header -->
                <div class="order-header-modern">
                    <div class="order-meta-info">
                        <div class="meta-group">
                            <label>Order ID</label>
                            <span>#<?php echo substr($order['order_number'], -8); ?></span>
                        </div>
                        <div class="meta-group">
                            <label>Date Placed</label>
                            <span><?php echo $orderDate; ?></span>
                        </div>
                        <div class="meta-group">
                            <label>Ship To</label>
                            <span><?php echo Session::get('user')['name'] ?? 'Customer'; ?></span>
                        </div>
                    </div>

                    <div class="status-badge-modern badge-<?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </div>
                </div>

                <!-- Order Content -->
                <div class="order-body-modern">
                    <div class="order-items-preview">
                        <?php
                        $previewItems = array_slice($order['items'], 0, 2);
                        foreach ($previewItems as $item):
                            ?>
                            <div class="order-item-modern">
                                <div class="order-item-img-box">
                                    <?php if (isset($item['image']) && (strpos($item['image'], 'assets/images') === 0 || strpos($item['image'], '/') === 0 || strpos($item['image'], 'http') === 0)): ?>
                                        <img src="<?php echo (strpos($item['image'], 'http') === 0) ? $item['image'] : BASE_URL . '/' . ltrim($item['image'], '/'); ?>"
                                            alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <?php else: ?>
                                        <div class="order-item-emoji"><?php echo $item['image'] ?? '📦'; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-info-modern">
                                    <div class="order-item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div class="order-item-qty">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="order-item-price">
                                    ₹<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (count($order['items']) > 2): ?>
                            <div class="text-center py-0-5 border-t mt-0-5">
                                <span class="text-secondary fs-0-85 font-600">
                                    + <?php echo count($order['items']) - 2; ?> more items in this order
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Premium Footer -->
                <div class="order-footer-modern">
                    <div class="total-amount-box">
                        <label>Order Total:</label>
                        <span class="amount">₹<?php echo number_format($order['final_amount']); ?></span>
                        <?php if ($discount > 0): ?>
                            <span class="text-success fs-0-8 font-700 ml-0-5">
                                (Saved ₹<?php echo number_format($discount); ?>)
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex-gap-1">
                        <a href="<?php echo BASE_URL; ?>/order/<?php echo $order['order_id']; ?>" class="btn-view-order">
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php require __DIR__ . '/../partials/pagination.php'; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>