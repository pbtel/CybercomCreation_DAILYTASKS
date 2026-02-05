<?php

/**
 * Cart Model
 * Handles all cart-related operations
 */
class CartModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get current cart based on session type
     */
    public function getCurrentCart() {
        if (Session::get('session_type') === 'user' && Session::has('user')) {
            $user = Session::get('user');
            if (isset($user['user_id'])) {
                return $this->getUserCart($user['user_id']);
            }
        }
        return Session::get('guest_cart', []);
    }

    /**
     * Set current cart
     */
    public function setCurrentCart($cart) {
        if (Session::get('session_type') === 'user' && Session::has('user')) {
            $user = Session::get('user');
            if (isset($user['user_id'])) {
                $this->saveUserCart($user['user_id'], $cart);
            }
        } else {
            Session::set('guest_cart', $cart);
            // Sync to database
            $this->syncGuestCartToDb($cart);
        }
    }

    /**
     * Add item to cart
     */
    public function addToCart($productId, $quantity = 1, $variant = []) {
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
    public function updateQuantity($cartItemKey, $quantity) {
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
    public function removeItem($cartItemKey) {
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
    public function clearCart() {
        $this->setCurrentCart([]);
        return true;
    }

    /**
     * Get cart items
     */
    public function getItems() {
        return $this->getCurrentCart();
    }

    /**
     * Get cart count
     */
    public function getCount() {
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
    public function getSubtotal() {
        $cart = $this->getCurrentCart();
        $total = 0;
        
        require_once __DIR__ . '/ProductModel.php';
        require_once __DIR__ . '/DiscountModel.php';
        $productModel = new ProductModel();
        $discountModel = new DiscountModel();
        
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
    public function getItemsWithDetails() {
        $cart = $this->getCurrentCart();
        $cartDetails = [];
        
        require_once __DIR__ . '/ProductModel.php';
        require_once __DIR__ . '/DiscountModel.php';
        $productModel = new ProductModel();
        $discountModel = new DiscountModel();
        
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
    private function getUserCart($userId) {
        $userCarts = Session::get('user_carts', []);
        return isset($userCarts[$userId]) ? $userCarts[$userId] : [];
    }

    /**
     * Save user cart to session
     */
    private function saveUserCart($userId, $cart) {
        $userCarts = Session::get('user_carts', []);
        $userCarts[$userId] = $cart;
        Session::set('user_carts', $userCarts);
    }

    /**
     * Sync guest cart to database
     */
    private function syncGuestCartToDb($cart) {
        // Get or create cart in database
        $sessionId = session_id();
        $cartId = $this->getOrCreateDbCart($sessionId);
        
        if ($cartId) {
            // Clear existing items
            $this->db->query("DELETE FROM sales_cart_item WHERE cart_id = $1", [$cartId]);
            
            // Insert new items
            foreach ($cart as $item) {
                $variantJson = json_encode($item['variant']);
                $this->db->query(
                    "INSERT INTO sales_cart_item (cart_id, product_id, quantity, variant_data, added_at) 
                     VALUES ($1, $2, $3, $4, $5)",
                    [$cartId, $item['product_id'], $item['quantity'], $variantJson, date('Y-m-d H:i:s', $item['added_at'])]
                );
            }
        }
    }

    /**
     * Get or create database cart
     */
    private function getOrCreateDbCart($sessionId) {
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
    public function mergeGuestCart($userId) {
        $guestCart = Session::get('guest_cart', []);
        $userCart = $this->getUserCart($userId);
        
        foreach ($guestCart as $key => $item) {
            if (isset($userCart[$key])) {
                $userCart[$key]['quantity'] += $item['quantity'];
            } else {
                $userCart[$key] = $item;
            }
        }
        
        // Deactivate guest cart in DB
        $sessionId = session_id();
        $this->db->query(
            "UPDATE sales_cart SET is_active = false WHERE session_id = $1",
            [$sessionId]
        );
        
        $this->saveUserCart($userId, $userCart);
        Session::set('guest_cart', []);
        
        return $userCart;
    }
}
