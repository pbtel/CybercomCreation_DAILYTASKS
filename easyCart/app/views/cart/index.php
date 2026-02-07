<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="section-title-lg">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <!-- EMPTY CART -->
        <div class="empty-state">
            <div class="empty-state-icon">🛒</div>
            <h2 class="fs-1-5 mb-1">Your cart is empty</h2>
            <p class="text-muted-sm mb-2rem">Start shopping to add items to your cart</p>
            <a href="<?php echo BASE_URL; ?>/products" class="btn-primary-lg">
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- CART ITEMS -->
            <div>
                <?php foreach ($cartItems as $key => $item): ?>
                    <div data-cart-item data-cart-key="<?php echo $key; ?>" class="cart-item-card">
                        <!-- Product Image -->
                        <div class="cart-item-image">
                            <?php if (strpos($item['product']['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/public/' . $item['product']['image']; ?>"
                                    alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                            <?php else: ?>
                                <?php echo $item['product']['image']; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h3 class="fs-1-125 font-600 mb-0-5">
                                <a href="<?php echo BASE_URL; ?>/product/<?php echo $item['product']['id']; ?>"
                                    class="color-inherit td-n">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </a>
                            </h3>
                            <p class="text-muted-sm mb-0-5">
                                <?php echo ucfirst($item['product']['category']); ?> / <?php echo $item['product']['brand']; ?>
                            </p>
                            <?php if (!empty($item['variant'])): ?>
                                <p class="color-text-secondary fs-0-875">
                                    <?php foreach ($item['variant'] as $type => $value): ?>
                                        <?php echo ucfirst($type); ?>: <?php echo $value; ?>&nbsp;
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Price Display with Discount -->
                            <div class="mt-0-75">
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <!-- Discounted Price (Large, Bold) -->
                                    <p class="price-large mb-0-25">
                                        ₹<?php echo number_format($item['unit_price_discounted']); ?>
                                    </p>
                                    <!-- Original Price (Strikethrough) -->
                                    <p class="price-old mb-0-25">
                                        ₹<?php echo number_format($item['unit_price_original']); ?>
                                    </p>
                                    <!-- Savings -->
                                    <p class="price-savings">
                                        Save ₹<?php echo number_format($item['first_unit_savings']); ?>
                                        (<?php echo $item['discount_percent']; ?>% off)
                                    </p>
                                <?php else: ?>
                                    <!-- No Discount - Regular Price -->
                                    <p class="price-large">
                                        ₹<?php echo number_format($item['product']['price']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quantity & Remove -->
                        <div class="text-right">
                            <div class="dib mb-0-5">
                                <div class="quantity-wrapper">
                                    <input type="number" class="cart-quantity-input input-text w-70 ta-center font-600"
                                        data-cart-key="<?php echo $key; ?>" value="<?php echo $item['quantity']; ?>" min="1"
                                        max="<?php echo $item['product']['stock']; ?>">
                                    <button
                                        onclick="updateCartQuantityAjax('<?php echo $key; ?>', this.previousElementSibling.value, this.closest('[data-cart-item]'))"
                                        class="btn-update">
                                        Update
                                    </button>
                                </div>
                            </div>
                            <button onclick="removeCartItemAjax('<?php echo $key; ?>', this.closest('[data-cart-item]'))"
                                class="btn-danger dib">
                                Remove
                            </button>
                            <p class="item-subtotal text-muted-sm mt-0-5">
                                Subtotal: ₹<?php echo number_format($item['subtotal']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-1">
                    <a href="<?php echo BASE_URL; ?>/products" class="btn-outline-primary mr-0-75">
                        Continue Shopping
                    </a>
                    <form action="<?php echo BASE_URL; ?>/cart/clear" method="POST" class="dib">
                        <button type="submit" onclick="return confirm('Are you sure you want to clear your cart?');"
                            class="btn-outline-danger">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- ORDER SUMMARY -->
            <div class="sticky-summary">
                <div class="card">
                    <h2 class="fs-1-5 font-700 mb-1-5">Order Summary</h2>

                    <div class="border-b pb-1 mb-1">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal (<?php echo count($cartItems); ?> unique items):</span>
                            <span class="summary-value"
                                id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Shipping:</span>
                            <span class="summary-value-italic">
                                <?php echo $shippingNote; ?>
                            </span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Tax (18% GST):</span>
                            <span class="summary-value-italic">
                                <?php echo $taxNote; ?>
                            </span>
                        </div>
                    </div>

                    <!-- COUPON CODE SECTION -->
                    <div class="coupon-section">
                        <label class="coupon-label">Have a coupon code?</label>

                        <?php if (!$appliedCoupon): ?>
                            <!-- Coupon Input Form -->
                            <div class="quantity-wrapper mb-0-5">
                                <input type="text" id="couponCode" placeholder="Enter code (e.g., SAVE10)"
                                    class="input-text fs-0-875">
                                <button onclick="applyCouponCode()" class="btn-update fs-0-875">
                                    Apply
                                </button>
                            </div>
                            <div id="couponMessage" class="fs-0-8125 mt-0-5"></div>
                        <?php else: ?>
                            <!-- Applied Coupon Display -->
                            <div class="applied-coupon">
                                <div>
                                    <span class="font-600 text-success fs-0-9375">
                                        ✓ <?php echo $appliedCoupon['code']; ?> Applied
                                    </span>
                                    <span class="text-muted-sm ml-0-5">
                                        (<?php echo $appliedCoupon['discount_percent']; ?>% off)
                                    </span>
                                </div>
                                <button onclick="removeCouponCode()" class="btn-danger p-0-5-1 border-radius-6 fs-0-8125">
                                    Remove
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Show Coupon Discount if Applied -->
                    <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                        <div class="border-b pb-1 mb-1">
                            <div class="summary-row">
                                <span class="text-success font-600">Coupon Discount
                                    (<?php echo $appliedCoupon['code']; ?>):</span>
                                <span class="font-600 text-success"
                                    data-coupon-discount>-₹<?php echo number_format($couponDiscount); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info-note">
                        <p class="fs-0-875 color-primary font-600">
                            ℹ️ Shipping cost and final tax will be calculated based on your selected shipping method at
                            checkout
                        </p>
                    </div>

                    <div class="summary-row fs-1-25 font-700 mb-2">
                        <span>Estimated Total:</span>
                        <span class="color-primary"
                            data-estimated-total>₹<?php echo number_format($subtotalAfterCoupon); ?>+</span>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/checkout" class="btn-primary-lg db w-100">
                        Proceed to Checkout
                    </a>

                    <div class="security-note">
                        <p class="text-muted-sm">
                            🔒 Secure Checkout<br>
                            💳 Multiple Payment Options<br>
                            📦 Fast Delivery
                        </p>
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