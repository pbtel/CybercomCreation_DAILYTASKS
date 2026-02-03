<?php
$pageTitle = "My Orders";
require_once 'includes/header.php';
require_once 'includes/orders.php';

// Phase 7: Enforce login
requireLogin('orders.php');

// Get logged-in user's ID
$userId = isLoggedIn() ? $_SESSION['user']['user_id'] : 0;
$userOrders = getUserOrders($userId);
$stats = getOrderStats($userId);
?>

    <div class="container">
        <h1 class="section-title-lg">My Orders</h1>

        <!-- ORDER STATS -->
        <div class="stat-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #f59e0b;"><?php echo $stats['processing']; ?></div>
                <div class="stat-label">Processing</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #3b82f6;"><?php echo $stats['shipped']; ?></div>
                <div class="stat-label">Shipped</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--accent);"><?php echo $stats['delivered']; ?></div>
                <div class="stat-label">Delivered</div>
            </div>
        </div>

        <!-- ORDERS LIST -->
        <?php if (empty($userOrders)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">No orders yet</h2>
                <p class="text-muted-sm mb-2rem">Start shopping to see your orders here</p>
                <a href="products.php" class="btn-primary-lg">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($userOrders as $order): ?>
            <div class="card" style="margin-bottom: 1.5rem;">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">
                            Order <?php echo $order['order_id']; ?>
                        </h3>
                        <p class="text-muted-sm" style="font-size: 0.9375rem;">
                            Placed on <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="order-status-badge <?php
                             $statusClasses = [
                                'pending'    => 'status-pending',
                                'processing' => 'status-processing',
                                'shipped'    => 'status-shipped',
                                'delivered'  => 'status-delivered',
                                'cancelled'  => 'status-cancelled'
                            ];
                            echo $statusClasses[$order['status']];
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem;">
                            ₹<?php echo number_format($order['final_amount']); ?>
                        </p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-item-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <?php $product = getProductById($item['product_id']); ?>
                        <div class="order-item-row">
                            <div class="order-item-thumb">
                                <?php echo $product ? $product['image'] : '📦'; ?>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p class="text-muted-sm">Quantity: <?php echo $item['quantity']; ?></p>
                                <?php if (!empty($item['variant'])): ?>
                                    <p class="text-muted-sm">
                                        <?php foreach ($item['variant'] as $type => $value): ?>
                                            <?php echo ucfirst($type); ?>: <?php echo $value; ?> &nbsp;
                                        <?php endforeach; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <p style="font-weight: 700; color: var(--primary);">₹<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Actions -->
                <div class="btn-group">
                    <?php if ($order['status'] === 'shipped' || $order['status'] === 'delivered'): ?>
                        <?php if ($order['tracking_number']): ?>
                            <a href="#" class="btn-outline-primary">
                                Track Order (<?php echo $order['tracking_number']; ?>)
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] === 'delivered'): ?>
                        <a href="#" class="btn-primary" style="padding: 0.75rem 1.5rem !important;">
                            Leave Review
                        </a>
                        <a href="#" class="btn-outline-accent">
                            Reorder
                        </a>
                    <?php endif; ?>

                    <?php if ($order['status'] === 'processing'): ?>
                        <a href="#" class="btn-outline-danger">
                            Cancel Order
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($order['status'] === 'shipped' && isset($order['estimated_delivery'])): ?>
                    <div class="alert-info-soft">
                        <p>
                            📦 Estimated Delivery: <?php echo date('d M, Y', strtotime($order['estimated_delivery'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php require_once 'includes/footer.php'; ?>
