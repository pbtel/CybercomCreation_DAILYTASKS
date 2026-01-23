<?php
$pageTitle = "Checkout";
require_once 'includes/header.php';

$cartItems = getCartItemsWithDetails();
$subtotal = getCartSubtotal();
$shipping = $subtotal > 999 ? 0 : 50;
$tax = round($subtotal * 0.18);
$total = $subtotal + $shipping + $tax;

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
                        
                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                            <input type="radio" name="shipping_method" value="standard" checked style="margin-right: 0.75rem;">
                            <strong>Standard Delivery</strong>
                            <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">5-7 business days</span>
                        </label>

                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                            <input type="radio" name="shipping_method" value="express" style="margin-right: 0.75rem;">
                            <strong>Express Delivery</strong>
                            <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">2-3 business days - Additional ₹100</span>
                        </label>

                        <label style="display: block; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="shipping_method" value="overnight" style="margin-right: 0.75rem;">
                            <strong>Overnight Delivery</strong>
                            <span style="display: block; color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Next business day - Additional ₹250</span>
                        </label>
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
                                <p style="font-weight: 600; color: var(--primary);">₹<?php echo number_format($item['subtotal']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="border-top: 2px solid var(--border); padding-top: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Subtotal:</span>
                            <span style="font-weight: 600;">₹<?php echo number_format($subtotal); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Shipping:</span>
                            <span style="font-weight: 600; color: <?php echo $shipping == 0 ? 'var(--accent)' : 'inherit'; ?>;">
                                <?php echo $shipping == 0 ? 'FREE' : '₹' . number_format($shipping); ?>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-secondary);">Tax:</span>
                            <span style="font-weight: 600;">₹<?php echo number_format($tax); ?></span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: 1rem; border-top: 2px solid var(--border);">
                        <span>Total:</span>
                        <span style="color: var(--primary);">₹<?php echo number_format($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
