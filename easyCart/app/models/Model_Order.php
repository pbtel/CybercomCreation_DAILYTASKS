<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Order extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Order';
    }

    public function afterLoad()
    {
        $data = $this->getData();
        if (isset($data['order_id'])) {
            $db = Database::getInstance();

            // Load Items
            $itemSql = "SELECT * FROM sales_order_product WHERE order_id = $1";
            $itemResult = $db->query($itemSql, [$data['order_id']]);
            $data['items'] = $db->fetchAll($itemResult) ?? [];

            // Load Address
            $addrSql = "SELECT * FROM sales_order_address WHERE order_id = $1";
            $addrResult = $db->query($addrSql, [$data['order_id']]);
            $data['address'] = $db->fetch($addrResult) ?? [];

            // Load Payment Info
            $billingSql = "SELECT payment_method FROM sales_order_billing WHERE order_id = $1 LIMIT 1";
            $billingResult = $db->query($billingSql, [$data['order_id']]);
            $billingRow = $db->fetch($billingResult);
            $data['payment_method'] = $billingRow['payment_method'] ?? 'not_specified';

            // Load Shipping Method
            $shipSql = "SELECT shipping_method FROM sales_order_shipping_method WHERE order_id = $1 LIMIT 1";
            $shipResult = $db->query($shipSql, [$data['order_id']]);
            $shipRow = $db->fetch($shipResult);
            $data['shipping_method'] = $shipRow['shipping_method'] ?? 'Standard';

            // Unserialize variant_data and map fields for template compatibility
            foreach ($data['items'] as &$item) {
                // Map unit_price to price
                if (isset($item['unit_price'])) {
                    $item['price'] = $item['unit_price'];
                }

                // Handle variant data
                if (isset($item['variant_data']) && is_string($item['variant_data'])) {
                    $item['variant'] = json_decode($item['variant_data'], true) ?? [];
                }

                // Try to get primary image from database if not present
                if (!isset($item['image']) && isset($item['product_id'])) {
                    $imgSql = "SELECT image_emoji FROM catalog_product_image WHERE product_id = $1 AND is_primary = true LIMIT 1";
                    $imgResult = $db->query($imgSql, [$item['product_id']]);
                    $imgRow = $db->fetch($imgResult);
                    $item['image'] = $imgRow['image_emoji'] ?? '📦';
                }
            }
        }
        $this->setData($data);
        return $this;
    }

    /**
     * Compatibility methods
     */
    public function getById($id)
    {
        $this->load($id);
        if ($this->getId()) {
            return $this->afterLoad()->getData();
        }
        return null;
    }

    public function getByUserId($userId)
    {
        require_once 'Collection_Order.php';
        $collection = new Collection_Order();
        return $collection->addFieldToFilter('user_id', $userId)
            ->addFieldToFilter('status', 'cart', '!=')
            ->setOrder('created_at', 'DESC')
            ->getData();
    }

    public function getByNumber($orderNumber)
    {
        $this->load($orderNumber, 'order_number');
        return $this->afterLoad()->getData();
    }

    public function getByStatus($status)
    {
        require_once 'Collection_Order.php';
        $collection = new Collection_Order();
        return $collection->addFieldToFilter('status', $status)
            ->setOrder('created_at', 'DESC')
            ->getData();
    }

    public function getFullOrder($orderId)
    {
        $this->load($orderId);
        return $this->afterLoad()->getData();
    }

    public function getUserOrders($userId)
    {
        return $this->getByUserId($userId);
    }

    public function getStats($userId)
    {
        $db = Database::getInstance();

        // Total stats - Exclude 'cart' and 'cancelled' for accurate dashboard summary
        $sql = "SELECT COUNT(*) as total_orders, SUM(final_amount) as total_spent 
                FROM sales_order 
                WHERE user_id = $1 AND status != 'cancelled' AND status != 'cart'";
        $result = $db->query($sql, [$userId]);
        $row = $db->fetch($result);

        // Status-wise counts
        $statusSql = "SELECT status, COUNT(*) as count FROM sales_order WHERE user_id = $1 AND status != 'cart' GROUP BY status";
        $statusResult = $db->query($statusSql, [$userId]);
        $statusRows = $db->fetchAll($statusResult);

        $statusCounts = [
            'total' => (int) ($row['total_orders'] ?? 0),
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0,
            'pending' => 0,
            'completed' => 0
        ];

        foreach ($statusRows as $sRow) {
            $status = strtolower($sRow['status']);
            if (isset($statusCounts[$status])) {
                $statusCounts[$status] = (int) $sRow['count'];
            }
        }

        // If 'completed' is used, it often counts as 'delivered' in the UI
        $statusCounts['delivered'] += $statusCounts['completed'];

        return array_merge($statusCounts, [
            'total_orders' => (int) ($row['total_orders'] ?? 0),
            'total_spent' => (float) ($row['total_spent'] ?? 0),
            'currency_symbol' => '₹'
        ]);
    }

    public function getChartData($userId = null)
    {
        $db = Database::getInstance();
        $params = [];
        $where = "WHERE status != 'cancelled' AND status != 'cart'";

        if ($userId) {
            $where .= " AND user_id = $1";
            $params[] = $userId;
        }

        $sql = "SELECT DATE(created_at) as date, SUM(final_amount) as total, MAX(order_number) as order_number 
                FROM sales_order 
                $where 
                GROUP BY DATE(created_at) 
                ORDER BY date ASC 
                LIMIT 30";
        $result = $db->query($sql, $params);
        return $db->fetchAll($result) ?? [];
    }
}
