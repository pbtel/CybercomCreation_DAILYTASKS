<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container orders-container">
    <!-- Breadcrumb-style back link -->
    <div class="mb-2">
        <a href="<?php echo BASE_URL; ?>/orders" class="btn-continue-shopping py-0-5 px-1 fs-0-85">
            ← Back to Orders
        </a>
    </div>

    <!-- PREMIUM HEADER CARD -->
    <div class="detail-header-premium">
        <!-- Status Banner -->
        <div class="status-banner <?php echo strtolower($order['status']); ?>">
            <span>Order Status</span>
            <span><?php echo ucfirst($order['status']); ?></span>
        </div>

        <div class="detail-header-content">
            <div class="detail-title-group">
                <p class="text-secondary font-800 uppercase tracking-widest fs-0-75 mb-0-5">Transaction Receipt</p>
                <h1>Order #<?php echo substr($order['order_number'], -12); ?></h1>

                <div class="detail-meta-modern">
                    <div class="meta-item-modern">
                        <label>Order Date</label>
                        <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="meta-item-modern">
                        <label>Customer Name</label>
                        <span><?php echo Session::get('user')['name'] ?? 'Customer'; ?></span>
                    </div>
                    <div class="meta-item-modern">
                        <label>Order Time</label>
                        <span><?php echo date('g:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Print/Download Icon placeholder if needed, otherwise just keep it clean -->
            <div class="align-end">
                <a href="<?php echo BASE_URL; ?>/order/invoice/<?php echo $order['order_id']; ?>" target="_blank"
                    class="btn-update-qty py-0-75 px-1-5 text-decoration-none">
                    🖨️ Print Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="detail-grid-layout">
        <!-- LEFT: Items Card -->
        <div class="order-items-card">
            <h2 class="items-section-title">
                📦 <span>Purchased Items</span>
            </h2>

            <div class="items-list-modern">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="detail-item-row">
                        <!-- Item Image -->
                        <div class="detail-item-image">
                            <?php if (isset($item['image']) && (strpos($item['image'], 'assets/images') === 0 || strpos($item['image'], '/') === 0 || strpos($item['image'], 'http') === 0)): ?>
                                <img src="<?php echo (strpos($item['image'], 'http') === 0) ? $item['image'] : BASE_URL . '/' . ltrim($item['image'], '/'); ?>"
                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php else: ?>
                                <div class="detail-item-emoji"><?php echo $item['image'] ?? '📦'; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Item Details -->
                        <div class="detail-item-info">
                            <h3 class="detail-item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <?php if (!empty($item['variant'])): ?>
                                <div class="flex-gap-0-5 mb-0-5">
                                    <?php foreach ($item['variant'] as $type => $value): ?>
                                        <span class="badge badge-grey-soft fs-0-7">
                                            <strong><?php echo ucfirst($type); ?>:</strong> <?php echo $value; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="detail-item-price-info">
                                ₹<?php echo number_format($item['price']); ?> × <?php echo $item['quantity']; ?>
                            </div>
                        </div>

                        <!-- Subtotal -->
                        <div class="detail-item-subtotal">
                            ₹<?php echo number_format($item['price'] * $item['quantity']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT: Summary & Address Sidebar -->
        <div class="sidebar-sticky">
            <!-- Order Summary Card -->
            <div class="premium-summary-card">
                <h3 class="summary-title-alt">Financial Summary</h3>

                <div class="row-alt">
                    <span class="text-secondary">Subtotal Amount</span>
                    <span class="font-700">₹<?php echo number_format($order['subtotal']); ?></span>
                </div>

                <div class="row-alt">
                    <span class="text-secondary">Processing & Tax</span>
                    <span class="font-700">₹<?php echo number_format($order['tax'] ?? 0); ?></span>
                </div>

                <div class="row-alt">
                    <span class="text-secondary">Shipping Cost</span>
                    <span class="font-700">₹<?php echo number_format($order['shipping_cost'] ?? 0); ?></span>
                </div>

                <?php
                $discount = $order['discount_amount'] ?? $order['discount'] ?? 0;
                if ($discount > 0):
                    ?>
                    <div class="row-alt text-success font-700">
                        <span>Loyalty Savings</span>
                        <span>-₹<?php echo number_format($discount); ?></span>
                    </div>
                <?php endif; ?>

                <div class="row-alt grand-total">
                    <span>Total Paid</span>
                    <span class="amount">₹<?php echo number_format($order['final_amount']); ?></span>
                </div>

                <!-- Payment Badge -->
                <div class="payment-badge-card">
                    <div class="fs-2">🏦</div>
                    <div>
                        <p class="payment-label-wide">Payment Method</p>
                        <strong
                            class="fs-1-1"><?php echo str_replace('_', ' ', strtoupper($order['payment_method'])); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="address-card-premium">
                <h3 class="font-900 fs-1 uppercase tracking-wider mb-1">📍 Destination</h3>
                <?php if ($order['address']): ?>
                    <div class="address-content-alt">
                        <strong><?php echo htmlspecialchars($order['address']['full_name']); ?></strong><br>
                        <?php echo htmlspecialchars($order['address']['address_line1']); ?><br>
                        <?php if (isset($order['address']['address_line2']) && $order['address']['address_line2']): ?>
                            <?php echo htmlspecialchars($order['address']['address_line2']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($order['address']['city']); ?>,
                        <?php echo htmlspecialchars($order['address']['state']); ?> -
                        <span
                            class="font-800"><?php echo htmlspecialchars($order['address']['pincode'] ?? $order['address']['postal_code']); ?></span><br>
                        <?php echo htmlspecialchars($order['address']['country']); ?><br>
                        <div class="mt-1 pt-0-5 border-t">
                            <span class="text-secondary">Contact:</span>
                            <?php echo htmlspecialchars($order['address']['phone']); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-italic text-secondary">Address details missing</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>