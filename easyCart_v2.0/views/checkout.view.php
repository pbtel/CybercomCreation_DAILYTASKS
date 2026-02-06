<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h1 class="checkout-title">Checkout</h1>
    <p class="checkout-subtitle">Complete your order</p>

    <div class="checkout-grid">
        <!-- CHECKOUT FORM -->
        <div>
            <form action="../../order-place.php" method="POST">
                <!-- SHIPPING INFO -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">Shipping Information</h2>

                    <div class="form-row">
                        <div>
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" id="first_name" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="email" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone *</label>
                        <input type="tel" name="phone" id="phone" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address *</label>
                        <input type="text" name="address" id="address" required class="form-input">
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">City *</label>
                            <input type="text" name="city" id="city" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">State *</label>
                            <input type="text" name="state" id="state" required class="form-input">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">PIN Code *</label>
                            <input type="text" name="pincode" id="pincode" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Country *</label>
                            <select name="country" id="country" required class="form-input">
                                <option value="">Select Country</option>
                                <option value="IN" selected>India</option>
                                <option value="US">United States</option>
                                <option value="UK">United Kingdom</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SHIPPING METHOD -->
                <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Shipping Method</h2>

                    <!-- Standard Shipping -->
                    <?php $standardAvailable = in_array('standard', $availableShippingMethods); ?>
                    <label
                        class="shipping-option <?php echo !$standardAvailable ? 'disabled' : ''; ?> <?php echo ($selectedShippingMethod === 'standard') ? 'selected' : ''; ?>">
                        <div class="shipping-option-row">
                            <input type="radio" name="shipping_method" value="standard" <?php echo ($selectedShippingMethod === 'standard') ? 'checked' : ''; ?> <?php echo !$standardAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;"
                                onchange="updateOrderSummary()">
                            <div class="shipping-option-details">
                                <div class="shipping-option-title-row">
                                    <span class="shipping-icon">&#128230;</span>
                                    <strong class="shipping-name">Standard Shipping</strong>
                                    <?php if ($standardAvailable): ?>
                                        <span class="shipping-badge badge-green-gradient">MOST POPULAR</span>
                                    <?php else: ?>
                                        <span class="shipping-badge badge-gray">NOT AVAILABLE</span>
                                    <?php endif; ?>
                                </div>
                                <span class="cart-item-meta"
                                    style="display: block; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('standard', $subtotal); ?></span>
                                <?php if ($standardAvailable): ?>
                                    <div class="shipping-note-box note-green">
                                        <span style="font-size: 0.8125rem; font-weight: 600;">&#128176; Most economical
                                            option</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- Express Shipping -->
                    <?php $expressAvailable = in_array('express', $availableShippingMethods); ?>
                    <label
                        class="shipping-option <?php echo !$expressAvailable ? 'disabled' : ''; ?> <?php echo ($selectedShippingMethod === 'express') ? 'selected' : ''; ?>">
                        <div class="shipping-option-row">
                            <input type="radio" name="shipping_method" value="express" <?php echo ($selectedShippingMethod === 'express') ? 'checked' : ''; ?> <?php echo !$expressAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                            <div class="shipping-option-details">
                                <div class="shipping-option-title-row">
                                    <span class="shipping-icon">&#9889;</span>
                                    <strong class="shipping-name">Express Shipping</strong>
                                    <?php if ($expressAvailable): ?>
                                        <span class="shipping-badge badge-orange-gradient">FAST</span>
                                    <?php else: ?>
                                        <span class="shipping-badge badge-gray">NOT AVAILABLE</span>
                                    <?php endif; ?>
                                </div>
                                <span class="cart-item-meta"
                                    style="display: block; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('express', $subtotal); ?></span>
                                <?php if ($expressAvailable): ?>
                                    <?php
                                    $expressCost = calculateShippingCost($subtotal, 'express');
                                    if ($expressCost < 80) {
                                        $savings = 80 - $expressCost;
                                        echo '<div class="shipping-note-box note-orange">';
                                        echo '<span style="font-size: 0.8125rem; font-weight: 600;">&#127881; Save &#8377;' . number_format($savings) . ' with your cart value!</span>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="shipping-note-box note-primary">';
                                        echo '<span style="font-size: 0.8125rem; font-weight: 600;">&#9889; Faster delivery guaranteed</span>';
                                        echo '</div>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- White Glove Delivery -->
                    <?php $whitegloveAvailable = in_array('whiteglove', $availableShippingMethods); ?>
                    <label
                        class="shipping-option <?php echo !$whitegloveAvailable ? 'disabled' : ''; ?> <?php echo ($selectedShippingMethod === 'whiteglove') ? 'selected' : ''; ?>">
                        <div class="shipping-option-row">
                            <input type="radio" name="shipping_method" value="whiteglove" <?php echo ($selectedShippingMethod === 'whiteglove') ? 'checked' : ''; ?> <?php echo !$whitegloveAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;"
                                onchange="updateOrderSummary()">
                            <div class="shipping-option-details">
                                <div class="shipping-option-title-row">
                                    <span class="shipping-icon">&#127942;</span>
                                    <strong class="shipping-name">White Glove Delivery</strong>
                                    <?php if ($whitegloveAvailable): ?>
                                        <span class="shipping-badge badge-purple-gradient">PREMIUM</span>
                                    <?php else: ?>
                                        <span class="shipping-badge badge-gray">NOT AVAILABLE</span>
                                    <?php endif; ?>
                                </div>
                                <span class="cart-item-meta"
                                    style="display: block; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('whiteglove', $subtotal); ?></span>
                                <?php if ($whitegloveAvailable): ?>
                                    <?php
                                    $whiteGloveCost = calculateShippingCost($subtotal, 'whiteglove');
                                    if ($whiteGloveCost < 150) {
                                        $savings = 150 - $whiteGloveCost;
                                        echo '<div class="shipping-note-box note-purple">';
                                        echo '<span style="font-size: 0.8125rem; font-weight: 600;">&#127881; Save &#8377;' . number_format($savings) . ' with your cart value!</span>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="shipping-note-box note-purple">';
                                        echo '<span style="font-size: 0.8125rem; font-weight: 600;">&#10024; Includes unpacking, assembly & setup</span>';
                                        echo '</div>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- Freight Shipping -->
                    <?php $freightAvailable = in_array('freight', $availableShippingMethods); ?>
                    <label
                        class="shipping-option <?php echo !$freightAvailable ? 'disabled' : ''; ?> <?php echo ($selectedShippingMethod === 'freight') ? 'selected' : ''; ?>">
                        <div class="shipping-option-row">
                            <input type="radio" name="shipping_method" value="freight" <?php echo ($selectedShippingMethod === 'freight') ? 'checked' : ''; ?> <?php echo !$freightAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                            <div class="shipping-option-details">
                                <div class="shipping-option-title-row">
                                    <span class="shipping-icon">&#128666;</span>
                                    <strong class="shipping-name">Freight Shipping</strong>
                                    <?php if ($freightAvailable): ?>
                                        <span class="shipping-badge badge-red-gradient">HEAVY ITEMS</span>
                                    <?php else: ?>
                                        <span class="shipping-badge badge-gray">NOT AVAILABLE</span>
                                    <?php endif; ?>
                                </div>
                                <span class="cart-item-meta"
                                    style="display: block; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('freight', $subtotal); ?></span>
                                <?php if ($freightAvailable): ?>
                                    <div class="shipping-note-box note-red">
                                        <span style="font-size: 0.8125rem; font-weight: 600;">&#128230; Best for bulk or
                                            oversized orders</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- Info Note -->
                    <div class="info-note">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">
                            <strong style="color: var(--primary);">&#8505;&#65039; Note:</strong> All delivery times
                            are
                            estimated
                            and may vary based on your location. Tracking information will be provided once your
                            order
                            ships.
                        </p>
                    </div>
                </div>

                <!-- PAYMENT METHOD -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">Payment Method</h2>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <strong>Cash on Delivery</strong>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="upi">
                        <strong>UPI / QR Code</strong>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="card">
                        <strong>Credit / Debit Card</strong>
                    </label>
                </div>

                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="sticky-sidebar">
            <div class="summary-card">
                <h2 class="summary-title">Order Summary</h2>

                <div class="checkout-summary-scroll">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="checkout-item">
                            <div class="checkout-item-image">
                                <?php echo $item['product']['image']; ?>
                            </div>
                            <div style="flex: 1;">
                                <p style="font-weight: 600; font-size: 0.9375rem;">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.8125rem;">Qty:
                                    <?php echo $item['quantity']; ?>
                                </p>

                                <?php if ($item['discount_percent'] > 0): ?>
                                    <!-- Show discounted unit price -->
                                    <p
                                        style="font-weight: 600; color: var(--primary); font-size: 0.875rem; margin-top: 0.25rem;">
                                        &#8377;<?php echo number_format($item['unit_price_discounted']); ?>
                                        <span
                                            style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.75rem;">
                                            &#8377;<?php echo number_format($item['unit_price_original']); ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <!-- Regular price -->
                                    <p
                                        style="font-weight: 600; color: var(--primary); font-size: 0.875rem; margin-top: 0.25rem;">
                                        &#8377;<?php echo number_format($item['product']['price']); ?>
                                    </p>
                                <?php endif; ?>

                                <p style="font-weight: 600; color: var(--primary); margin-top: 0.25rem;">
                                    &#8377;<?php echo number_format($item['subtotal']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-top: 2px solid var(--border); padding-top: 1rem; margin-bottom: 1rem;">
                    <div class="summary-row">
                        <span style="color: var(--text-secondary);">Subtotal:</span>
                        <span style="font-weight: 600;"
                            id="summary-subtotal">&#8377;<?php echo number_format($subtotal); ?></span>
                    </div>

                    <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                        <div class="summary-row">
                            <span style="color: #10b981; font-weight: 600;">Coupon
                                (<?php echo $appliedCoupon['code']; ?>):</span>
                            <span
                                style="font-weight: 600; color: #10b981;">-&#8377;<?php echo number_format($couponDiscount); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span style="color: var(--text-secondary);">Shipping:</span>
                        <span style="font-weight: 600;"
                            id="summary-shipping">&#8377;<?php echo number_format($shipping); ?></span>
                    </div>
                    <div class="summary-row">
                        <span style="color: var(--text-secondary);">Tax (18% GST):</span>
                        <span style="font-weight: 600;"
                            id="summary-tax">&#8377;<?php echo number_format($tax); ?></span>
                    </div>
                </div>

                <div class="summary-row"
                    style="font-size: 1.25rem; font-weight: 700; padding-top: 1rem; border-top: 2px solid var(--border);">
                    <span>Total:</span>
                    <span style="color: var(--primary);"
                        id="summary-total">&#8377;<?php echo number_format($total); ?></span>
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

            document.getElementById('summary-shipping').textContent = '&#8377;' + shippingCost.toLocaleString('en-IN');
            document.getElementById('summary-tax').textContent = '&#8377;' + tax.toLocaleString('en-IN');
            document.getElementById('summary-total').textContent = '&#8377;' + total.toLocaleString('en-IN');
        }

        // Update shipping option selection state
        document.querySelectorAll('.shipping-option').forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            if (radio.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });
    }

    // Add event listeners handled by CSS hover now
    document.addEventListener('DOMContentLoaded', function () {
        // Apply correct styling on page load based on selected shipping method
        updateOrderSummary();
    });

    // ============================================
    // FORM DATA PERSISTENCE WITH LOCALSTORAGE
    // ============================================

    // List of form field IDs to persist
    const formFields = [
        'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'state', 'pincode', 'country'
    ];

    // Load saved data on page load
    document.addEventListener('DOMContentLoaded', function () {
        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                const savedValue = localStorage.getItem('checkout_' + fieldId);
                if (savedValue) {
                    field.value = savedValue;
                }
            }
        });

        // Load selected shipping method
        const savedShipping = localStorage.getItem('checkout_shipping_method');
        if (savedShipping) {
            const shippingRadio = document.querySelector(`input[name="shipping_method"][value="${savedShipping}"]`);
            if (shippingRadio) {
                shippingRadio.checked = true;
                // Trigger change event to update totals
                shippingRadio.dispatchEvent(new Event('change'));
            }
        }

        // Load selected payment method
        const savedPayment = localStorage.getItem('checkout_payment_method');
        if (savedPayment) {
            const paymentRadio = document.querySelector(`input[name="payment_method"][value="${savedPayment}"]`);
            if (paymentRadio) {
                paymentRadio.checked = true;
            }
        }
    });

    // Save data on input change
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function () {
                localStorage.setItem('checkout_' + fieldId, this.value);
            });
        }
    });

    // Save shipping method selection
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                localStorage.setItem('checkout_shipping_method', this.value);
            }
        });
    });

    // Save payment method selection
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                localStorage.setItem('checkout_payment_method', this.value);
            }
        });
    });

    // Clear localStorage when order is successfully placed
    const checkoutForm = document.querySelector('form[action="../../order-place.php"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function () {
            // Clear all checkout data from localStorage
            formFields.forEach(fieldId => {
                localStorage.removeItem('checkout_' + fieldId);
            });
            localStorage.removeItem('checkout_shipping_method');
            localStorage.removeItem('checkout_payment_method');
        });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>