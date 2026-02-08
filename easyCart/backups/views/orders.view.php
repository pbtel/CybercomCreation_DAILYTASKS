<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">

    <h1 class="page-title">My Orders</h1>

    <!-- ORDER STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-primary"><?php echo $stats['total']; ?>
            </div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-success">
                &#8377;<?php echo number_format($stats['total_spent']); ?>
            </div>
            <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-warning"><?php echo $stats['processing']; ?></div>
            <div class="stat-label">Processing</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-info"><?php echo $stats['shipped']; ?></div>
            <div class="stat-label">Shipped</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-accent"><?php echo $stats['delivered']; ?>
            </div>
            <div class="stat-label">Delivered</div>
        </div>
    </div>

    <!-- SPENDING CHART -->
    <div class="chart-card">
        <h3 class="chart-title">Spending History</h3>
        <div class="chart-container">
            <canvas id="spendingChart"></canvas>
        </div>
    </div>

    <!-- ORDERS LIST -->
    <?php if (empty($userOrders)): ?>
        <div class="empty-state">
            <div class="empty-icon">&#128230;</div>
            <h2 class="empty-text">No orders yet</h2>
            <p class="empty-subtext">Start shopping to see your orders here</p>
            <a href="<?= BASE_URL ?>/products" class="btn-gradient">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($userOrders as $order): ?>
            <?php
            // Get full order details including shipping info
            $fullOrder = getOrderById($order['order_id']);
            $shippingInfo = $fullOrder['shipping'] ?? null;
            $billingInfo = $fullOrder['billing'] ?? null;
            ?>
            <div class="order-card">
                <!-- Order Header - Always Visible -->
                <div class="order-header">
                    <div style="flex: 1; min-width: 250px;">
                        <h3 class="order-title">
                            Order #<?php echo htmlspecialchars($order['order_number']); ?>
                        </h3>
                        <p class="order-meta">
                            <strong>Order ID:</strong> <?php echo $order['order_id']; ?>
                        </p>
                        <p class="order-meta">
                            <strong>Order Date:</strong> <?php echo date('d M, Y h:i A', strtotime($order['created_at'])); ?>
                        </p>
                        <?php if ($shippingInfo): ?>
                            <p class="order-meta">
                                <strong>Shipping Type:</strong>
                                <span class="shipping-badge">
                                    <?php echo htmlspecialchars(ucfirst($shippingInfo['shipping_method'] ?? 'Standard')); ?>
                                </span>
                                <?php if ($shippingInfo['shipping_type']): ?>
                                    <span style="color: #6b7280; font-size: 0.875rem;">
                                        (<?php echo htmlspecialchars(ucfirst($shippingInfo['shipping_type'])); ?>)
                                    </span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div style="text-align: right;">
                        <div class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                        <p class="order-amount">
                            &#8377;<?php echo number_format($order['final_amount']); ?>
                        </p>
                    </div>
                </div>

                <!-- View Details Button -->
                <div style="margin-top: 1rem;">
                    <button onclick="toggleOrderDetails(<?php echo $order['order_id']; ?>)" class="view-details-btn">
                        View Order Details
                        <span id="details-icon-<?php echo $order['order_id']; ?>">&#9660;</span>
                    </button>
                </div>

                <!-- Order Details - Hidden by Default -->
                <div id="order-details-<?php echo $order['order_id']; ?>" class="order-details-panel">

                    <!-- Order Items -->
                    <div style="margin-bottom: 1.5rem;">
                        <h4 class="section-title">Order Items (<?php echo count($order['items']); ?>)</h4>
                        <?php foreach ($order['items'] as $item): ?>
                            <?php $product = getProductById($item['product_id']); ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <?php echo $product ? $product['image'] : '&#128230;'; ?>
                                </div>
                                <div class="item-info">
                                    <h4 class="item-name">
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                    </h4>
                                    <p class="item-meta">Quantity: <?php echo $item['quantity']; ?></p>
                                    <?php if (!empty($item['variant'])): ?>
                                        <p class="item-meta">
                                            <?php foreach ($item['variant'] as $type => $value): ?>
                                                <?php echo ucfirst($type); ?>: <?php echo $value; ?> &nbsp;
                                            <?php endforeach; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="item-price-block">
                                    <p class="item-total">
                                        &#8377;<?php echo number_format($item['unit_price'] * $item['quantity']); ?></p>
                                    <p class="item-meta">
                                        &#8377;<?php echo number_format($item['unit_price']); ?> each
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Breakup -->
                    <div class="price-breakup">
                        <h4 class="section-title">Price Details</h4>

                        <div class="price-row">
                            <span style="color: var(--text-secondary);">Subtotal:</span>
                            <span style="font-weight: 600;">&#8377;<?php echo number_format($order['subtotal']); ?></span>
                        </div>

                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="price-row">
                                <span class="text-success" style="font-weight: 600;">
                                    Discount
                                    <?php if ($billingInfo && $billingInfo['coupon_code']): ?>(<?php echo htmlspecialchars($billingInfo['coupon_code']); ?>)<?php endif; ?>:
                                </span>
                                <span class="text-success"
                                    style="font-weight: 600;">-&#8377;<?php echo number_format($order['discount_amount']); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="price-row">
                            <span style="color: var(--text-secondary);">Shipping Cost:</span>
                            <span style="font-weight: 600;">&#8377;<?php echo number_format($order['shipping_cost']); ?></span>
                        </div>

                        <div class="price-row">
                            <span style="color: var(--text-secondary);">Tax (18% GST):</span>
                            <span style="font-weight: 600;">&#8377;<?php echo number_format($order['tax']); ?></span>
                        </div>

                        <div class="price-row total">
                            <span>Total Amount:</span>
                            <span class="text-primary">&#8377;<?php echo number_format($order['final_amount']); ?></span>
                        </div>

                        <?php if ($billingInfo): ?>
                            <div class="payment-info">
                                <p class="order-meta">
                                    <strong>Payment Method:</strong> <?php echo htmlspecialchars($billingInfo['payment_method']); ?>
                                </p>
                                <p class="order-meta">
                                    <strong>Payment Status:</strong>
                                    <span
                                        class="<?php echo $billingInfo['payment_status'] === 'completed' ? 'text-success' : 'text-warning'; ?>">
                                        <?php echo ucfirst($billingInfo['payment_status']); ?>
                                    </span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Actions -->
                    <div class="action-buttons">
                        <?php if ($order['status'] === 'shipped' || $order['status'] === 'delivered'): ?>
                            <?php if ($order['tracking_number']): ?>
                                <a href="#" class="btn-outlined">
                                    Track Order (<?php echo $order['tracking_number']; ?>)
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'delivered'): ?>
                            <a href="#" class="btn-primary">
                                Leave Review
                            </a>
                            <a href="#" class="btn-accent-outlined">
                                Reorder
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['status'] === 'shipped' && isset($order['estimated_delivery'])): ?>
                        <div class="delivery-estimate">
                            <p class="delivery-text">
                                &#128230; Estimated Delivery: <?php echo date('d M, Y', strtotime($order['estimated_delivery'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle order details visibility
    function toggleOrderDetails(orderId) {
        const detailsDiv = document.getElementById('order-details-' + orderId);
        const icon = document.getElementById('details-icon-' + orderId);

        if (detailsDiv.style.display === 'none') {
            detailsDiv.style.display = 'block';
            icon.innerHTML = '&#9650;';
        } else {
            detailsDiv.style.display = 'none';
            icon.innerHTML = '&#9660;';
        }
    }

    // Initialize Spending Chart
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('spendingChart');
        if (!ctx) return;

        fetch(BASE_URL + '/api/chart-data-ajax.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.labels.length > 0) {
                    new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Order Amount',
                                data: data.data,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#6366f1',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return ' \u20B9' + context.parsed.y.toLocaleString('en-IN');
                                        }
                                    },
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    padding: 12,
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { borderDash: [2, 4], color: '#f3f4f6' },
                                    ticks: {
                                        callback: value => '\u20B9' + value,
                                        font: { size: 11 }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11 } }
                                }
                            }
                        }
                    });
                } else {
                    // Show empty state if no data
                    ctx.parentElement.innerHTML = '<div style="display: flex; height: 100%; align-items: center; justify-content: center; color: var(--text-secondary);">No spending data available yet</div>';
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                ctx.parentElement.innerHTML = '<div style="display: flex; height: 100%; align-items: center; justify-content: center; color: var(--danger);">Failed to load chart</div>';
            });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>