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
        $totalSalesResult = $db->query("SELECT SUM(final_amount) as total FROM sales_order WHERE status != 'cancelled' AND status != 'cart'");
        $totalSalesRow = $db->fetch($totalSalesResult);
        $totalSales = $totalSalesRow['total'] ?? 0;

        $totalOrdersResult = $db->query("SELECT COUNT(*) as total FROM sales_order WHERE status != 'cart'");
        $totalOrdersRow = $db->fetch($totalOrdersResult);
        $totalOrders = $totalOrdersRow['total'] ?? 0;

        $totalUsersResult = $db->query("SELECT COUNT(*) as total FROM customer_entity");
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
                LEFT JOIN customer_entity u ON o.user_id = u.entity_id 
                WHERE o.status != 'cart'
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

    /**
     * Import/Export Page
     */
    public function importExport()
    {
        $data = [
            'pageTitle' => 'Import / Export Products'
        ];
        $this->view('admin/import_export', $data);
    }

    /**
     * Process Product Import
     */
    public function processImport()
    {
        if (!$this->isPost()) {
            $this->redirect('admin/importExport');
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Please upload a valid CSV file.');
            $this->redirect('admin/importExport');
            return;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");

        if ($handle === false) {
            Session::setFlash('error', 'Could not open CSV file.');
            $this->redirect('admin/importExport');
            return;
        }

        // Expected header: sku, name, brand, price, stock, description, category, image_url, discount_percent
        $header = fgetcsv($handle);

        $productModel = $this->model('ProductModel');
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Map row to data
            // Assuming order: 0:sku, 1:name, 2:brand, 3:price, 4:stock, 5:description, 6:category, 7:image_url, 8:discount_percent
            if (count($row) < 4) {
                $errorCount++; // Invalid row
                continue;
            }

            $data = [
                'sku' => $row[0] ?? '',
                'name' => $row[1] ?? '',
                'brand' => $row[2] ?? '',
                'price' => $row[3] ?? 0,
                'stock' => $row[4] ?? 0,
                'description' => $row[5] ?? '',
                'category' => $row[6] ?? '',
                'image_url' => $row[7] ?? '',
                'discount_percent' => $row[8] ?? 0
            ];

            // Validation
            if (empty($data['sku']) || empty($data['name']) || empty($data['price'])) {
                $errorCount++;
                $errors[] = "Row with missing required fields (SKU: {$data['sku']})";
                continue;
            }

            // Check duplicate
            $existing = $productModel->getBySku($data['sku']);
            if ($existing) {
                // Update
                if ($productModel->updateProduct($existing['id'], $data)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Failed to update SKU: {$data['sku']}";
                }
            } else {
                // Create
                if ($productModel->createProduct($data)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Failed to create SKU: {$data['sku']}";
                }
            }
        }

        fclose($handle);

        $msg = "Import completed. Success: $successCount, Failed: $errorCount.";
        if (!empty($errors)) {
            $msg .= " Errors: " . implode(", ", array_slice($errors, 0, 5)); // Show first 5 errors
        }

        if ($errorCount > 0) {
            Session::setFlash('warning', $msg);
        } else {
            Session::setFlash('success', $msg);
        }

        $this->redirect('admin/importExport');
    }

    /**
     * Export Products to CSV
     */
    public function exportProducts()
    {
        $productModel = $this->model('ProductModel');
        $products = $productModel->getAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['sku', 'name', 'brand', 'price', 'stock', 'description', 'category', 'image_url', 'discount_percent', 'original_price', 'rating', 'reviews']);

        foreach ($products as $product) {
            fputcsv($output, [
                $product['sku'],
                $product['name'],
                $product['brand'],
                $product['price'],
                $product['stock'],
                $product['description'],
                $product['category'],
                $product['image'] ?? '', // Image emoji/url
                $product['discount_percent'],
                $product['original_price'],
                $product['rating'],
                $product['reviews_count']
            ]);
        }

        fclose($output);
        exit;
    }
}
