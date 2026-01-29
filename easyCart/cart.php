<?php
$pageTitle = "Shopping Cart";
require_once 'includes/header.php';

$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();
// Shipping will be calculated at checkout based on selected method
$shippingNote = 'Calculated at checkout';
// Tax note - will be calculated on (Subtotal + Shipping) at checkout
$taxNote = 'Calculated at checkout';
?>

    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 2rem;">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <!-- EMPTY CART -->
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🛒</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Your cart is empty</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">Start shopping to add items to your cart</p>
                <a href="products.php" style="display: inline-block; padding: 1rem 2.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-decoration: none; border-radius: 12px; font-weight: 700;">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="cart-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- CART ITEMS -->
                <div>
                    <?php foreach ($cartItems as $key => $item): ?>
                    <div data-cart-item data-cart-key="<?php echo $key; ?>" style="background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem; display: grid; grid-template-columns: 120px 1fr auto; gap: 1.5rem; align-items: center;">
                        <!-- Product Image -->
                        <div style="width: 120px; height: 120px; background: var(--bg-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                            <?php echo $item['product']['image']; ?>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                                <a href="product-detail.php?id=<?php echo $item['product']['id']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </a>
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">
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
                                    <p style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.25rem;">
                                        ₹<?php echo number_format($item['unit_price_discounted']); ?>
                                    </p>
                                    <!-- Original Price (Strikethrough) -->
                                    <p style="font-size: 0.9375rem; color: var(--text-secondary); text-decoration: line-through; margin-bottom: 0.25rem;">
                                        ₹<?php echo number_format($item['unit_price_original']); ?>
                                    </p>
                                    <!-- Savings -->
                                    <p style="font-size: 0.875rem; color: #10b981; font-weight: 600;">
                                        <?php echo formatDiscountText($item['first_unit_savings'], $item['discount_percent']); ?>
                                    </p>
                                <?php else: ?>
                                    <!-- No Discount - Regular Price -->
                                    <p style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">
                                        ₹<?php echo number_format($item['product']['price']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quantity & Remove -->
                        <div style="text-align: right;">
                            <form action="cart-update.php" method="POST" style="display: inline-block; margin-bottom: 0.5rem;">
                                <input type="hidden" name="cart_key" value="<?php echo $key; ?>">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['product']['stock']; ?>"
                                           style="width: 70px; padding: 0.5rem; border: 2px solid var(--border); border-radius: 8px; text-align: center; font-weight: 600;">
                                    <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                        Update
                                    </button>
                                </div>
                            </form>
                            <form action="cart-remove.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="cart_key" value="<?php echo $key; ?>">
                                <button type="submit" style="padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                    Remove
                                </button>
                            </form>
                            <p class="item-subtotal" style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">
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
                        <a href="products.php" style="display: inline-block; padding: 0.75rem 1.5rem; border: 2px solid var(--primary); color: var(--primary); text-decoration: none; border-radius: 8px; font-weight: 600; margin-right: 1rem;">
                            Continue Shopping
                        </a>
                        <form action="cart-clear.php" method="POST" style="display: inline-block;">
                            <button type="submit" onclick="return confirm('Are you sure you want to clear your cart?');" 
                                    style="padding: 0.75rem 1.5rem; border: 2px solid #ef4444; color: #ef4444; background: white; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ORDER SUMMARY -->
                <div style="position: sticky; top: 6rem;">
                    <div style="background: white; border-radius: 16px; padding: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Order Summary</h2>

                        <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Subtotal (<?php echo count($cartItems); ?> unique items):</span>
                                <span style="font-weight: 600;">₹<?php echo number_format($subtotal); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Shipping:</span>
                                <span style="font-weight: 600; color: var(--text-secondary); font-style: italic;">
                                    <?php echo $shippingNote; ?>
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Tax (18% GST):</span>
                                <span style="font-weight: 600; color: var(--text-secondary); font-style: italic;">
                                    <?php echo $taxNote; ?>
                                </span>
                            </div>
                        </div>

                        <div style="background: rgba(99, 102, 241, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                            <p style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">
                                ℹ️ Shipping cost and final tax will be calculated based on your selected shipping method at checkout
                            </p>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem;">
                            <span>Estimated Total:</span>
                            <span style="color: var(--primary);">₹<?php echo number_format($subtotal); ?>+</span>
                        </div>



                        <a href="checkout.php" style="display: block; width: 100%; padding: 1.25rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-align: center; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 1.125rem; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                            Proceed to Checkout
                        </a>

                        <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; text-align: center;">
                            <p style="font-size: 0.875rem; color: var(--text-secondary);">
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
