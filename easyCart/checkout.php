<?php
$pageTitle = "Checkout";
require_once 'includes/header.php';
require_once 'includes/shipping.php';

$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();

// Apply coupon discount if available
$appliedCoupon = getAppliedCoupon();
$couponDiscount = 0;
if ($appliedCoupon) {
    $couponDiscount = calculateCouponDiscount($subtotal);
}
$subtotalAfterCoupon = $subtotal - $couponDiscount;

// Get available shipping methods and auto-select default if needed
$availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotalAfterCoupon);
$selectedShippingMethod = getOrSetDefaultShippingMethod();

// Validate selected method is available, if not reset to default
if (!in_array($selectedShippingMethod, $availableShippingMethods)) {
    $selectedShippingMethod = getDefaultShippingMethod($cartItems, $subtotalAfterCoupon);
    setSelectedShippingMethod($selectedShippingMethod);
}

$shipping = calculateShippingCost($subtotalAfterCoupon, $selectedShippingMethod);
$tax = calculateTax($subtotalAfterCoupon, $shipping);
$total = calculateOrderTotal($subtotalAfterCoupon, $shipping, $tax);

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Check if user is logged in, if not redirect to login with return URL
requireLogin('checkout.php');
?>

    <div class="container">
        <h1 class="section-title-lg mb-1">Checkout</h1>
        <p class="text-muted-sm mb-2rem">Complete your order</p>

        <div class="checkout-grid">
            <!-- CHECKOUT FORM -->
            <div>
                <form action="order-place.php" method="POST">
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
                            <input type="email" name="email" required class="input-text">
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
                        
                        <!-- Standard Shipping -->
                        <?php $standardAvailable = in_array('standard', $availableShippingMethods); ?>
                        <label class="shipping-option <?php echo !$standardAvailable ? 'disabled' : ''; ?>" id="label-standard">
                            <div class="flex-start-gap-0-75">
                                <input type="radio" name="shipping_method" value="standard" <?php echo ($selectedShippingMethod === 'standard') ? 'checked' : ''; ?> <?php echo !$standardAvailable ? 'disabled' : ''; ?> class="mt-0-25" onchange="updateOrderSummary()">
                                <div class="flex-1">
                                    <div class="flex-center-gap-0-5 mb-0-5">
                                        <span class="fs-1-25">📦</span>
                                        <strong class="fs-1-0625">Standard Shipping</strong>
                                        <?php if ($standardAvailable): ?>
                                            <span class="shipping-badge bg-primary-soft color-primary">MOST POPULAR</span>
                                        <?php else: ?>
                                            <span class="shipping-badge badge-grey-soft">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="db color-text-secondary fs-0-875 mb-0-5"><?php echo getShippingMethodDescription('standard', $subtotal); ?></span>
                                    <?php if ($standardAvailable): ?>
                                        <div class="shipping-promo">
                                            <span>💰 Most economical option</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Express Shipping -->
                        <?php $expressAvailable = in_array('express', $availableShippingMethods); ?>
                        <label class="shipping-option <?php echo !$expressAvailable ? 'disabled' : ''; ?>" id="label-express">
                            <div class="flex-start-gap-0-75">
                                <input type="radio" name="shipping_method" value="express" <?php echo ($selectedShippingMethod === 'express') ? 'checked' : ''; ?> <?php echo !$expressAvailable ? 'disabled' : ''; ?> class="mt-0-25" onchange="updateOrderSummary()">
                                <div class="flex-1">
                                    <div class="flex-center-gap-0-5 mb-0-5">
                                        <span class="fs-1-25">⚡</span>
                                        <strong class="fs-1-0625">Express Shipping</strong>
                                        <?php if ($expressAvailable): ?>
                                            <span class="shipping-badge bg-warning-soft color-warning">FAST</span>
                                        <?php else: ?>
                                            <span class="shipping-badge badge-grey-soft">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="db color-text-secondary fs-0-875 mb-0-5"><?php echo getShippingMethodDescription('express', $subtotal); ?></span>
                                    <?php if ($expressAvailable): ?>
                                        <?php 
                                        $expressCost = calculateShippingCost($subtotal, 'express');
                                        if ($expressCost < 80) {
                                            $savings = 80 - $expressCost;
                                            echo '<div class="shipping-promo bg-warning-soft color-warning">';
                                            echo '<span>🎉 Save ₹' . number_format($savings) . ' with your cart value!</span>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="shipping-promo bg-primary-soft color-primary">';
                                            echo '<span>⚡ Faster delivery guaranteed</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- White Glove Delivery -->
                        <?php $whitegloveAvailable = in_array('whiteglove', $availableShippingMethods); ?>
                        <label class="shipping-option <?php echo !$whitegloveAvailable ? 'disabled' : ''; ?>" id="label-whiteglove">
                            <div class="flex-start-gap-0-75">
                                <input type="radio" name="shipping_method" value="whiteglove" <?php echo ($selectedShippingMethod === 'whiteglove') ? 'checked' : ''; ?> <?php echo !$whitegloveAvailable ? 'disabled' : ''; ?> class="mt-0-25" onchange="updateOrderSummary()">
                                <div class="flex-1">
                                    <div class="flex-center-gap-0-5 mb-0-5">
                                        <span class="fs-1-25">🏆</span>
                                        <strong class="fs-1-0625">White Glove Delivery</strong>
                                        <?php if ($whitegloveAvailable): ?>
                                            <span class="shipping-badge bg-accent-soft color-accent">PREMIUM</span>
                                        <?php else: ?>
                                            <span class="shipping-badge badge-grey-soft">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="db color-text-secondary fs-0-875 mb-0-5"><?php echo getShippingMethodDescription('whiteglove', $subtotal); ?></span>
                                    <?php if ($whitegloveAvailable): ?>
                                        <?php 
                                        $whiteGloveCost = calculateShippingCost($subtotal, 'whiteglove');
                                        if ($whiteGloveCost < 150) {
                                            $savings = 150 - $whiteGloveCost;
                                            echo '<div class="shipping-promo bg-accent-soft color-accent">';
                                            echo '<span>🎉 Save ₹' . number_format($savings) . ' with your cart value!</span>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="shipping-promo bg-accent-soft color-accent">';
                                            echo '<span>✨ Includes unpacking, assembly & setup</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Freight Shipping -->
                        <?php $freightAvailable = in_array('freight', $availableShippingMethods); ?>
                        <label class="shipping-option <?php echo !$freightAvailable ? 'disabled' : ''; ?>" id="label-freight">
                            <div class="flex-start-gap-0-75">
                                <input type="radio" name="shipping_method" value="freight" <?php echo ($selectedShippingMethod === 'freight') ? 'checked' : ''; ?> <?php echo !$freightAvailable ? 'disabled' : ''; ?> class="mt-0-25" onchange="updateOrderSummary()">
                                <div class="flex-1">
                                    <div class="flex-center-gap-0-5 mb-0-5">
                                        <span class="fs-1-25">🚚</span>
                                        <strong class="fs-1-0625">Freight Shipping</strong>
                                        <?php if ($freightAvailable): ?>
                                            <span class="shipping-badge bg-danger-soft color-danger">HEAVY ITEMS</span>
                                        <?php else: ?>
                                            <span class="shipping-badge badge-grey-soft">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="db color-text-secondary fs-0-875 mb-0-5"><?php echo getShippingMethodDescription('freight', $subtotal); ?></span>
                                    <?php if ($freightAvailable): ?>
                                        <div class="shipping-promo bg-danger-soft color-danger">
                                            <span>📦 Best for bulk or oversized orders</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Info Note -->
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
                                    <img src="<?php echo BASE_URL; ?>/public/<?php echo $item['product']['image']; ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                                <?php else: ?>
                                    <?php echo $item['product']['image']; ?>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-600 fs-0-9375"><?php echo htmlspecialchars($item['product']['name']); ?></p>
                                <p class="color-text-secondary fs-0-8125">Qty: <?php echo $item['quantity']; ?></p>
                                
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <!-- Show discounted unit price -->
                                    <p class="font-600 color-primary fs-0-875 mt-0-25">
                                        ₹<?php echo number_format($item['unit_price_discounted']); ?> 
                                        <span class="text-muted-sm td-lt fs-0-75">
                                            ₹<?php echo number_format($item['unit_price_original']); ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <!-- Regular price -->
                                    <p class="font-600 color-primary fs-0-875 mt-0-25">
                                        ₹<?php echo number_format($item['product']['price']); ?>
                                    </p>
                                <?php endif; ?>
                                
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
    // Shipping calculation data from PHP
    const subtotal = <?php echo $subtotal; ?>;
    const shippingCosts = {
        standard: <?php echo calculateShippingCost($subtotal, 'standard'); ?>,
        express: <?php echo calculateShippingCost($subtotal, 'express'); ?>,
        whiteglove: <?php echo calculateShippingCost($subtotal, 'whiteglove'); ?>,
        freight: <?php echo calculateShippingCost($subtotal, 'freight'); ?>
    };

    function updateOrderSummary() {
        // Get selected shipping method
        const selectedMethod = document.querySelector('input[name="shipping_method"]:checked').value;
        
        // Save shipping method to session via AJAX
        fetch('api/shipping-method-update.php', {
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
        })
        .catch(error => {
            console.error('Error updating shipping method:', error);
        });
        
        // Use AJAX to calculate shipping (from cart-ajax.js)
        if (typeof updateShippingCalculation === 'function') {
            updateShippingCalculation(selectedMethod);
        } else {
            // Fallback to original calculation if AJAX not available
            const shippingCost = shippingCosts[selectedMethod];
            const tax = Math.round((subtotal + shippingCost) * 0.18);
            const total = subtotal + shippingCost + tax;
            
            document.getElementById('summary-shipping').textContent = '₹' + shippingCost.toLocaleString('en-IN');
            document.getElementById('summary-tax').textContent = '₹' + tax.toLocaleString('en-IN');
            document.getElementById('summary-total').textContent = '₹' + total.toLocaleString('en-IN');
        }
        
        // Update shipping option borders with smooth transition
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

    // Initialize styling on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateOrderSummary();
    });
    </script>

<?php require_once 'includes/footer.php'; ?>
