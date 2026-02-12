<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Cart extends Core_Model
{
    private $db;

    protected function _init()
    {
        $this->_resourceName = 'Resource_Cart';
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

            // Ensure an active record exists in sales_cart for this session to mark guest as "live"
            $this->getOrCreateDbCart(session_id());

            Session::set('guest_cart', $cart);
        }

        return $cart ?: [];
    }

    /**
     * Unified method to save checkout data (Address, Shipping, Billing)
     */
    public function saveCheckoutData($postData)
    {
        if (Session::get('session_type') === 'user' && Session::has('user')) {
            $user = Session::get('user');
            if (isset($user['user_id'])) {
                return $this->saveUserCheckoutData($user['user_id'], $postData);
            }
        }
        return $this->saveGuestCheckoutData($postData);
    }

    /**
     * Save logged-in user checkout data to sales_order related tables
     */
    public function saveUserCheckoutData($userId, $postData)
    {
        $orderId = $this->getOrCreateDbOrderCart($userId);
        if (!$orderId)
            return false;

        // 1. Address
        $this->db->query("DELETE FROM sales_order_address WHERE order_id = $1", [$orderId]);

        $query = (new Query())
            ->insert('sales_order_address', [
                'order_id' => $orderId,
                'full_name' => trim(($postData['first_name'] ?? '') . ' ' . ($postData['last_name'] ?? '')),
                'phone' => $postData['phone'] ?? '',
                'address_line1' => $postData['address'] ?? '',
                'city' => $postData['city'] ?? '',
                'state' => $postData['state'] ?? '',
                'pincode' => $postData['pincode'] ?? '',
                'country' => $postData['country'] ?? 'India'
            ]);
        $this->db->query((string) $query, $query->getParams());

        // 2. Shipping Method
        if (isset($postData['shipping_method'])) {
            $this->db->query("DELETE FROM sales_order_shipping_method WHERE order_id = $1", [$orderId]);
            $query = (new Query())
                ->insert('sales_order_shipping_method', [
                    'order_id' => $orderId,
                    'shipping_method' => $postData['shipping_method'],
                    'shipping_type' => 'standard'
                ]);
            $this->db->query((string) $query, $query->getParams());
        }

        // 3. Billing (Payment Method & Coupon)
        if (isset($postData['payment_method'])) {
            require_once __DIR__ . '/Model_Coupon.php';
            $couponModel = new Model_Coupon();
            $appliedCoupon = $couponModel->getApplied();
            $couponCode = $appliedCoupon['code'] ?? null;

            $this->db->query("DELETE FROM sales_order_billing WHERE order_id = $1", [$orderId]);
            $query = (new Query())
                ->insert('sales_order_billing', [
                    'order_id' => $orderId,
                    'payment_method' => $postData['payment_method'],
                    'payment_status' => 'pending',
                    'coupon_code' => $couponCode
                ]);
            $this->db->query((string) $query, $query->getParams());
        }

        // 4. Update sales_order with contact info and current totals
        $this->syncUserCartToOrderDb($userId, $this->getUserCart($userId), $postData);

        return true;
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

        $query = (new Query())
            ->insert('sales_cart_address', [
                'cart_id' => $cartId,
                'full_name' => trim(($postData['first_name'] ?? '') . ' ' . ($postData['last_name'] ?? '')),
                'email' => trim($postData['email'] ?? ''),
                'phone' => trim($postData['phone'] ?? ''),
                'address_line1' => trim($postData['address'] ?? ''),
                'city' => trim($postData['city'] ?? ''),
                'state' => trim($postData['state'] ?? ''),
                'pincode' => trim($postData['pincode'] ?? ''),
                'country' => $postData['country'] ?? 'India'
            ]);
        $this->db->query((string) $query, $query->getParams());

        // Shipping Method
        if (isset($postData['shipping_method'])) {
            $this->db->query("DELETE FROM sales_cart_shipping_method WHERE cart_id = $1", [$cartId]);
            $query = (new Query())
                ->insert('sales_cart_shipping_method', [
                    'cart_id' => $cartId,
                    'shipping_method' => $postData['shipping_method'],
                    'shipping_type' => 'standard'
                ]);
            $this->db->query((string) $query, $query->getParams());
        }

        // Billing
        require_once __DIR__ . '/Model_Coupon.php';
        $couponModel = new Model_Coupon();
        $appliedCoupon = $couponModel->getApplied();
        $couponCode = $appliedCoupon['code'] ?? null;

        if (isset($postData['payment_method'])) {
            $this->db->query("DELETE FROM sales_cart_billing WHERE cart_id = $1", [$cartId]);
            $query = (new Query())
                ->insert('sales_cart_billing', [
                    'cart_id' => $cartId,
                    'payment_method' => $postData['payment_method'],
                    'payment_status' => 'pending',
                    'coupon_code' => $couponCode
                ]);
            $this->db->query((string) $query, $query->getParams());
        }

        // --- Update sales_cart with totals and contact info ---
        require_once __DIR__ . '/Model_Shipping.php';
        $shippingModel = new Model_Shipping();

        $subtotal = $this->getSubtotal();
        $discount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $discount;

        $shippingMethod = $postData['shipping_method'] ?? 'standard';
        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $finalAmount = $subtotalAfterCoupon + $shippingCost + $tax;

        $query = (new Query())
            ->update('sales_cart', [
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'final_amount' => $finalAmount,
                'customer_email' => trim($postData['email'] ?? ''),
                'customer_phone' => trim($postData['phone'] ?? ''),
                'updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
            ])
            ->where('cart_id', $cartId);

        $this->db->query((string) $query, $query->getParams());

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
            $this->
                setCurrentCart($cart);
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
    public function syncUserCartToOrderDb($userId, $cart, $postData = [])
    {
        $orderId = $this->getOrCreateDbOrderCart($userId);

        if ($orderId) {
            // Clear existing items in this order cart
            $query = (new Query())
                ->delete('sales_order_product')
                ->where('order_id', $orderId);
            $this->db->query((string) $query, $query->getParams());

            $subtotal = 0;
            require_once __DIR__ . '/Model_Discount.php';
            $discountModel = new Model_Discount();

            // Insert new items
            foreach ($cart as $item) {
                $selQuery = (new Query())
                    ->select(['name', 'price'])
                    ->from('catalog_product_entity')
                    ->where('entity_id', $item['product_id']);

                $product = $this->db->fetch($this->db->query((string) $selQuery, $selQuery->getParams()));

                if ($product) {
                    $itemPrice = (float) $product['price'];

                    // Use Discount Model for accurate item subtotal
                    $discountInfo = $discountModel->calculateItemTotal($itemPrice, $item['quantity']);
                    $itemSubtotal = $discountInfo['total'];
                    $subtotal += $itemSubtotal;

                    $query = (new Query())
                        ->insert('sales_order_product', [
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'product_name' => $product['name'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $itemPrice,
                            'variant_data' => json_encode($item['variant']),
                            'subtotal' => $itemSubtotal,
                            'created_at' => isset($item['added_at']) ? (new DateTime("@" . $item['added_at'], new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s') : (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
                        ]);
                    $this->db->query((string) $query, $query->getParams());
                }
            }

            // --- Re-calculate full totals ---
            require_once __DIR__ . '/Model_Shipping.php';
            require_once __DIR__ . '/Model_Coupon.php';
            $shippingModel = new Model_Shipping();
            $couponModel = new Model_Coupon();

            $discount = $couponModel->calculateDiscount($subtotal);
            $subtotalAfterCoupon = $subtotal - $discount;

            // Get shipping method from DB if not in postData
            $shippingMethod = $postData['shipping_method'] ?? null;
            if (!$shippingMethod) {
                $shipQuery = (new Query())
                    ->select('shipping_method')
                    ->from('sales_order_shipping_method')
                    ->where('order_id', $orderId);
                $shipRow = $this->db->fetch($this->db->query((string) $shipQuery, $shipQuery->getParams()));
                $shippingMethod = $shipRow['shipping_method'] ?? 'standard';
            }

            $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
            $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
            $finalAmount = $subtotalAfterCoupon + $shippingCost + $tax;

            $email = $postData['email'] ?? null;
            $phone = $postData['phone'] ?? null;

            $updateData = [
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'final_amount' => $finalAmount,
                'updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
            ];

            if ($email) {
                $updateData['customer_email'] = $email;
            }
            if ($phone) {
                $updateData['customer_phone'] = $phone;
            }

            $query = (new Query())
                ->update('sales_order', $updateData)
                ->where('order_id', $orderId);

            $this->db->query((string) $query, $query->getParams());
        }
    }

    /**
     * Get or create active order for cart
     */
    public function getOrCreateDbOrderCart($userId, $forceNew = false)
    {
        if (!$forceNew) {
            $selQuery = (new Query())
                ->select('order_id')
                ->from('sales_order')
                ->where('user_id', $userId)
                ->where('status', 'cart')
                ->limit(1);

            $result = $this->db->query((string) $selQuery, $selQuery->getParams());
            $order = $this->db->fetch($result);

            if ($order) {
                return $order['order_id'];
            }
        }

        // Create new order with status 'cart'
        $orderNumber = 'ORD-CART-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $insQuery = (new Query())
            ->insert('sales_order', [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'subtotal' => 0,
                'final_amount' => 0,
                'status' => 'cart',
                'created_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
            ]);
        $this->db->query((string) $insQuery, $insQuery->getParams());

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
            $delQuery = (new Query())
                ->delete('sales_cart_product')
                ->where('cart_id', $cartId);
            $this->db->query((string) $delQuery, $delQuery->getParams());

            // Insert new items
            foreach ($cart as $item) {
                $variantJson = json_encode($item['variant']);

                // Fix timezone to IST
                $dt = new DateTime("@" . $item['added_at']);
                $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                $addedAt = $dt->format('Y-m-d H:i:s');

                $insQuery = (new Query())
                    ->insert('sales_cart_product', [
                        'cart_id' => $cartId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'variant_data' => json_encode($item['variant']),
                        'added_at' => $addedAt
                    ]);
                $this->db->query((string) $insQuery, $insQuery->getParams());
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

        // Retrieve existing shipping choice or default
        $shipQuery = (new Query())
            ->select('shipping_method')
            ->from('sales_cart_shipping_method')
            ->where('cart_id', $cartId);
        $shippingResult = $this->db->fetch($this->db->query((string) $shipQuery, $shipQuery->getParams()));
        $shippingMethod = $shippingResult['shipping_method'] ?? 'standard';

        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $finalAmount = $subtotalAfterCoupon + $shippingCost + $tax;

        $query = (new Query())
            ->update('sales_cart', [
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'final_amount' => $finalAmount,
                'updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
            ])
            ->where('cart_id', $cartId);

        $this->db->query((string) $query, $query->getParams());
    }

    /**
     * Get or create database cart
     */
    private function getOrCreateDbCart($sessionId)
    {
        // Use robust matching for session_id and explicit boolean 'true'
        $sql = "SELECT cart_id FROM sales_cart WHERE LOWER(TRIM(session_id)) = LOWER(TRIM($1)) AND is_active = 't' LIMIT 1";
        $result = $this->db->query($sql, [$sessionId]);
        $cart = $this->db->fetch($result);

        if ($cart) {
            return $cart['cart_id'];
        }

        // Guard: Don't create new active rows if already logged in as a user
        if (Session::get('session_type') === 'user' && Session::isLoggedIn()) {
            return null;
        }

        // Create new cart row with is_active = true
        $insQuery = (new Query())
            ->insert('sales_cart', [
                'session_id' => $sessionId,
                'is_active' => 't'
            ]);
        $this->db->query((string) $insQuery, $insQuery->getParams());

        return $this->db->lastInsertId('sales_cart', 'cart_id');
    }

    /**
     * Merge guest cart with user cart on login
     */
    public function mergeGuestCart($userId, $userEmail = null)
    {
        $guestCart = $this->loadGuestCartFromDb(); // Load from DB for accuracy
        $userCart = $this->loadUserCartFromDb($userId);

        foreach ($guestCart as $key => $item) {
            if (isset($userCart[$key])) {
                $userCart[$key]['quantity'] += $item['quantity'];
                // Keep the more recent added_at
                $userCart[$key]['added_at'] = max($userCart[$key]['added_at'] ?? 0, $item['added_at'] ?? 0);
            } else {
                $userCart[$key] = $item;
            }
        }

        // Deactivate guest cart in DB
        $sessionId = session_id();

        // --- DATA MIGRATION START ---
        // Retrieve guest checkout data before deactivating
        $selQuery = (new Query())
            ->select('cart_id')
            ->from('sales_cart')
            ->where('is_active', true);

        if ($userEmail) {
            // Correctly handle OR condition: (session_id = ? OR customer_email = ?)
            $selQuery->whereRaw("(session_id = ? OR LOWER(TRIM(customer_email)) = LOWER(TRIM(?)))", [$sessionId, $userEmail]);
        } else {
            $selQuery->where('session_id', $sessionId);
        }
        $selQuery->orderBy('updated_at', 'DESC')->limit(1);

        $cartIdRow = $this->db->fetch($this->db->query((string) $selQuery, $selQuery->getParams()));
        $cartId = $cartIdRow['cart_id'] ?? null;

        // --- UNIVERSAL DEACTIVATION ---
        // This MUST run regardless of whether we found a specifically populated guest cart
        $now = (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s');

        // 1. Perform a total sweep for this session/email - using raw SQL for boolean safety
        $sweepSql = "UPDATE sales_cart SET is_active = 'f', user_id = $1, updated_at = $2 WHERE is_active = 't'";
        $sweepParams = [$userId, $now];
        $sweepSql .= " AND (LOWER(TRIM(session_id)) = LOWER(TRIM($3))";
        $sweepParams[] = $sessionId;
        if ($userEmail) {
            $sweepSql .= " OR LOWER(TRIM(customer_email)) = LOWER(TRIM($4))";
            $sweepParams[] = $userEmail;
        }
        $sweepSql .= ")";
        $this->db->query($sweepSql, $sweepParams);

        // 2. Specific deactivation for the identified cart ID if it exists
        if ($cartId) {
            $this->db->query(
                "UPDATE sales_cart SET is_active = 'f', user_id = $1, updated_at = $2 WHERE cart_id = $3",
                [$userId, $now, $cartId]
            );
        }
        // --- DEACTIVATION END ---

        if ($cartId) {
            // Retrieve guest related data
            $addrQuery = (new Query())->select('*')->from('sales_cart_address')->where('cart_id', $cartId);
            $address = $this->db->fetch($this->db->query((string) $addrQuery, $addrQuery->getParams()));

            $billQuery = (new Query())->select('*')->from('sales_cart_billing')->where('cart_id', $cartId);
            $billing = $this->db->fetch($this->db->query((string) $billQuery, $billQuery->getParams()));

            $shipQuery = (new Query())->select('*')->from('sales_cart_shipping_method')->where('cart_id', $cartId);
            $shipping = $this->db->fetch($this->db->query((string) $shipQuery, $shipQuery->getParams()));

            // Get user's active cart order - FORCE NEW RECORD AS REQUESTED
            $orderId = $this->getOrCreateDbOrderCart($userId, true);

            if ($orderId) {
                // Migrate Address - Only migrate if address has content
                if ($address && !empty($address['full_name'])) {
                    $insQuery = (new Query())
                        ->insert('sales_order_address', [
                            'order_id' => $orderId,
                            'full_name' => $address['full_name'],
                            'phone' => $address['phone'] ?? '',
                            'address_line1' => $address['address_line1'] ?? '',
                            'address_line2' => $address['address_line2'] ?? '',
                            'city' => $address['city'] ?? '',
                            'state' => $address['state'] ?? '',
                            'pincode' => $address['pincode'] ?? '',
                            'country' => $address['country'] ?? 'India'
                        ]);
                    $this->db->query((string) $insQuery, $insQuery->getParams());
                }

                // Migrate Billing
                if ($billing) {
                    $insQuery = (new Query())
                        ->insert('sales_order_billing', [
                            'order_id' => $orderId,
                            'payment_method' => $billing['payment_method'],
                            'payment_status' => 'pending',
                            'coupon_code' => $billing['coupon_code']
                        ]);
                    $this->db->query((string) $insQuery, $insQuery->getParams());
                }

                // Migrate Shipping Method
                if ($shipping) {
                    $insQuery = (new Query())
                        ->insert('sales_order_shipping_method', [
                            'order_id' => $orderId,
                            'shipping_method' => $shipping['shipping_method'],
                            'shipping_type' => $shipping['shipping_type'] ?? 'standard'
                        ]);
                    $this->db->query((string) $insQuery, $insQuery->getParams());
                }

                // Update sales_order with migrated contact details and guest coupon
                $totalQuery = (new Query())
                    ->select(['customer_email', 'customer_phone', 'coupon_code'])
                    ->from('sales_cart')
                    ->where('cart_id', $cartId);
                $guestMain = $this->db->fetch($this->db->query((string) $totalQuery, $totalQuery->getParams()));

                if ($guestMain) {
                    $updData = ['updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')];
                    if (!empty($guestMain['customer_email']))
                        $updData['customer_email'] = $guestMain['customer_email'];
                    if (!empty($guestMain['customer_phone']))
                        $updData['customer_phone'] = $guestMain['customer_phone'];

                    if (count($updData) > 1) { // more than just updated_at
                        $updQuery = (new Query())->update('sales_order', $updData)->where('order_id', $orderId);
                        $this->db->query((string) $updQuery, $updQuery->getParams());
                    }
                }
            }
        }
        // --- DATA MIGRATION END ---

        // Save merged cart to user's order-based cart
        $this->saveUserCart($userId, $userCart);
        $this->syncUserCartToOrderDb($userId, $userCart);

        // --- Global Sync: Sync session pending data to match migrated DB data ---
        $migratedData = $this->getCheckoutData($userId);
        if (!empty($migratedData)) {
            $currentPending = Session::get('pending_checkout_data', []);
            $newPending = array_merge($currentPending, $migratedData);
            $newPending['_timestamp'] = time();
            Session::set('pending_checkout_data', $newPending);
        }

        Session::set('guest_cart', []);

        return $userCart;
    }

    public function deactivateGuestCart($userId = null)
    {
        $sessionId = session_id();
        $now = (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s');

        // Use raw SQL for robust boolean and session matching
        $sql = "UPDATE sales_cart SET is_active = 'f', updated_at = $1";
        $params = [$now];

        if ($userId) {
            $sql .= ", user_id = $2";
            $params[] = $userId;
        }

        $sql .= " WHERE LOWER(TRIM(session_id)) = LOWER(TRIM($" . (count($params) + 1) . ")) AND is_active = 't'";
        $params[] = $sessionId;

        $this->db->query($sql, $params);
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
    WHERE LOWER(TRIM(c.session_id)) = LOWER(TRIM($1)) AND c.is_active = 't'";
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
                'added_at' => strtotime($row['added_at'] ?? $row['created_at'] ?? 'now')
            ];
        }
        return $cart;
    }

    /**
     * Retrieve full checkout data for a user (Address, Billing, Shipping)
     */
    public function getCheckoutData($userId)
    {
        $orderId = $this->getOrCreateDbOrderCart($userId);

        $data = [];

        // 1. Get fundamental order info (Email/Phone)
        $order = $this->db->fetch($this->db->query("SELECT customer_email, customer_phone FROM sales_order WHERE order_id = $1", [$orderId]));
        if ($order) {
            if (!empty($order['customer_email']))
                $data['email'] = $order['customer_email'];
            if (!empty($order['customer_phone']))
                $data['phone'] = $order['customer_phone'];
        }

        // 2. Address
        $address = $this->db->fetch($this->db->query("SELECT * FROM sales_order_address WHERE order_id = $1", [$orderId]));
        if ($address && (!empty($address['full_name']) || !empty($address['address_line1']))) {
            // Split full_name robustly
            $parts = explode(' ', trim($address['full_name'] ?? ''));
            if (count($parts) > 1) {
                $data['first_name'] = $parts[0];
                array_shift($parts);
                $data['last_name'] = implode(' ', $parts);
            } else {
                $data['first_name'] = $parts[0] ?? '';
                $data['last_name'] = '';
            }
            // Prioritize phone from address table if present
            if (!empty($address['phone'])) {
                $data['phone'] = $address['phone'];
            }
            $data['address'] = $address['address_line1'];
            $data['city'] = $address['city'];
            $data['state'] = $address['state'];
            $data['pincode'] = $address['pincode'];
            $data['country'] = $address['country'];
        } else {
            // Fallback to user account info for name/email if address is missing
            $user = $this->db->fetch($this->db->query("SELECT name, email FROM customer_entity WHERE entity_id = $1", [$userId]));
            if ($user) {
                if (empty($data['first_name'])) {
                    $parts = explode(' ', trim($user['name']));
                    $data['first_name'] = $parts[0] ?? '';
                    if (empty($data['last_name'])) {
                        $data['last_name'] = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
                    }
                }
                if (empty($data['email'])) {
                    $data['email'] = $user['email'];
                }
            }
        }

        // 3. Billing
        $billing = $this->db->fetch($this->db->query("SELECT * FROM sales_order_billing WHERE order_id = $1", [$orderId]));
        if ($billing) {
            $data['payment_method'] = $billing['payment_method'];
            $data['coupon_code'] = $billing['coupon_code'];
        }

        // 4. Shipping
        $ship = $this->db->fetch($this->db->query("SELECT * FROM sales_order_shipping_method WHERE order_id = $1", [$orderId]));
        if ($ship) {
            $data['shipping_method'] = $ship['shipping_method'];
        }

        return $data;
    }
}