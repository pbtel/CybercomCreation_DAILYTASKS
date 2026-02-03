<?php
$pageTitle = "Shopping Cart";
require_once 'includes/header.php';

// Phase 7: Enforce login
requireLogin('cart.php');


$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();

// Calculate subtotal after applying coupon discount
$appliedCoupon = getAppliedCoupon();
$couponDiscount = 0;
if ($appliedCoupon) {
    $couponDiscount = calculateCouponDiscount($subtotal);
}
$subtotalAfterCoupon = $subtotal - $couponDiscount;

// Get available shipping methods - use subtotal AFTER coupon
$availableShippingMethods = getAvailableShippingMethods($cartItems, $subtotalAfterCoupon);

// Shipping will be calculated at checkout based on selected method
$shippingNote = 'Calculated at checkout';
// Tax note - will be calculated on (Subtotal + Shipping) at checkout
$taxNote = 'Calculated at checkout';
?>

    <div class="container">
        <h1 class="section-title-lg">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <!-- EMPTY CART -->
            <div class="empty-state">
                <div class="empty-state-icon">🛒</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Your cart is empty</h2>
                <p class="text-muted-sm mb-2rem">Start shopping to add items to your cart</p>
                <a href="products.php" class="btn-primary-lg">
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
                            <?php echo $item['product']['image']; ?>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                                <a href="product-detail.php?id=<?php echo $item['product']['id']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </a>
                            </h3>
                            <p class="text-muted-sm mb-2rem" style="margin-bottom: 0.5rem;">
                                <?php echo ucfirst($item['product']['category']); ?> / <?php echo $item['product']['brand']; ?>
                            </p>
                        <?php if (!empty($item['variant'])): ?>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">
                                    <?php foreach ($item['variant'] as $type => $value): ?>
                                        <?php echo ucfirst($type); ?>: <?php echo $value; ?>&nbsp;
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Price Display with Discount -->
                            <div style="margin-top: 0.75rem;">
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <!-- Discounted Price (Large, Bold) -->
                                    <p class="price-large" style="margin-bottom: 0.25rem;">
                                        ₹<?php echo number_format($item['unit_price_discounted']); ?>
                                    </p>
                                    <!-- Original Price (Strikethrough) -->
                                    <p class="price-old" style="margin-bottom: 0.25rem;">
                                        ₹<?php echo number_format($item['unit_price_original']); ?>
                                    </p>
                                    <!-- Savings -->
                                    <p class="price-savings">
                                        <?php echo formatDiscountText($item['first_unit_savings'], $item['discount_percent']); ?>
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
                            <div style="display: inline-block; margin-bottom: 0.5rem;">
                                <div class="quantity-wrapper">
                                    <input type="number" 
                                           class="cart-quantity-input input-text" 
                                           data-cart-key="<?php echo $key; ?>" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['product']['stock']; ?>"
                                           style="width: 70px; text-align: center; font-weight: 600;">
                                    <button onclick="updateCartQuantityAjax('<?php echo $key; ?>', this.previousElementSibling.value, this.closest('[data-cart-item]'))" 
                                            class="btn-update">
                                        Update
                                    </button>
                                </div>
                            </div>
                            <button onclick="removeCartItemAjax('<?php echo $key; ?>', this.closest('[data-cart-item]'))" 
                                    class="btn-danger" style="display: inline-block;">
                                Remove
                            </button>
                            <p class="item-subtotal text-muted-sm" style="margin-top: 0.5rem;">
                                Subtotal: ₹<?php echo number_format($item['subtotal']); ?>
                            </p>
                            <?php if ($item['quantity'] > 1 && $item['discount_percent'] > 0): ?>
                                <p style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;">
                                    💡 First unit discounted, others at full price
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 1rem;">
                        <a href="products.php" class="btn-outline-primary" style="margin-right: 1rem;">
                            Continue Shopping
                        </a>
                        <form action="cart-clear.php" method="POST" style="display: inline-block;">
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
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Order Summary</h2>

                        <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div class="summary-row">
                                <span class="summary-label">Subtotal (<?php echo count($cartItems); ?> unique items):</span>
                                <span class="summary-value" id="summary-subtotal">₹<?php echo number_format($subtotal); ?></span>
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
                        <?php
                        // Coupon variables already calculated at top of file
                        $finalSubtotal = $subtotalAfterCoupon;
                        ?>
                        
                        <div class="coupon-section">
                            <label class="coupon-label">Have a coupon code?</label>
                            
                            <?php if (!$appliedCoupon): ?>
                                <!-- Coupon Input Form -->
                                <div class="quantity-wrapper" style="margin-bottom: 0.5rem;">
                                    <input type="text" id="couponCode" placeholder="Enter code (e.g., SAVE10)" 
                                           class="input-text" style="font-size: 0.875rem;">
                                    <button onclick="applyCouponCode()" 
                                            class="btn-update" style="font-size: 0.875rem;">
                                        Apply
                                    </button>
                                </div>
                                <div id="couponMessage" style="font-size: 0.8125rem; margin-top: 0.5rem;"></div>
                            <?php else: ?>
                                <!-- Applied Coupon Display -->
                                <div class="applied-coupon">
                                    <div>
                                        <span style="font-weight: 600; color: #10b981; font-size: 0.9375rem;">
                                            ✓ <?php echo $appliedCoupon['code']; ?> Applied
                                        </span>
                                        <span class="text-muted-sm" style="margin-left: 0.5rem;">
                                            (<?php echo $appliedCoupon['discount_percent']; ?>% off)
                                        </span>
                                    </div>
                                    <button onclick="removeCouponCode()" 
                                            class="btn-danger" style="padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8125rem;">
                                        Remove
                                    </button>
                                </div>
<?php endif; ?>
                        </div>

                        <!-- Show Coupon Discount if Applied -->
                        <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                            <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                                <div class="summary-row">
                                    <span style="color: #10b981; font-weight: 600;">Coupon Discount (<?php echo $appliedCoupon['code']; ?>):</span>
                                    <span style="font-weight: 600; color: #10b981;" data-coupon-discount>-₹<?php echo number_format($couponDiscount); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>



                        <div class="info-note">
                            <p style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">
                                ℹ️ Shipping cost and final tax will be calculated based on your selected shipping method at checkout
                            </p>
                        </div>

                        <div class="summary-row" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem;">
                            <span>Estimated Total:</span>
                            <span style="color: var(--primary);" data-estimated-total>₹<?php echo number_format($finalSubtotal); ?>+</span>
                        </div>

                        <a href="checkout.php" class="btn-primary-lg" style="display: block; width: 100%;">
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

<?php require_once 'includes/footer.php'; ?>
