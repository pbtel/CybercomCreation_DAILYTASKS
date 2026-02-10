<?php

/**
 * Order Model
 * Handles all order-related database operations
 */
class Model_Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Save order to database (distributed across 5 tables)
     */
    public function save($orderData)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert into sales_order
            $sql = "INSERT INTO sales_order (user_id, order_number, subtotal, discount_amount, tax, shipping_cost, final_amount, status, customer_email, customer_phone, created_at, updated_at) 
                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, NOW(), NOW()) RETURNING order_id";

            // Extract email and phone from address data if not directly provided
            $email = $orderData['customer_email'] ?? ($orderData['address']['email'] ?? null);
            $phone = $orderData['customer_phone'] ?? ($orderData['address']['phone'] ?? null);

            $result = $this->db->query($sql, [
                $orderData['user_id'] ?? null,
                $orderData['order_number'],
                $orderData['subtotal'],
                $orderData['discount'] ?? 0,
                $orderData['tax'] ?? 0,
                $orderData['shipping_cost'] ?? 0,
                $orderData['final_amount'],
                $orderData['status'] ?? 'pending',
                $email,
                $phone
            ]);
            $orderRow = $this->db->fetch($result);
            $orderId = $orderRow['order_id'];

            if (!$orderId) {
                $this->db->rollback();
                return false;
            }

            // 2. Insert order products
            if (isset($orderData['items']) && is_array($orderData['items'])) {
                foreach ($orderData['items'] as $item) {
                    $sql = "INSERT INTO sales_order_product (order_id, product_id, product_name, quantity, unit_price, variant_data, subtotal) 
                            VALUES ($1, $2, $3, $4, $5, $6, $7)";
                    $this->db->query($sql, [
                        $orderId,
                        $item['product_id'],
                        $item['product_name'] ?? 'Product',
                        $item['quantity'],
                        $item['price'],
                        json_encode($item['variant'] ?? []),
                        $item['price'] * $item['quantity']
                    ]);
                }
            }

            // 3. Insert shipping address
            if (isset($orderData['address'])) {
                $addr = $orderData['address'];
                $sql = "INSERT INTO sales_order_address (order_id, full_name, phone, address_line1, address_line2, city, state, pincode, country) 
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                $this->db->query($sql, [
                    $orderId,
                    $addr['full_name'] ?? '',
                    $addr['phone'] ?? '',
                    $addr['address_line1'] ?? '',
                    $addr['address_line2'] ?? '',
                    $addr['city'] ?? '',
                    $addr['state'] ?? '',
                    $addr['postal_code'] ?? '',
                    $addr['country'] ?? 'India'
                ]);
            }

            // 4. Insert shipping method
            $sql = "INSERT INTO sales_order_shipping_method (order_id, shipping_method, shipping_type) 
                    VALUES ($1, $2, $3)";
            $this->db->query($sql, [
                $orderId,
                $orderData['shipping_type'] ?? 'Standard',
                'Method' // or 'Express'/'Standard' if known
            ]);

            // 5. Insert billing info
            $sql = "INSERT INTO sales_order_billing (order_id, payment_method, payment_status, coupon_code) 
                    VALUES ($1, $2, $3, $4)";
            $this->db->query($sql, [
                $orderId,
                $orderData['payment_method'] ?? 'COD',
                'pending',
                $orderData['applied_coupon'] ?? null
            ]);

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Failed to save order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order by ID with all details
     */
    public function getById($orderId)
    {
        $sql = "SELECT o.*, s.shipping_method, b.payment_method
                FROM sales_order o
                LEFT JOIN sales_order_shipping_method s ON o.order_id = s.order_id
                LEFT JOIN sales_order_billing b ON o.order_id = b.order_id
                WHERE o.order_id = $1 AND o.status != 'cart'";
        $result = $this->db->query($sql, [$orderId]);
        $order = $this->db->fetch($result);

        if ($order) {
            // Get order items
            $itemsSql = "SELECT op.*, op.product_name, pi.image_emoji as image
                         FROM sales_order_product op
                         LEFT JOIN catalog_product_entity pe ON op.product_id = pe.entity_id
                         LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                         WHERE op.order_id = $1";
            $itemsResult = $this->db->query($itemsSql, [$orderId]);
            $items = $this->db->fetchAll($itemsResult);

            foreach ($items as &$item) {
                $item['variant'] = json_decode($item['variant_data'], true) ?? [];
                $item['price'] = $item['unit_price']; // Map for view compatibility
            }
            $order['items'] = $items;

            // Get address
            $addrSql = "SELECT * FROM sales_order_address WHERE order_id = $1";
            $addrResult = $this->db->query($addrSql, [$orderId]);
            $order['address'] = $this->db->fetch($addrResult);
        }

        return $order;
    }

    /**
     * Get orders by user ID
     */
    public function getUserOrders($userId)
    {
        $sql = "SELECT o.*, s.shipping_method
                FROM sales_order o
                LEFT JOIN sales_order_shipping_method s ON o.order_id = s.order_id
                WHERE o.user_id = $1 AND o.status != 'cart'
                ORDER BY o.created_at DESC";
        $result = $this->db->query($sql, [$userId]);
        $orders = $this->db->fetchAll($result);

        foreach ($orders as &$order) {
            // Get order items
            $itemsSql = "SELECT op.*, op.product_name, pi.image_emoji as image
                         FROM sales_order_product op
                         LEFT JOIN catalog_product_entity pe ON op.product_id = pe.entity_id
                         LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                         WHERE op.order_id = $1";
            $itemsResult = $this->db->query($itemsSql, [$order['order_id']]);
            $items = $this->db->fetchAll($itemsResult);

            foreach ($items as &$item) {
                $item['variant'] = json_decode($item['variant_data'], true) ?? [];
                $item['price'] = $item['unit_price']; // Map for view compatibility
            }
            $order['items'] = $items;
        }

        return $orders ?? [];
    }

    /**
     * Get orders by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT o.*, s.shipping_method
                FROM sales_order o
                LEFT JOIN sales_order_shipping_method s ON o.order_id = s.order_id
                WHERE o.status = $1 
                ORDER BY o.created_at DESC";
        $result = $this->db->query($sql, [$status]);
        return $this->db->fetchAll($result) ?? [];
    }

    /**
     * Get order statistics for a user
     */
    public function getStats($userId)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered
                FROM sales_order
                WHERE user_id = $1 AND status != 'cart'";

        $result = $this->db->query($sql, [$userId]);
        $stats = $this->db->fetch($result);

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'pending' => (int) ($stats['pending'] ?? 0),
            'processing' => (int) ($stats['processing'] ?? 0),
            'shipped' => (int) ($stats['shipped'] ?? 0),
            'delivered' => (int) ($stats['delivered'] ?? 0)
        ];
    }

    /**
     * Generate unique order number
     */
    public function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    /**
     * Get chart data for orders visualization (individual orders)
     */
    public function getChartData()
    {
        if (!isset($_SESSION['user'])) {
            return [];
        }
        $userId = $_SESSION['user']['user_id'];

        $sql = "SELECT created_at as date, final_amount as total, order_number 
                FROM sales_order 
                WHERE user_id = $1 AND status NOT IN ('cancelled', 'cart') 
                ORDER BY created_at ASC";
        $result = $this->db->query($sql, [$userId]);
        return $this->db->fetchAll($result) ?? [];
    }
}
