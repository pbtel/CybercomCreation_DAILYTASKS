<?php
$pageTitle = "My Orders";
require_once 'includes/header.php';
require_once 'includes/orders.php';

// For demo, using user_id = 1
$userId = 1;
$userOrders = getUserOrders($userId);
$stats = getOrderStats($userId);
?>

    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 2rem;">My Orders</h1>

        <!-- ORDER STATS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: white; padding: 2rem; border-radius: 16px; text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary);"><?php echo $stats['total']; ?></div>
                <div style="color: var(--text-secondary); font-weight: 600; margin-top: 0.5rem;">Total Orders</div>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 16px; text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 800; color: #f59e0b;"><?php echo $stats['processing']; ?></div>
                <div style="color: var(--text-secondary); font-weight: 600; margin-top: 0.5rem;">Processing</div>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 16px; text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 800; color: #3b82f6;"><?php echo $stats['shipped']; ?></div>
                <div style="color: var(--text-secondary); font-weight: 600; margin-top: 0.5rem;">Shipped</div>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 16px; text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);"><?php echo $stats['delivered']; ?></div>
                <div style="color: var(--text-secondary); font-weight: 600; margin-top: 0.5rem;">Delivered</div>
            </div>
        </div>

        <!-- ORDERS LIST -->
        <?php if (empty($userOrders)): ?>
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">📦</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">No orders yet</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">Start shopping to see your orders here</p>
                <a href="products.php" style="display: inline-block; padding: 1rem 2.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-decoration: none; border-radius: 12px; font-weight: 700;">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($userOrders as $order): ?>
            <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem;">
                <!-- Order Header -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid var(--border);">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">
                            Order <?php echo $order['order_id']; ?>
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9375rem;">
                            Placed on <?php echo date('d M, Y', strtotime($order['date'])); ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="display: inline-block; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.875rem; 
                            <?php
                            $statusColors = [
                                'processing' => 'background: rgba(245, 158, 11, 0.1); color: #f59e0b;',
                                'shipped' => 'background: rgba(59, 130, 246, 0.1); color: #3b82f6;',
                                'delivered' => 'background: rgba(16, 185, 129, 0.1); color: #10b981;',
                                'cancelled' => 'background: rgba(239, 68, 68, 0.1); color: #ef4444;'
                            ];
                            echo $statusColors[$order['status']];
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem;">
                            ₹<?php echo number_format($order['total']); ?>
                        </p>
                    </div>
                </div>

                <!-- Order Items -->
                <div style="margin-bottom: 1.5rem;">
                    <?php foreach ($order['items'] as $item): ?>
                        <?php $product = getProductById($item['product_id']); ?>
                        <div style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border);">
                            <div style="width: 80px; height: 80px; background: var(--bg-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                                <?php echo $product ? $product['image'] : '📦'; ?>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Quantity: <?php echo $item['quantity']; ?></p>
                                <?php if (!empty($item['variant'])): ?>
                                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                                        <?php foreach ($item['variant'] as $type => $value): ?>
                                            <?php echo ucfirst($type); ?>: <?php echo $value; ?> &nbsp;
                                        <?php endforeach; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-weight: 700; color: var(--primary);">₹<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Actions -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <?php if ($order['status'] === 'shipped' || $order['status'] === 'delivered'): ?>
                        <?php if ($order['tracking_number']): ?>
                            <a href="#" style="padding: 0.75rem 1.5rem; border: 2px solid var(--primary); color: var(--primary); text-decoration: none; border-radius: 8px; font-weight: 600;">
                                Track Order (<?php echo $order['tracking_number']; ?>)
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] === 'delivered'): ?>
                        <a href="#" style="padding: 0.75rem 1.5rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                            Leave Review
                        </a>
                        <a href="#" style="padding: 0.75rem 1.5rem; border: 2px solid var(--accent); color: var(--accent); text-decoration: none; border-radius: 8px; font-weight: 600;">
                            Reorder
                        </a>
                    <?php endif; ?>

                    <?php if ($order['status'] === 'processing'): ?>
                        <a href="#" style="padding: 0.75rem 1.5rem; border: 2px solid #ef4444; color: #ef4444; text-decoration: none; border-radius: 8px; font-weight: 600;">
                            Cancel Order
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($order['status'] === 'shipped' && isset($order['estimated_delivery'])): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: 8px;">
                        <p style="color: #3b82f6; font-weight: 600;">
                            📦 Estimated Delivery: <?php echo date('d M, Y', strtotime($order['estimated_delivery'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php require_once 'includes/footer.php'; ?>
