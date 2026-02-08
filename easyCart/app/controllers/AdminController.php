<?php

/**
 * Admin Controller
 * Handles administrative backend tasks
 */
class AdminController extends Controller
{

    public function __construct()
    {
        // Simple admin check - only allow admin@easycart.com for now
        $user = Session::get('user', ['logged_in' => false]);
        if (!$user['logged_in'] || $user['email'] !== 'admin@easycart.com') {
            Session::setFlash('error', 'Unauthorized access.');
            $this->redirect('home');
            exit;
        }
    }

    /**
     * Admin Dashboard
     */
    public function index()
    {
        $orderModel = $this->model('OrderModel');
        $productModel = $this->model('ProductModel');

        // Fetch stats
        $db = Database::getInstance();
        $totalSalesResult = $db->query("SELECT SUM(final_amount) as total FROM sales_order WHERE status != 'cancelled'");
        $totalSalesRow = $db->fetch($totalSalesResult);
        $totalSales = $totalSalesRow['total'] ?? 0;

        $totalOrdersResult = $db->query("SELECT COUNT(*) as total FROM sales_order");
        $totalOrdersRow = $db->fetch($totalOrdersResult);
        $totalOrders = $totalOrdersRow['total'] ?? 0;

        $totalUsersResult = $db->query("SELECT COUNT(*) as total FROM users");
        $totalUsersRow = $db->fetch($totalUsersResult);
        $totalUsers = $totalUsersRow['total'] ?? 0;

        $recentOrders = $orderModel->getByStatus('pending');

        $data = [
            'pageTitle' => 'Admin Dashboard',
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * Manage Orders
     */
    public function orders()
    {
        $orderModel = $this->model('OrderModel');

        // Use Database directly for simple admin listing if model doesn't have getAllOrders
        $db = Database::getInstance();
        $sql = "SELECT o.*, u.name as customer_name 
                FROM sales_order o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.created_at DESC";
        $result = $db->query($sql);
        $allOrders = $db->fetchAll($result);

        // Pagination
        $itemsPerPage = 20;
        $currentPage = $this->get('page', 1);
        $totalItems = count($allOrders);
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $pagedOrders = array_slice($allOrders, $pagination->getOffset(), $pagination->getLimit());

        $data = [
            'pageTitle' => 'Manage Orders',
            'orders' => $pagedOrders,
            'pagination' => $pagination,
            'totalItems' => $totalItems
        ];

        $this->view('admin/orders', $data);
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus()
    {
        if ($this->isPost()) {
            $orderId = $this->post('order_id');
            $status = $this->post('status');

            if ($orderId && $status) {
                $db = Database::getInstance();
                $sql = "UPDATE sales_order SET status = $1, updated_at = NOW() WHERE order_id = $2";
                $db->query($sql, [$status, $orderId]);
                Session::setFlash('success', 'Order status updated successfully.');
            }
        }
        $this->redirect('admin/orders');
    }

    /**
     * Manage Products
     */
    public function products()
    {
        $productModel = $this->model('ProductModel');
        $allProducts = $productModel->getAll();

        // Pagination
        $itemsPerPage = 20;
        $currentPage = $this->get('page', 1);
        $totalItems = count($allProducts);
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $pagedProducts = array_slice($allProducts, $pagination->getOffset(), $pagination->getLimit());

        $data = [
            'pageTitle' => 'Manage Products',
            'products' => $pagedProducts,
            'pagination' => $pagination,
            'totalItems' => $totalItems
        ];

        $this->view('admin/products', $data);
    }

    /**
     * Update Product Stock
     */
    public function updateStock()
    {
        if ($this->isPost()) {
            $productId = $this->post('product_id');
            $stock = (int) $this->post('stock');

            if ($productId) {
                $db = Database::getInstance();
                $sql = "UPDATE catalog_product_entity SET stock = $1, updated_at = NOW() WHERE entity_id = $2";
                $db->query($sql, [$stock, $productId]);
                Session::setFlash('success', 'Product stock updated successfully.');
            }
        }
        $this->redirect('admin/products');
    }
}
