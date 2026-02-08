<?php

/**
 * Order Controller
 * Handles order history and details
 */
class OrderController extends Controller
{

    /**
     * Display order history
     */
    public function index()
    {
        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('OrderModel');

        // Get current user
        $user = $userModel->getCurrentUser();

        // Get user orders (all)
        $allOrders = $orderModel->getUserOrders($user['user_id']);

        // Pagination
        $itemsPerPage = 10;
        $currentPage = $this->get('page', 1);
        $totalItems = count($allOrders);
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $pagedOrders = array_slice($allOrders, $pagination->getOffset(), $pagination->getLimit());

        // Get order statistics
        $stats = $orderModel->getStats($user['user_id']);

        // Pass data to view
        $data = [
            'pageTitle' => 'Order History',
            'orders' => $pagedOrders,
            'pagination' => $pagination,
            'stats' => $stats
        ];

        $this->view('orders/index', $data);
    }

    /**
     * Display order detail
     * URL: /order/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->redirect('orders');
            return;
        }

        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('OrderModel');

        // Get order
        $order = $orderModel->getById($id);

        if (!$order) {
            Session::setFlash('error', 'Order not found');
            $this->redirect('orders');
            return;
        }

        // Verify order belongs to current user
        $user = $userModel->getCurrentUser();
        if ($order['user_id'] != $user['user_id']) {
            Session::setFlash('error', 'Access denied');
            $this->redirect('orders');
            return;
        }

        // Pass data to view
        $data = [
            'pageTitle' => 'Order #' . $order['order_number'],
            'order' => $order
        ];

        $this->view('orders/detail', $data);
    }

    /**
     * Display order invoice
     * URL: /order/invoice/{id}
     */
    public function invoice($id = null)
    {
        if (!$id) {
            $this->redirect('orders');
            return;
        }

        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('OrderModel');

        // Get order
        $order = $orderModel->getById($id);

        if (!$order) {
            Session::setFlash('error', 'Order not found');
            $this->redirect('orders');
            return;
        }

        // Verify order belongs to current user
        $user = $userModel->getCurrentUser();
        if ($order['user_id'] != $user['user_id']) {
            Session::setFlash('error', 'Access denied');
            $this->redirect('orders');
            return;
        }

        // Pass data to view
        $data = [
            'pageTitle' => 'Invoice ' . $order['order_number'],
            'order' => $order,
            'user' => $user
        ];

        // Using special view without standard layout for printing
        $this->view('orders/invoice', $data);
    }

    /**
     * Process order placement
     * URL: /order/place
     */
    public function place()
    {
        if (!$this->isPost()) {
            $this->redirect('cart');
            return;
        }

        // This handles the legacy logic while maintaining clean MVC URL
        require_once __DIR__ . '/../../order-place.php';
    }
}
