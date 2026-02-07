<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container">
        <h1 class="section-title-lg mb-1">Checkout</h1>
        <p class="text-muted-sm mb-2rem">Complete your order</p>

        <div class="checkout-grid">
            <!-- CHECKOUT FORM -->
            <div>
                <form action="<?php echo BASE_URL; ?>/checkout/place" method="POST">
                    <!-- SHIPPING INFO -->
                    <div class="card mb-2">
                        <h2 class="card-title-lg mb-1-5">Shipping Information</h2>
                        
                        <div class="form-row">
                            <div>
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" required class="input-text">
                            </div>
                            <div>
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" required class="input-text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" required class="input-text" value="<?php echo Session::get('user')['email'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone *</label>
                            <input type="tel" name="phone" required class="input-text">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Address *</label>
                            <input type="text" name="address" required class="input-text">
                        </div>

                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">City *</label>
                                <input type="text" name="city" required class="input-text">
                            </div>
                            <div>
                                <label class="form-label">State *</label>
                                <input type="text" name="state" required class="input-text">
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">PIN Code *</label>
                                <input type="text" name="pincode" required class="input-text">
                            </div>
                            <div>
                                <label class="form-label">Country *</label>
                                <select name="country" required class="input-select">
                                    <option value="">Select Country</option>
                                    <option value="IN" selected>India</option>
                                    <option value="US">United States</option>
                                    <option value="UK">United Kingdom</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SHIPPING METHOD -->
                    <div class="card mb-2">
                        <h2 class="card-title-lg mb-1-5">Shipping Method</h2>
                        
                        <?php 
                        $shippingModel = new ShippingModel();
                        $methods = ['standard', 'express', 'whiteglove', 'freight'];
                        foreach ($methods as $method): 
                            $available = in_array($method, $availableShippingMethods);
                        ?>
                        <label class="shipping-option <?php echo !$available ? 'disabled' : ''; ?>" id="label-<?php echo $method; ?>">
                            <div class="flex-start-gap-0-75">
                                <input type="radio" name="shipping_method" value="<?php echo $method; ?>" 
                                       <?php echo ($selectedShippingMethod === $method) ? 'checked' : ''; ?> 
                                       <?php echo !$available ? 'disabled' : ''; ?> 
                                       class="mt-0-25" onchange="updateOrderSummary()">
                                <div class="flex-1">
                                    <div class="flex-center-gap-0-5 mb-0-5">
                                        <span class="fs-1-25">
                                            <?php 
                                            if ($method === 'standard') echo '📦';
                                            elseif ($method === 'express') echo '⚡';
                                            elseif ($method === 'whiteglove') echo '🏆';
                                            elseif ($method === 'freight') echo '🚚';
                                            ?>
                                        </span>
                                        <strong class="fs-1-0625"><?php echo ucfirst($method); ?> Shipping</strong>
                                        <?php if ($available): ?>
                                            <span class="shipping-badge bg-primary-soft color-primary">
                                                <?php 
                                                if ($method === 'standard') echo 'MOST POPULAR';
                                                elseif ($method === 'express') echo 'FAST';
                                                elseif ($method === 'whiteglove') echo 'PREMIUM';
                                                elseif ($method === 'freight') echo 'HEAVY ITEMS';
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="shipping-badge badge-grey-soft">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="db color-text-secondary fs-0-875 mb-0-5">
                                        <?php echo $shippingModel->getMethodDescription($method, $subtotalAfterCoupon); ?>
                                    </span>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>

                        <div class="alert-info-soft mt-1">
                            <p class="fs-0-875 color-text-secondary m-0">
                                <strong class="color-primary">ℹ️ Note:</strong> All delivery times are estimated and may vary based on your location. Tracking information will be provided once your order ships.
                            </p>
                        </div>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="card mb-2">
                        <h2 class="card-title-lg mb-1-5">Payment Method</h2>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-0-75">
                            <strong>Cash on Delivery</strong>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="upi" class="mr-0-75">
                            <strong>UPI / QR Code</strong>
                        </label>

                        <label class="payment-option last">
                            <input type="radio" name="payment_method" value="card" class="mr-0-75">
                            <strong>Credit / Debit Card</strong>
                        </label>
                    </div>

                    <button type="submit" class="action-button">Place Order</button>
                </form>
            </div>

            <!-- ORDER SUMMARY -->
            <div class="sticky-summary">
                <div class="card">
                    <h2 class="card-title-lg mb-1-5">Order Summary</h2>

                    <div class="scroll-summary">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="product-summary-item">
                            <div class="product-summary-image">
                                <?php if (strpos($item['product']['image'], 'assets/images') === 0): ?>
                                    <img src="<?php echo BASE_URL . '/public/' . $item['product']['image']; ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                                <?php else: ?>
                                    <?php echo $item['product']['image']; ?>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-600 fs-0-9375"><?php echo htmlspecialchars($item['product']['name']); ?></p>
                                <p class="color-text-secondary fs-0-8125">Qty: <?php echo $item['quantity']; ?></p>
                                
                                <p class="font-600 color-primary fs-0-875 mt-0-25">
                                    ₹<?php echo number_format($item['unit_price_discounted']); ?> 
                                    <?php if ($item['discount_percent'] > 0): ?>
                                        <span class="text-muted-sm td-lt fs-0-75">
                                            ₹<?php echo number_format($item['unit_price_original']); ?>
                                        </span>
                                    <?php endif; ?>
                                </p>
                                
                                <p class="font-600 color-primary mt-0-25">₹<?php echo number_format($item['subtotal']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-divider">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal:</span>
                            <span class="summary-value" id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
                        </div>
                        
                        <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                        <div class="summary-row">
                            <span class="font-600 color-accent">Coupon (<?php echo $appliedCoupon['code']; ?>):</span>
                            <span class="font-600 color-accent">-₹<?php echo number_format($couponDiscount); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="summary-row">
                            <span class="summary-label">Shipping:</span>
                            <span class="summary-value" id="summary-shipping">₹<?php echo number_format($shipping); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Tax (18% GST):</span>
                            <span class="summary-value" id="summary-tax">₹<?php echo number_format($tax); ?></span>
                        </div>
                    </div>

                    <div class="total-row">
                        <span>Total:</span>
                        <span class="color-primary" id="summary-total">₹<?php echo number_format($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const API_URL = '<?php echo BASE_URL; ?>/api';
    const subtotalAfterCoupon = <?php echo $subtotalAfterCoupon; ?>;

    function updateOrderSummary() {
        // Get selected shipping method
        const selectedMethod = document.querySelector('input[name="shipping_method"]:checked').value;
        
        // Save shipping method to session via AJAX
        fetch(API_URL + '/shippingMethodUpdate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'shipping_method=' + encodeURIComponent(selectedMethod)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to update shipping method:', data.message);
            }
        });
        
        // Use AJAX to calculate shipping
        fetch(API_URL + '/shippingCalculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'shipping_method=' + encodeURIComponent(selectedMethod)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('summary-shipping').textContent = '₹' + data.shipping_cost.toLocaleString('en-IN');
                document.getElementById('summary-tax').textContent = '₹' + data.tax.toLocaleString('en-IN');
                document.getElementById('summary-total').textContent = '₹' + data.total.toLocaleString('en-IN');
            }
        });
        
        // Update shipping option borders
        document.querySelectorAll('.shipping-option').forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            if (radio.checked) {
                label.classList.add('selected');
                label.classList.remove('shadow-none');
            } else {
                label.classList.remove('selected');
                label.classList.add('shadow-none');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateOrderSummary();
    });
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
