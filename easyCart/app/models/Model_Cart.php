<?php

/**
 * Cart Model
 * Handles all cart-related operations
 */
class Model_Cart
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get current cart based on session type
     */
    public function getCurrentCart()
    {
        if (Session::get('session_type') === 'user' && Session::has('user')) {
            $user = Session::get('user');
            if (isset($user['user_id'])) {
                $cart = $this->getUserCart($user['user_id']);
                if (empty($cart)) {
                    // Try loading from DB
                    $cart = $this->loadUserCartFromDb($user['user_id']);
                    if (!empty($cart)) {
                        $this->saveUserCart($user['user_id'], $cart);
                    }
                }
                return $cart;
            }
        }

        $cart = Session::get('guest_cart', null);
        if ($cart === null) {
            // Load from DB
            $cart = $this->loadGuestCartFromDb();
            Session::set('guest_cart', $cart);
        }

        return $cart ?: [];
    }

    /**
     * Save guest checkout data to sales_cart tables.
     */
    public function saveGuestCheckoutData($postData)
    {
        $guestCart = $this->getCurrentCart();
        if (empty($guestCart)) {
            return false;
        }

        $sessionId = session_id();
        $cartId = $this->getOrCreateDbCart($sessionId);

        // Address
        $this->db->query("DELETE FROM sales_cart_address WHERE cart_id = $1", [$cartId]);
        $fullName = $postData['first_name'] . ' ' . $postData['last_name'];
        $this->db->query(
            "INSERT INTO sales_cart_address (cart_id, full_name, email, phone, address_line1, city, state, pincode, country) 
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)",
            [
                $cartId,
                $fullName,
                $postData['email'] ?? '',
                $postData['phone'],
                $postData['address'],
                $postData['city'],
                $postData['state'],
                $postData['pincode'],
                $postData['country'] ?? 'India'
            ]
        );

        // Shipping Method
        if (isset($postData['shipping_method'])) {
            $this->db->query("DELETE FROM sales_cart_shipping_method WHERE cart_id = $1", [$cartId]);
            $this->db->query(
                "INSERT INTO sales_cart_shipping_method (cart_id, shipping_method, shipping_type) 
                 VALUES ($1, $2, $3)",
                [$cartId, $postData['shipping_method'], 'standard']
            );
        }

        // Billing
        if (isset($postData['payment_method'])) {
            // Get applied coupon code
            require_once __DIR__ . '/Model_Coupon.php';
            $couponModel = new Model_Coupon();
            $appliedCoupon = $couponModel->getApplied();
            $couponCode = $appliedCoupon['code'] ?? null;

            $this->db->query("DELETE FROM sales_cart_billing WHERE cart_id = $1", [$cartId]);
            $this->db->query(
                "INSERT INTO sales_cart_billing (cart_id, payment_method, payment_status, coupon_code) 
                 VALUES ($1, $2, $3, $4)",
                [$cartId, $postData['payment_method'], 'pending', $couponCode]
            );
        }

        // --- NEW: Update sales_cart with totals and contact info ---
        // Calculate Totals using models
        require_once __DIR__ . '/Model_Shipping.php';
        require_once __DIR__ . '/Model_Coupon.php';
        $shippingModel = new Model_Shipping();
        $couponModel = new Model_Coupon();

        $subtotal = $this->getSubtotal();
        // Use CouponModel for cart-wide discount
        $discount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $discount;

        $shippingMethod = $postData['shipping_method'] ?? 'standard';
        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $finalAmount = $subtotalAfterCoupon + $shippingCost + $tax;

        $this->db->query(
            "UPDATE sales_cart SET 
                subtotal = $1, 
                discount_amount = $2, 
                shipping_cost = $3, 
                tax = $4, 
                final_amount = $5, 
                customer_email = $6, 
                customer_phone = $7,
                updated_at = NOW()
            WHERE cart_id = $8",
            [
                $subtotal,
                $discount,
                $shippingCost,
                $tax,
                $finalAmount,
                $postData['email'] ?? null,
                $postData['phone'] ?? null,
                $cartId
            ]
        );

        return true;
    }
    public function setCurrentCart($cart)
    {
        if (Session::get('session_type') === 'user' && Session::has('user')) {
            $user = Session::get('user');
            if (isset($user['user_id'])) {
                $this->saveUserCart($user['user_id'], $cart);
                // Sync to database sales_order table
                $this->syncUserCartToOrderDb($user['user_id'], $cart);
            }
        } else {
            Session::set('guest_cart', $cart);
            // Sync to database sales_cart table
            $this->syncGuestCartToDb($cart);
        }
    }

    /**
     * Add item to cart
     */
    public function addToCart($productId, $quantity = 1, $variant = [])
    {
        $cart = $this->getCurrentCart();
        $cartItemKey = $productId . '_' . md5(serialize($variant));

        if (isset($cart[$cartItemKey])) {
            $cart[$cartItemKey]['quantity'] += $quantity;
        } else {
            $cart[$cartItemKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'variant' => $variant,
                'added_at' => time()
            ];
        }

        $this->setCurrentCart($cart);
        return true;
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($cartItemKey, $quantity)
    {
        $cart = $this->getCurrentCart();

        if (isset($cart[$cartItemKey])) {
            if ($quantity <= 0) {
                unset($cart[$cartItemKey]);
            } else {
                $cart[$cartItemKey]['quantity'] = $quantity;
            }
            $this->setCurrentCart($cart);
            return true;
        }
        return false;
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartItemKey)
    {
        $cart = $this->getCurrentCart();

        if (isset($cart[$cartItemKey])) {
            unset($cart[$cartItemKey]);
            $this->setCurrentCart($cart);
            return true;
        }
        return false;
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        $this->setCurrentCart([]);
        return true;
    }

    /**
     * Get cart items
     */
    public function getItems()
    {
        return $this->getCurrentCart();
    }

    /**
     * Get cart count
     */
    public function getCount()
    {
        $cart = $this->getCurrentCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotal()
    {
        $cart = $this->getCurrentCart();
        $total = 0;

        require_once __DIR__ . '/Model_Product.php';
        require_once __DIR__ . '/Model_Discount.php';
        $productModel = new Model_Product();
        $discountModel = new Model_Discount();

        foreach ($cart as $item) {
            $product = $productModel->getById($item['product_id']);
            if ($product) {
                $discountInfo = $discountModel->calculateItemTotal($product['price'], $item['quantity']);
                $total += $discountInfo['total'];
            }
        }

        return $total;
    }

    /**
     * Get cart items with full product details
     */
    public function getItemsWithDetails()
    {
        $cart = $this->getCurrentCart();
        $cartDetails = [];

        require_once __DIR__ . '/Model_Product.php';
        require_once __DIR__ . '/Model_Discount.php';
        $productModel = new Model_Product();
        $discountModel = new Model_Discount();

        foreach ($cart as $key => $item) {
            $product = $productModel->getById($item['product_id']);
            if ($product) {
                $discountInfo = $discountModel->calculateItemTotal($product['price'], $item['quantity']);

                $cartDetails[$key] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'variant' => $item['variant'],
                    'subtotal' => $discountInfo['total'],
                    'discount_percent' => $discountInfo['discount_percent'],
                    'unit_price_original' => $discountInfo['unit_price_original'],
                    'unit_price_discounted' => $discountInfo['unit_price_discounted'],
                    'first_unit_savings' => $discountInfo['first_unit_savings'],
                    'total_savings' => $discountInfo['total_savings'],
                    'full_price_total' => $discountInfo['full_price_total']
                ];
            }
        }

        return $cartDetails;
    }

    /**
     * Get user cart from session
     */
    private function getUserCart($userId)
    {
        $userCarts = Session::get('user_carts', []);
        return isset($userCarts[$userId]) ? $userCarts[$userId] : [];
    }

    /**
     * Save user cart to session
     */
    private function saveUserCart($userId, $cart)
    {
        $userCarts = Session::get('user_carts', []);
        $userCarts[$userId] = $cart;
        Session::set('user_carts', $userCarts);
    }

    /**
     * Sync user cart to sales_order database table
     */
    private function syncUserCartToOrderDb($userId, $cart)
    {
        $orderId = $this->getOrCreateDbOrderCart($userId);

        if ($orderId) {
            // Clear existing items in this order cart
            $this->db->query("DELETE FROM sales_order_product WHERE order_id = $1", [$orderId]);

            $subtotal = 0;
            // Insert new items
            foreach ($cart as $item) {
                $product = $this->db->fetch($this->db->query("SELECT name, price FROM catalog_product_entity WHERE entity_id = $1", [$item['product_id']]));
                if ($product) {
                    $itemPrice = (float) $product['price'];
                    $itemSubtotal = $itemPrice * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $variantJson = json_encode($item['variant']);
                    $this->db->query(
                        "INSERT INTO sales_order_product (order_id, product_id, product_name, quantity, unit_price, variant_data, subtotal, created_at) 
                         VALUES ($1, $2, $3, $4, $5, $6, $7, NOW())",
                        [$orderId, $item['product_id'], $product['name'], $item['quantity'], $itemPrice, $variantJson, $itemSubtotal]
                    );
                }
            }

            // Update order totals
            $this->db->query("UPDATE sales_order SET subtotal = $1, final_amount = $1, updated_at = NOW() WHERE order_id = $2", [$subtotal, $orderId]);
        }
    }

    /**
     * Get or create active order for cart
     */
    private function getOrCreateDbOrderCart($userId)
    {
        $result = $this->db->query(
            "SELECT order_id FROM sales_order WHERE user_id = $1 AND status = 'cart' LIMIT 1",
            [$userId]
        );
        $order = $this->db->fetch($result);

        if ($order) {
            return $order['order_id'];
        }

        // Create new order with status 'cart'
        $orderNumber = 'ORD-CART-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $this->db->query(
            "INSERT INTO sales_order (user_id, order_number, subtotal, final_amount, status, created_at, updated_at) 
             VALUES ($1, $2, 0, 0, 'cart', NOW(), NOW())",
            [$userId, $orderNumber]
        );

        return $this->db->lastInsertId('sales_order', 'order_id');
    }

    /**
     * Sync guest cart to database
     */
    private function syncGuestCartToDb($cart)
    {
        // Get or create cart in database
        $sessionId = session_id();
        $cartId = $this->getOrCreateDbCart($sessionId);

        if ($cartId) {
            // Clear existing items
            $this->db->query("DELETE FROM sales_cart_product WHERE cart_id = $1", [$cartId]);

            // Insert new items
            foreach ($cart as $item) {
                $variantJson = json_encode($item['variant']);

                // Fix timezone to IST
                $dt = new DateTime("@" . $item['added_at']);
                $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                $addedAt = $dt->format('Y-m-d H:i:s');

                $this->db->query(
                    "INSERT INTO sales_cart_product (cart_id, product_id, quantity, variant_data, added_at) 
                     VALUES ($1, $2, $3, $4, $5)",
                    [$cartId, $item['product_id'], $item['quantity'], $variantJson, $addedAt]
                );
            }
        }

        // --- NEW: Continuously update cart totals ---
        require_once __DIR__ . '/Model_Shipping.php';
        require_once __DIR__ . '/Model_Coupon.php';
        $shippingModel = new Model_Shipping();
        $couponModel = new Model_Coupon();

        $subtotal = $this->getSubtotal();
        $discount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $discount;

        // Get applied coupon code
        $appliedCoupon = $couponModel->getApplied();
        $couponCode = $appliedCoupon['code'] ?? null;

        // Retrieve existing shipping choice or default
        $shippingResult = $this->db->fetch($this->db->query("SELECT shipping_method FROM sales_cart_shipping_method WHERE cart_id = $1", [$cartId]));
        $shippingMethod = $shippingResult['shipping_method'] ?? 'standard';

        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $finalAmount = $subtotalAfterCoupon + $shippingCost + $tax;

        // Get existing contact info to avoid overwriting with null if possible (though usually handled in checkout save)
        // But for sync, we just want to update totals.

        $this->db->query(
            "UPDATE sales_cart SET 
                subtotal = $1, 
                discount_amount = $2, 
                shipping_cost = $3, 
                tax = $4, 
                final_amount = $5,
                updated_at = NOW()
            WHERE cart_id = $6",
            [
                $subtotal,
                $discount,
                $shippingCost,
                $tax,
                $finalAmount,
                $cartId
            ]
        );
    }

    /**
     * Get or create database cart
     */
    private function getOrCreateDbCart($sessionId)
    {
        $result = $this->db->query(
            "SELECT cart_id FROM sales_cart WHERE session_id = $1 AND is_active = true",
            [$sessionId]
        );
        $cart = $this->db->fetch($result);

        if ($cart) {
            return $cart['cart_id'];
        }

        // Create new cart
        $this->db->query(
            "INSERT INTO sales_cart (session_id, is_active, created_at, updated_at) 
             VALUES ($1, true, NOW(), NOW())",
            [$sessionId]
        );

        return $this->db->lastInsertId('sales_cart', 'cart_id');
    }

    /**
     * Merge guest cart with user cart on login
     */
    public function mergeGuestCart($userId)
    {
        $guestCart = $this->loadGuestCartFromDb(); // Load from DB for accuracy
        $userCart = $this->loadUserCartFromDb($userId);

        foreach ($guestCart as $key => $item) {
            if (isset($userCart[$key])) {
                $userCart[$key]['quantity'] += $item['quantity'];
            } else {
                $userCart[$key] = $item;
            }
        }

        // Deactivate guest cart in DB
        $sessionId = session_id();

        // --- DATA MIGRATION START ---
        // Retrieve guest checkout data before deactivating
        $cartIdRow = $this->db->fetch($this->db->query("SELECT cart_id FROM sales_cart WHERE session_id = $1 AND is_active = true", [$sessionId]));
        $cartId = $cartIdRow['cart_id'] ?? null;

        if ($cartId) {
            $address = $this->db->fetch($this->db->query("SELECT * FROM sales_cart_address WHERE cart_id = $1", [$cartId]));
            $billing = $this->db->fetch($this->db->query("SELECT * FROM sales_cart_billing WHERE cart_id = $1", [$cartId]));
            $shipping = $this->db->fetch($this->db->query("SELECT * FROM sales_cart_shipping_method WHERE cart_id = $1", [$cartId]));

            // Get user's active cart order
            $orderId = $this->getOrCreateDbOrderCart($userId);

            if ($orderId) {
                // Migrate Address
                if ($address) {
                    $this->db->query("DELETE FROM sales_order_address WHERE order_id = $1", [$orderId]);
                    $this->db->query(
                        "INSERT INTO sales_order_address (order_id, full_name, phone, address_line1, address_line2, city, state, pincode, country) 
                         VALUES ($1, $2, $3, $4, '', $5, $6, $7, $8)",
                        [
                            $orderId,
                            $address['full_name'],
                            $address['phone'],
                            $address['address_line1'],
                            $address['city'],
                            $address['state'],
                            $address['pincode'],
                            $address['country']
                        ]
                    );

                    // --- NEW: Update sales_order with migrated totals and contact info ---
                    // Retrieve cart totals
                    $cartTotals = $this->db->fetch($this->db->query("SELECT subtotal, discount_amount, shipping_cost, tax, final_amount FROM sales_cart WHERE cart_id = $1", [$cartId]));

                    if ($cartTotals) {
                        $this->db->query(
                            "UPDATE sales_order SET 
                                customer_email = $1, 
                                customer_phone = $2,
                                subtotal = $3,
                                discount_amount = $4,
                                shipping_cost = $5,
                                tax = $6,
                                final_amount = $7,
                                updated_at = NOW()
                             WHERE order_id = $8",
                            [
                                $address['email'] ?? null,
                                $address['phone'] ?? null,
                                $cartTotals['subtotal'],
                                $cartTotals['discount_amount'],
                                $cartTotals['shipping_cost'],
                                $cartTotals['tax'],
                                $cartTotals['final_amount'],
                                $orderId
                            ]
                        );
                    } else if (!empty($address['email'])) {
                        $this->db->query(
                            "UPDATE sales_order SET customer_email = $1, customer_phone = $2 WHERE order_id = $3",
                            [$address['email'], $address['phone'], $orderId]
                        );
                    }
                }

                // Migrate Billing
                if ($billing) {
                    $this->db->query("DELETE FROM sales_order_billing WHERE order_id = $1", [$orderId]);
                    $this->db->query(
                        "INSERT INTO sales_order_billing (order_id, payment_method, payment_status, coupon_code) 
                         VALUES ($1, $2, 'pending', $3)",
                        [$orderId, $billing['payment_method'], $billing['coupon_code']]
                    );
                }

                // Migrate Shipping Method
                if ($shipping) {
                    $this->db->query("DELETE FROM sales_order_shipping_method WHERE order_id = $1", [$orderId]);
                    $this->db->query(
                        "INSERT INTO sales_order_shipping_method (order_id, shipping_method, shipping_type) 
                         VALUES ($1, $2, $3)",
                        [$orderId, $shipping['shipping_method'], $shipping['shipping_type']]
                    );
                }
            }
        }
        // --- DATA MIGRATION END ---

        $this->db->query(
            "UPDATE sales_cart SET is_active = false, updated_at = NOW() WHERE session_id = $1 AND is_active = true",
            [$sessionId]
        );

        // Save merged cart to user's order-based cart
        $this->saveUserCart($userId, $userCart);
        $this->syncUserCartToOrderDb($userId, $userCart);

        Session::set('guest_cart', []);

        return $userCart;
    }

    /**
     * Load guest cart from database
     */
    private function loadGuestCartFromDb()
    {
        $sessionId = session_id();
        $sql = "SELECT cp.*, c.cart_id 
                FROM sales_cart_product cp
                JOIN sales_cart c ON cp.cart_id = c.cart_id
                WHERE c.session_id = $1 AND c.is_active = true";
        $result = $this->db->query($sql, [$sessionId]);
        $rows = $this->db->fetchAll($result);

        return $this->formatDbRowsToCart($rows);
    }

    /**
     * Load user cart from database (sales_order table)
     */
    private function loadUserCartFromDb($userId)
    {
        $sql = "SELECT cp.*, o.order_id 
                FROM sales_order_product cp
                JOIN sales_order o ON cp.order_id = o.order_id
                WHERE o.user_id = $1 AND o.status = 'cart'";
        $result = $this->db->query($sql, [$userId]);
        $rows = $this->db->fetchAll($result);

        return $this->formatDbRowsToCart($rows);
    }

    /**
     * Format database rows to session cart format
     */
    private function formatDbRowsToCart($rows)
    {
        $cart = [];
        if (!$rows)
            return [];

        foreach ($rows as $row) {
            $variant = json_decode($row['variant_data'], true) ?: [];
            $cartItemKey = $row['product_id'] . '_' . md5(serialize($variant));

            $cart[$cartItemKey] = [
                'product_id' => (int) $row['product_id'],
                'quantity' => (int) $row['quantity'],
                'variant' => $variant,
                'added_at' => strtotime($row['added_at'] ?: 'now')
            ];
        }
        return $cart;
    }
}
