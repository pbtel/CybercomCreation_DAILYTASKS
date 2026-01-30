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
if (!isLoggedIn()) {
    setFlashMessage('info', 'Please login or create an account to proceed with checkout.');
    header('Location: login.php?redirect=checkout.php');
    exit;
}
?>

    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">Checkout</h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">Complete your order</p>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
            <!-- CHECKOUT FORM -->
            <div>
                <form action="order-place.php" method="POST">
                    <!-- SHIPPING INFO -->
                    <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Shipping Information</h2>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">First Name *</label>
                                <input type="text" name="first_name" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Last Name *</label>
                                <input type="text" name="last_name" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                            </div>
                        </div>

                        <div style="margin-top: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email *</label>
                            <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                        </div>

                        <div style="margin-top: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone *</label>
                            <input type="tel" name="phone" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                        </div>

                        <div style="margin-top: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Address *</label>
                            <input type="text" name="address" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">City *</label>
                                <input type="text" name="city" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">State *</label>
                                <input type="text" name="state" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">PIN Code *</label>
                                <input type="text" name="pincode" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Country *</label>
                                <select name="country" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px;">
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
                        <label class="shipping-option" style="display: block; padding: 1.25rem; border: 2px solid <?php echo $standardAvailable ? 'var(--primary)' : 'var(--border)'; ?>; border-radius: 12px; margin-bottom: 1rem; cursor: <?php echo $standardAvailable ? 'pointer' : 'not-allowed'; ?>; background: <?php echo $standardAvailable ? 'rgba(99, 102, 241, 0.05)' : 'rgba(0, 0, 0, 0.02)'; ?>; position: relative; transition: all 0.3s ease; <?php echo !$standardAvailable ? 'opacity: 0.5;' : ''; ?>">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <input type="radio" name="shipping_method" value="standard" <?php echo ($selectedShippingMethod === 'standard') ? 'checked' : ''; ?> <?php echo !$standardAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 1.25rem;">📦</span>
                                        <strong style="font-size: 1.0625rem;">Standard Shipping</strong>
                                        <?php if ($standardAvailable): ?>
                                            <span style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">MOST POPULAR</span>
                                        <?php else: ?>
                                            <span style="background: #9ca3af; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('standard', $subtotal); ?></span>
                                    <?php if ($standardAvailable): ?>
                                        <div style="background: rgba(16, 185, 129, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">
                                            <span style="color: #059669; font-size: 0.8125rem; font-weight: 600;">💰 Most economical option</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Express Shipping -->
                        <?php $expressAvailable = in_array('express', $availableShippingMethods); ?>
                        <label class="shipping-option" style="display: block; padding: 1.25rem; border: 2px solid var(--border); border-radius: 12px; margin-bottom: 1rem; cursor: <?php echo $expressAvailable ? 'pointer' : 'not-allowed'; ?>; position: relative; transition: all 0.3s ease; <?php echo !$expressAvailable ? 'opacity: 0.5;' : ''; ?>">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <input type="radio" name="shipping_method" value="express" <?php echo ($selectedShippingMethod === 'express') ? 'checked' : ''; ?> <?php echo !$expressAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 1.25rem;">⚡</span>
                                        <strong style="font-size: 1.0625rem;">Express Shipping</strong>
                                        <?php if ($expressAvailable): ?>
                                            <span style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">FAST</span>
                                        <?php else: ?>
                                            <span style="background: #9ca3af; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('express', $subtotal); ?></span>
                                    <?php if ($expressAvailable): ?>
                                        <?php 
                                        $expressCost = calculateShippingCost($subtotal, 'express');
                                        if ($expressCost < 80) {
                                            $savings = 80 - $expressCost;
                                            echo '<div style="background: rgba(245, 158, 11, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">';
                                            echo '<span style="color: #d97706; font-size: 0.8125rem; font-weight: 600;">🎉 Save ₹' . number_format($savings) . ' with your cart value!</span>';
                                            echo '</div>';
                                        } else {
                                            echo '<div style="background: rgba(99, 102, 241, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">';
                                            echo '<span style="color: var(--primary); font-size: 0.8125rem; font-weight: 600;">⚡ Faster delivery guaranteed</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- White Glove Delivery -->
                        <?php $whitegloveAvailable = in_array('whiteglove', $availableShippingMethods); ?>
                        <label class="shipping-option" style="display: block; padding: 1.25rem; border: 2px solid var(--border); border-radius: 12px; margin-bottom: 1rem; cursor: <?php echo $whitegloveAvailable ? 'pointer' : 'not-allowed'; ?>; position: relative; transition: all 0.3s ease; <?php echo !$whitegloveAvailable ? 'opacity: 0.5;' : ''; ?>">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <input type="radio" name="shipping_method" value="whiteglove" <?php echo ($selectedShippingMethod === 'whiteglove') ? 'checked' : ''; ?> <?php echo !$whitegloveAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 1.25rem;">🏆</span>
                                        <strong style="font-size: 1.0625rem;">White Glove Delivery</strong>
                                        <?php if ($whitegloveAvailable): ?>
                                            <span style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">PREMIUM</span>
                                        <?php else: ?>
                                            <span style="background: #9ca3af; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('whiteglove', $subtotal); ?></span>
                                    <?php if ($whitegloveAvailable): ?>
                                        <?php 
                                        $whiteGloveCost = calculateShippingCost($subtotal, 'whiteglove');
                                        if ($whiteGloveCost < 150) {
                                            $savings = 150 - $whiteGloveCost;
                                            echo '<div style="background: rgba(139, 92, 246, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">';
                                            echo '<span style="color: #7c3aed; font-size: 0.8125rem; font-weight: 600;">🎉 Save ₹' . number_format($savings) . ' with your cart value!</span>';
                                            echo '</div>';
                                        } else {
                                            echo '<div style="background: rgba(139, 92, 246, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">';
                                            echo '<span style="color: #7c3aed; font-size: 0.8125rem; font-weight: 600;">✨ Includes unpacking, assembly & setup</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Freight Shipping -->
                        <?php $freightAvailable = in_array('freight', $availableShippingMethods); ?>
                        <label class="shipping-option" style="display: block; padding: 1.25rem; border: 2px solid var(--border); border-radius: 12px; cursor: <?php echo $freightAvailable ? 'pointer' : 'not-allowed'; ?>; position: relative; transition: all 0.3s ease; <?php echo !$freightAvailable ? 'opacity: 0.5;' : ''; ?>">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <input type="radio" name="shipping_method" value="freight" <?php echo ($selectedShippingMethod === 'freight') ? 'checked' : ''; ?> <?php echo !$freightAvailable ? 'disabled' : ''; ?> style="margin-top: 0.25rem;" onchange="updateOrderSummary()">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 1.25rem;">🚚</span>
                                        <strong style="font-size: 1.0625rem;">Freight Shipping</strong>
                                        <?php if ($freightAvailable): ?>
                                            <span style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">HEAVY ITEMS</span>
                                        <?php else: ?>
                                            <span style="background: #9ca3af; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">NOT AVAILABLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo getShippingMethodDescription('freight', $subtotal); ?></span>
                                    <?php if ($freightAvailable): ?>
                                        <div style="background: rgba(239, 68, 68, 0.1); padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block;">
                                            <span style="color: #dc2626; font-size: 0.8125rem; font-weight: 600;">📦 Best for bulk or oversized orders</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <!-- Info Note -->
                        <div style="background: rgba(99, 102, 241, 0.05); border-left: 4px solid var(--primary); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                            <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">
                                <strong style="color: var(--primary);">ℹ️ Note:</strong> All delivery times are estimated and may vary based on your location. Tracking information will be provided once your order ships.
                            </p>
                        </div>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Payment Method</h2>
                        
                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                            <input type="radio" name="payment_method" value="cod" checked style="margin-right: 0.75rem;">
                            <strong>Cash on Delivery</strong>
                        </label>

                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                            <input type="radio" name="payment_method" value="upi" style="margin-right: 0.75rem;">
                            <strong>UPI / QR Code</strong>
                        </label>

                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="payment_method" value="card" style="margin-right: 0.75rem;">
                            <strong>Credit / Debit Card</strong>
                        </label>
                    </div>

                    <button type="submit" class="action-button">Place Order</button>
                </form>
            </div>

            <!-- ORDER SUMMARY -->
            <div style="position: sticky; top: 6rem;">
                <div style="background: white; border-radius: 16px; padding: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Order Summary</h2>

                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem;">
                        <?php foreach ($cartItems as $item): ?>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                            <div style="width: 60px; height: 60px; background: var(--bg-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                <?php echo $item['product']['image']; ?>
                            </div>
                            <div style="flex: 1;">
                                <p style="font-weight: 600; font-size: 0.9375rem;"><?php echo htmlspecialchars($item['product']['name']); ?></p>
                                <p style="color: var(--text-secondary); font-size: 0.8125rem;">Qty: <?php echo $item['quantity']; ?></p>
                                
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <!-- Show discounted unit price -->
                                    <p style="font-weight: 600; color: var(--primary); font-size: 0.875rem; margin-top: 0.25rem;">
                                        ₹<?php echo number_format($item['unit_price_discounted']); ?> 
                                        <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.75rem;">
                                            ₹<?php echo number_format($item['unit_price_original']); ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <!-- Regular price -->
                                    <p style="font-weight: 600; color: var(--primary); font-size: 0.875rem; margin-top: 0.25rem;">
                                        ₹<?php echo number_format($item['product']['price']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p style="font-weight: 600; color: var(--primary); margin-top: 0.25rem;">₹<?php echo number_format($item['subtotal']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="border-top: 2px solid var(--border); padding-top: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Subtotal:</span>
                            <span style="font-weight: 600;" id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
                        </div>
                        
                        <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: #10b981; font-weight: 600;">Coupon (<?php echo $appliedCoupon['code']; ?>):</span>
                            <span style="font-weight: 600; color: #10b981;">-₹<?php echo number_format($couponDiscount); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Shipping:</span>
                            <span style="font-weight: 600;" id="summary-shipping">₹<?php echo number_format($shipping); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Tax (18% GST):</span>
                            <span style="font-weight: 600;" id="summary-tax">₹<?php echo number_format($tax); ?></span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: 1rem; border-top: 2px solid var(--border);">
                        <span>Total:</span>
                        <span style="color: var(--primary);" id="summary-total">₹<?php echo number_format($total); ?></span>
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
                label.style.borderColor = 'var(--primary)';
                label.style.background = 'rgba(99, 102, 241, 0.05)';
                label.style.transform = 'scale(1.02)';
                label.style.boxShadow = '0 4px 12px rgba(99, 102, 241, 0.15)';
            } else {
                label.style.borderColor = 'var(--border)';
                label.style.background = 'white';
                label.style.transform = 'scale(1)';
                label.style.boxShadow = 'none';
            }
        });
    }

    // Add hover effects to shipping options
    document.addEventListener('DOMContentLoaded', function() {
        // Apply correct styling on page load based on selected shipping method
        updateOrderSummary();
        
        document.querySelectorAll('.shipping-option').forEach(label => {
            label.addEventListener('mouseenter', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (!radio.checked) {
                    this.style.borderColor = 'rgba(99, 102, 241, 0.5)';
                    this.style.background = 'rgba(99, 102, 241, 0.02)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
                }
            });

            label.addEventListener('mouseleave', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (!radio.checked) {
                    this.style.borderColor = 'var(--border)';
                    this.style.background = 'white';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                }
            });
        });
    });
    </script>

<?php require_once 'includes/footer.php'; ?>
