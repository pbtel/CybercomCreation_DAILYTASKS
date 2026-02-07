<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mb-3rem">
    <div class="flex-between mb-2">
        <div>
            <a href="<?php echo BASE_URL; ?>/orders" class="text-muted-sm td-n flex-center-gap-0-5 mb-0-5">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Orders
            </a>
            <h1 class="section-title-lg mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
            <p class="text-muted-sm">Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
            </p>
        </div>
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
    </div>

    <div class="grid-2-1">
        <!-- Left Column: Products -->
        <div class="card">
            <h3 class="fs-1-25 font-700 border-b pb-1 mb-1-5">
                Items in Order
            </h3>

            <div class="order-item-list">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="order-item-row p-1-0 border-b-light">
                        <div class="order-item-thumb-lg">
                            <?php if (isset($item['image']) && strpos($item['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/public/' . $item['image']; ?>"
                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php else: ?>
                                <span class="fs-1-5">📦</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 pl-1-5">
                            <h4 class="font-600 mb-0-25 fs-1-1"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                            <?php if (!empty($item['variant'])): ?>
                                <div class="flex-gap-1 mb-0-5">
                                    <?php foreach ($item['variant'] as $type => $value): ?>
                                        <span class="text-muted-sm fs-0-875">
                                            <strong class="color-text-main"><?php echo ucfirst($type); ?>:</strong>
                                            <?php echo $value; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <p class="text-muted-sm">₹<?php echo number_format($item['price']); ?> ×
                                <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-700 color-primary fs-1-1">
                                ₹<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Column: Summary & Address -->
        <div class="flex-col-gap-2">
            <!-- Order Summary -->
            <div class="card bg-card border-all">
                <h3 class="fs-1-1 font-700 mb-1-5">Order Summary</h3>

                <div class="flex-col-gap-1">
                    <div class="flex-between">
                        <span class="text-muted">Subtotal</span>
                        <span class="font-500">₹<?php echo number_format($order['subtotal']); ?></span>
                    </div>
                    <div class="flex-between">
                        <span class="text-muted">Shipping
                            (<?php echo htmlspecialchars($order['shipping_type']); ?>)</span>
                        <span class="font-500">₹<?php echo number_format($order['shipping_cost']); ?></span>
                    </div>
                    <div class="flex-between">
                        <span class="text-muted">Tax (18%)</span>
                        <span class="font-500">₹<?php echo number_format($order['tax']); ?></span>
                    </div>

                    <?php if ($order['discount'] > 0): ?>
                        <div class="flex-between color-success-dark">
                            <span>
                                Discount
                                <?php if (!empty($order['applied_coupon'])): ?>
                                    <span class="bg-success-soft p-2-6 border-radius-4 fs-0-75 font-700 ml-0-5">
                                        <?php echo htmlspecialchars($order['applied_coupon']); ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <span class="font-600">-₹<?php echo number_format($order['discount']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="border-t-2 mt-0-5 pt-1 flex-between items-center">
                        <span class="font-700 fs-1-1">Total Amount</span>
                        <span
                            class="font-800 fs-1-5 color-primary">₹<?php echo number_format($order['final_amount']); ?></span>
                    </div>
                </div>

                <div class="mt-2 pt-1-5 border-t-1">
                    <p class="text-muted-sm mb-0-5">Payment Method</p>
                    <p class="font-600 tt-u"><?php echo str_replace('_', ' ', $order['payment_method']); ?></p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card">
                <h3 class="fs-1-1 font-700 mb-1">Shipping Address</h3>
                <?php if ($order['address']): ?>
                    <p class="font-600 mb-0-5"><?php echo htmlspecialchars($order['address']['full_name']); ?></p>
                    <p class="text-muted-sm lh-1-6">
                        <?php echo htmlspecialchars($order['address']['address_line1']); ?><br>
                        <?php if (isset($order['address']['address_line2']) && $order['address']['address_line2']): ?>
                            <?php echo htmlspecialchars($order['address']['address_line2']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($order['address']['city']); ?>,
                        <?php echo htmlspecialchars($order['address']['state']); ?> -
                        <?php echo htmlspecialchars($order['address']['postal_code']); ?><br>
                        <?php echo htmlspecialchars($order['address']['country']); ?>
                    </p>
                    <div class="mt-1 flex-col-gap-0-25">
                        <p class="text-muted-sm"><strong>Phone:</strong>
                            <?php echo htmlspecialchars($order['address']['phone']); ?></p>
                        <p class="text-muted-sm"><strong>Email:</strong>
                            <?php echo htmlspecialchars($order['address']['email']); ?></p>
                    </div>
                <?php else: ?>
                    <p class="text-muted-sm">Address information not available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>