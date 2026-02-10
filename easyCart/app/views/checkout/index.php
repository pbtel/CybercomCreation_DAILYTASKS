<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container mt-2">
    <div class="checkout-header">
        <h1 class="checkout-title">Finalize Your Order</h1>
        <p class="checkout-subtitle">Secure encrypted checkout process</p>
    </div>

    <div class="checkout-grid">
        <!-- CHECKOUT FORM -->
        <div>
            <form id="checkout-form" action="<?php echo BASE_URL; ?>/checkout/place" method="POST" autocomplete="off">
                <!-- Fake fields to trick browser autofill -->
                <input type="text" style="display:none">
                <input type="password" style="display:none">
                
                <!-- SHIPPING INFO -->
                <?php 
                    $savedData = $data['savedData'] ?? [];
                    $currentUser = Session::get('user') ?? [];
                    
                    // Helper to get value
                    $getVal = function($key) use ($savedData, $currentUser) {
                        return $savedData[$key] ?? ($currentUser[$key] ?? '');
                    };
                ?>
                <div class="checkout-section">
                    <h2 class="checkout-section-title">📦 Shipping Information</h2>
                    
                    <div class="form-row">
                        <div>
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" required class="form-input" value="<?php echo htmlspecialchars($getVal('first_name')); ?>">
                        </div>
                        <div>
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" required class="form-input" value="<?php echo htmlspecialchars($getVal('last_name')); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" required class="form-input" value="<?php echo htmlspecialchars($savedData['email'] ?? ($currentUser['email'] ?? '')); ?>">
                        </div>
                        <div>
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" required class="form-input" placeholder="+91" value="<?php echo htmlspecialchars($getVal('phone')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Delivery Address *</label>
                        <input type="text" name="address" required class="form-input" placeholder="House No, Street, Area" value="<?php echo htmlspecialchars($getVal('address')); ?>">
                    </div>

                    <div class="form-row form-group">
                        <div>
                            <label class="form-label">City *</label>
                            <input type="text" name="city" required class="form-input" value="<?php echo htmlspecialchars($getVal('city')); ?>">
                        </div>
                        <div>
                            <label class="form-label">State / Region *</label>
                            <input type="text" name="state" required class="form-input" value="<?php echo htmlspecialchars($getVal('state')); ?>">
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <div>
                            <label class="form-label">Postal Code (PIN) *</label>
                            <input type="text" name="pincode" required class="form-input" value="<?php echo htmlspecialchars($getVal('pincode')); ?>">
                        </div>
                        <div>
                            <label class="form-label">Country *</label>
                            <select name="country" required class="form-input" style="height: auto;">
                                <option value="IN" selected>India</option>
                                <option value="US">United States</option>
                                <option value="UK">United Kingdom</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SHIPPING METHOD -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">🚚 Delivery Method</h2>
                    
                    <?php 
                    $shippingModel = new ShippingModel();
                    $methods = ['standard', 'express', 'whiteglove', 'freight'];
                    foreach ($methods as $method): 
                        $available = in_array($method, $availableShippingMethods);
                        $isSelected = ($selectedShippingMethod === $method);
                    ?>
                    <label class="shipping-option <?php echo !$available ? 'disabled' : ''; ?> <?php echo $isSelected ? 'selected' : ''; ?>" id="label-<?php echo $method; ?>">
                        <div class="shipping-option-row">
                            <input type="radio" name="shipping_method" value="<?php echo $method; ?>" 
                                   <?php echo $isSelected ? 'checked' : ''; ?> 
                                   <?php echo !$available ? 'disabled' : ''; ?> 
                                   style="width: 20px; height: 20px; margin-top: 5px;"
                                   onchange="updateOrderSummary()">
                            <div class="shipping-option-details">
                                <div class="shipping-option-title-row">
                                    <span class="shipping-icon">
                                        <?php 
                                        if ($method === 'standard') echo '📦';
                                        elseif ($method === 'express') echo '⚡';
                                        elseif ($method === 'whiteglove') echo '🏆';
                                        elseif ($method === 'freight') echo '🚛';
                                        ?>
                                    </span>
                                    <span class="shipping-name"><?php echo ucfirst($method); ?> Shipping</span>
                                    <?php if ($available): ?>
                                        <span class="shipping-badge badge-green-gradient">
                                            <?php 
                                            if ($method === 'standard') echo 'RELIABLE';
                                            elseif ($method === 'express') echo 'FAST';
                                            elseif ($method === 'whiteglove') echo 'PREMIUM';
                                            elseif ($method === 'freight') echo 'FOR BULK';
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="db text-secondary fs-0-85 mb-0-5">
                                    <?php echo $shippingModel->getMethodDescription($method, $subtotalAfterCoupon); ?>
                                </span>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>

                    <div class="info-note">
                        <p class="fs-0-85 m-0 color-primary">
                            <strong>⚡ Pro Tip:</strong> Express shipping usually arrives within 48 hours for metro cities.
                        </p>
                    </div>
                </div>

                <!-- PAYMENT METHOD -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">💳 Payment Selection</h2>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <span><strong>Cash on Delivery</strong> (Pay when you receive)</span>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="upi">
                        <span><strong>UPI / Digital Wallets</strong> (Instant & Safe)</span>
                    </label>

                    <label class="payment-option last">
                        <input type="radio" name="payment_method" value="card">
                        <span><strong>Credit / Debit Card</strong> (Powered by Razorpay)</span>
                    </label>
                </div>

                <div class="mb-5">
                    <button type="submit" class="checkout-btn">Confirm & Place Order</button>
                    <p class="text-center text-secondary fs-0-8 mt-1">By clicking "Place Order", you agree to EasyCart's Terms of Service.</p>
                </div>
            </form>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="sticky-sidebar">
            <div class="summary-card">
                <h2 class="summary-title">Order Recap</h2>

                <div class="checkout-summary-scroll">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-item">
                        <div class="checkout-item-image">
                            <?php if (strpos($item['product']['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/' . ltrim($item['product']['image'], '/'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                     style="max-width: 40px; max-height: 40px;">
                            <?php else: ?>
                                <span style="font-size: 1.5rem;"><?php echo $item['product']['image']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <p class="font-700 fs-0-9 mb-0-25"><?php echo htmlspecialchars($item['product']['name']); ?></p>
                            <div class="flex-between">
                                <span class="text-secondary fs-0-8">Qty: <?php echo $item['quantity']; ?></span>
                                <span class="font-700 color-primary fs-0-85">₹<?php echo number_format($item['subtotal']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-b pb-1 mb-1 mt-1">
                    <div class="summary-row">
                        <span class="summary-text-secondary">Subtotal Items:</span>
                        <span class="summary-value">₹<?php echo number_format($subtotal); ?></span>
                    </div>
                    
                    <?php if ($appliedCoupon && $couponDiscount > 0): ?>
                    <div class="summary-row">
                        <span class="font-700 text-success">Coupon Savings:</span>
                        <span class="font-700 text-success">-₹<?php echo number_format($couponDiscount); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="summary-row">
                        <span class="summary-text-secondary">Shipping Fee:</span>
                        <span class="summary-value" id="summary-shipping">₹<?php echo number_format($shipping); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-text-secondary">GST (18% Included):</span>
                        <span class="summary-value" id="summary-tax">₹<?php echo number_format($tax); ?></span>
                    </div>
                </div>

                <div class="summary-row fs-1-25 font-800 mb-2">
                    <span>Payable Amount:</span>
                    <span class="color-primary" id="summary-total">₹<?php echo number_format($total); ?></span>
                </div>

                <div class="secure-info">
                    <p class="fs-0-8 m-0 opacity-70">🔒 Trusted & Encrypted Checkout</p>
                    <p class="fs-0-75 text-secondary mt-0-25">Your payment info is never stored on our servers.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    const API_URL = '<?php echo BASE_URL; ?>/api';
    // No need to redeclare subtotalAfterCoupon if not used or already available in wider scope
    // const subtotalAfterCoupon = <?php echo $subtotalAfterCoupon; ?>;

    function updateOrderSummary() {
        // ... (Shipping update logic remains similar but streamlined if needed)
        const selectedRadio = document.querySelector('input[name="shipping_method"]:checked');
        if (!selectedRadio) return;
        
        const selectedMethod = selectedRadio.value;
        const subtotal = <?php echo $subtotalAfterCoupon; ?>; // PHP injection

        // Optimistic UI update or simple fetch to calculate
        // For simplicity, let's just trigger the fetch to update totals
        
        // 1. Update Session
        fetch('<?php echo BASE_URL; ?>/api/shipping-method-update', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'shipping_method=' + encodeURIComponent(selectedMethod)
        });

        // 2. Calculate Costs
        fetch('<?php echo BASE_URL; ?>/api/shipping-calculate', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'shipping_method=' + encodeURIComponent(selectedMethod) + '&subtotal=' + subtotal
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const shipping = data.shipping_cost || 0;
                const tax = data.tax || 0;
                const total = data.total || 0;
                
                document.getElementById('summary-shipping').textContent = '₹' + parseInt(shipping).toLocaleString('en-IN');
                document.getElementById('summary-tax').textContent = '₹' + parseInt(tax).toLocaleString('en-IN');
                document.getElementById('summary-total').textContent = '₹' + parseInt(total).toLocaleString('en-IN');
            }
        });

        // Visual update
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        if(selectedRadio.closest('.shipping-option')) selectedRadio.closest('.shipping-option').classList.add('selected');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- DATA PERSISTENCE LOGIC START ---
        const form = document.getElementById('checkout-form');
        const storageKey = 'easycart_checkout_form_data';

        // 1. Restore data on load
        const savedPacket = sessionStorage.getItem(storageKey);
        const currentSessionId = '<?php echo session_id(); ?>';
        
        if (savedPacket) {
            try {
                const packet = JSON.parse(savedPacket);
                
                // Only restore if from SAME session
                if (packet.sessionId === currentSessionId && packet.formData) {
                    const data = packet.formData;
                    Object.keys(data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'radio' || input.type === 'checkbox') {
                                if (input.value === data[key]) input.checked = true;
                            } else {
                                input.value = data[key];
                            }
                        }
                    });
                    
                    // If shipping method was restored, update summary
                    if (data['shipping_method']) {
                        const radio = form.querySelector(`input[name="shipping_method"][value="${data['shipping_method']}"]`);
                        if (radio) {
                            radio.checked = true;
                            updateOrderSummary();
                        }
                    }
                } else {
                    // Session mismatch - clear stale data
                    sessionStorage.removeItem(storageKey);
                }
            } catch (e) {
                console.error('Error parsing saved checkout data', e);
                sessionStorage.removeItem(storageKey);
            }
        }

        // 2. Save data on input
        form.addEventListener('input', function(e) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            // Bind to session ID to validation freshness
            const storagePacket = {
                sessionId: '<?php echo session_id(); ?>',
                timestamp: new Date().getTime(),
                formData: data
            };
            sessionStorage.setItem(storageKey, JSON.stringify(storagePacket));
        });

        // 3. Clear data on successful submit
        form.addEventListener('submit', function() {
            sessionStorage.removeItem(storageKey);
        });
        // --- DATA PERSISTENCE LOGIC END ---

        // Initial summary update
        updateOrderSummary();
    });
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
