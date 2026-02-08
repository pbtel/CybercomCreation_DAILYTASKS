<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <h1 class="cart-title">Your Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <!-- EMPTY CART -->
        <div class="empty-cart-container">
            <div class="empty-cart-icon">🛒</div>
            <h2 class="empty-cart-text">Your cart is empty</h2>
            <p class="empty-cart-subtext">Start shopping to add items to your cart</p>
            <a href="<?php echo BASE_URL; ?>/products" class="btn-primary-lg">
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="cart-grid cart-layout">
            <!-- CART ITEMS -->
            <div>
                <?php foreach ($cartItems as $key => $item): ?>
                    <div data-cart-item data-cart-key="<?php echo $key; ?>" class="cart-item-card">
                        <div class="cart-item-image">
                            <?php if (isset($item['product']['image']) && (strpos($item['product']['image'], 'assets/images') === 0 || strpos($item['product']['image'], '/') === 0 || strpos($item['product']['image'], 'http') === 0)): ?>
                                <img src="<?php echo (strpos($item['product']['image'], 'http') === 0) ? $item['product']['image'] : BASE_URL . '/' . ltrim($item['product']['image'], '/'); ?>"
                                    alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                            <?php else: ?>
                                <div class="cart-item-emoji"><?php echo $item['product']['image'] ?? '📦'; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h3 class="cart-item-title">
                                <a href="<?php echo BASE_URL; ?>/product/<?php echo $item['product']['id']; ?>"
                                    class="cart-item-link">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </a>
                            </h3>
                            <p class="cart-item-meta">
                                <?php echo ucfirst($item['product']['category']); ?> / <?php echo $item['product']['brand']; ?>
                            </p>
                            <?php if (!empty($item['variant'])): ?>
                                <p class="color-text-secondary fs-0-875 mb-0-5">
                                    <?php foreach ($item['variant'] as $type => $value): ?>
                                        <span class="chip"><?php echo ucfirst($type); ?>: <?php echo $value; ?></span>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Price Display -->
                            <div class="mt-0-75">
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <p class="cart-price-large">₹<?php echo number_format($item['unit_price_discounted']); ?></p>
                                    <p class="cart-price-old">₹<?php echo number_format($item['unit_price_original']); ?></p>
                                    <p class="cart-savings">Save ₹<?php echo number_format($item['first_unit_savings']); ?>
                                        (<?php echo $item['discount_percent']; ?>%)</p>
                                <?php else: ?>
                                    <p class="cart-price-large">₹<?php echo number_format($item['product']['price']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quantity & Remove -->
                        <div class="text-right">
                            <div class="dib mb-1">
                                <div class="coupon-input-group" style="width: 150px;">
                                    <input type="number" class="cart-qty-input" data-cart-key="<?php echo $key; ?>"
                                        value="<?php echo $item['quantity']; ?>" min="1"
                                        max="<?php echo $item['product']['stock']; ?>">
                                    <button
                                        onclick="updateCartQuantityAjax('<?php echo $key; ?>', this.previousElementSibling.value, this.closest('[data-cart-item]'))"
                                        class="btn-update-qty">
                                        Update
                                    </button>
                                </div>
                            </div>
                            <br>
                            <button onclick="removeCartItemAjax('<?php echo $key; ?>', this.closest('[data-cart-item]'))"
                                class="btn-remove-item">
                                Remove Item
                            </button>
                            <p class="item-subtotal">
                                Subtotal: ₹<?php echo number_format($item['subtotal']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="cart-actions">
                    <a href="<?php echo BASE_URL; ?>/products" class="btn-continue-shopping">
                        ← Continue Shopping
                    </a>
                    <form action="<?php echo BASE_URL; ?>/cart/clear" method="POST" class="dib">
                        <button type="submit" onclick="return confirm('Are you sure you want to clear your cart?');"
                            class="btn-clear-cart">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- ORDER SUMMARY -->
            <div class="sticky-sidebar">
                <div class="summary-card">
                    <h2 class="summary-title">Order Summary</h2>

                    <div class="border-b pb-1 mb-1">
                        <div class="summary-row">
                            <span class="summary-text-secondary">Subtotal (<?php echo count($cartItems); ?> unique
                                items):</span>
                            <span class="summary-value"
                                id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-text-secondary">Estimated Shipping:</span>
                            <span class="summary-value-italic"><?php echo $shippingNote; ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-text-secondary">Estimated Tax (GST):</span>
                            <span class="summary-value-italic"><?php echo $taxNote; ?></span>
                        </div>
                    </div>

                    <!-- COUPON SECTION -->
                    <div class="coupon-section">
                        <label class="coupon-label">Promo Code</label>

                        <?php if (!$appliedCoupon): ?>
                            <div class="coupon-input-group">
                                <input type="text" id="couponCode" placeholder="Enter Code" class="coupon-input">
                                <button onclick="applyCouponCode()" class="btn-apply-coupon">Apply</button>
                            </div>

                            <?php if (!empty($availableCoupons)): ?>
                                <div class="available-coupons-list mt-1">
                                    <p class="text-secondary fs-0-75 font-700 uppercase mb-0-5">Available Offers:</p>
                                    <div class="flex-wrap gap-0-5">
                                        <?php foreach ($availableCoupons as $code => $percent): ?>
                                            <div class="coupon-tag" title="Apply <?php echo $code; ?>"
                                                onclick="document.getElementById('couponCode').value = '<?php echo $code; ?>'">
                                                <span class="font-800"><?php echo $code; ?></span>
                                                <span class="fs-0-7 opacity-0-8">(<?php echo $percent; ?>% OFF)</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div id="couponMessage" class="fs-0-8 mt-0-5"></div>
                        <?php else: ?>
                            <div class="applied-coupon-box">
                                <div>
                                    <div class="font-700 text-success fs-0-9">✓ <?php echo $appliedCoupon['code']; ?></div>
                                    <div class="text-secondary fs-0-8"><?php echo $appliedCoupon['discount_percent']; ?>%
                                        discount applied</div>
                                </div>
                                <button onclick="removeCouponCode()" class="btn-remove-coupon">Remove</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                        <div class="summary-row mb-1">
                            <span class="text-success font-700">Coupon Savings:</span>
                            <span class="text-success font-700">-₹<?php echo number_format($couponDiscount); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row fs-1-25 font-800 mb-2">
                        <span>Grand Total:</span>
                        <span class="color-primary">₹<?php echo number_format($subtotalAfterCoupon); ?>*</span>
                    </div>

                    <p class="fs-0-8 text-secondary mb-1-5">
                        * Shipping and final taxes will be calculated at the final step of checkout.
                    </p>

                    <a href="<?php echo BASE_URL; ?>/checkout" class="checkout-btn">
                        Checkout Now
                    </a>

                    <div class="secure-info">
                        <p class="fs-0-85 mb-0">🔒 256-bit SSL Secure Checkout</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // AJAX functions need to be updated to use current BASE_URL and routes
    const API_URL = '<?php echo BASE_URL; ?>/api';

    // Example wrapper for the existing cart-ajax.js functions
    // (Actual script.js and cart-ajax.js will need slight modifications to use these endpoint mappings)
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>