<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Order extends Core_Model
{
    private $db;

    protected function _init()
    {
        $this->_resourceName = 'Resource_Order';
        $this->db = Database::getInstance();
    }

    public function afterLoad()
    {
        $data = $this->getData();
        if (isset($data['order_id'])) {
            $orderId = $data['order_id'];

            // Load Items
            $itemQuery = (new Query())->select('*')->from('sales_order_product')->where('order_id', $orderId);
            $itemResult = $this->db->query((string) $itemQuery, $itemQuery->getParams());
            $data['items'] = $this->db->fetchAll($itemResult) ?? [];

            // Load Address
            $addrQuery = (new Query())->select('*')->from('sales_order_address')->where('order_id', $orderId);
            $addrResult = $this->db->query((string) $addrQuery, $addrQuery->getParams());
            $data['address'] = $this->db->fetch($addrResult) ?? [];

            // Load Payment Info
            $billingQuery = (new Query())->select('payment_method')->from('sales_order_billing')->where('order_id', $orderId)->limit(1);
            $billingResult = $this->db->query((string) $billingQuery, $billingQuery->getParams());
            $billingRow = $this->db->fetch($billingResult);
            $data['payment_method'] = $billingRow['payment_method'] ?? 'not_specified';

            // Load Shipping Method
            $shipQuery = (new Query())->select('shipping_method')->from('sales_order_shipping_method')->where('order_id', $orderId)->limit(1);
            $shipResult = $this->db->query((string) $shipQuery, $shipQuery->getParams());
            $shipRow = $this->db->fetch($shipResult);
            $data['shipping_method'] = $shipRow['shipping_method'] ?? 'Standard';

            // Unserialize variant_data and map fields for template compatibility
            foreach ($data['items'] as &$item) {
                if (isset($item['unit_price'])) {
                    $item['price'] = $item['unit_price'];
                }

                if (isset($item['variant_data']) && is_string($item['variant_data'])) {
                    $item['variant'] = json_decode($item['variant_data'], true) ?? [];
                }

                if (!isset($item['image']) && isset($item['product_id'])) {
                    $imgQuery = (new Query())
                        ->select('image_emoji')
                        ->from('catalog_product_image')
                        ->where('product_id', $item['product_id'])
                        ->where('is_primary', true)
                        ->limit(1);
                    $imgResult = $this->db->query((string) $imgQuery, $imgQuery->getParams());
                    $imgRow = $this->db->fetch($imgResult);
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
        $query = (new Query())
            ->select(['DATE(created_at) as date', 'SUM(final_amount) as total', 'MAX(order_number) as order_number'])
            ->from('sales_order')
            ->where('status', 'cancelled', '!=')
            ->where('status', 'cart', '!=');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $query->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->limit(30);

        $result = $this->db->query((string) $query, $query->getParams());
        return $this->db->fetchAll($result) ?? [];
    }
}
