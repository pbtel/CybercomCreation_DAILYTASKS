<?php

/**
 * Order Controller
 * Handles order history and details
 */
class Controller_Order extends Controller
{

    /**
     * Display order history
     */
    public function index()
    {
        // Require login
        $userModel = $this->model('Model_User');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('Model_Order');

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

        // Use View_Order_List class
        require_once __DIR__ . '/../views/View_Orders.php';
        $view = new View_Order_List($data);
        echo $view->toHtml();
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
        $userModel = $this->model('Model_User');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('Model_Order');

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

        // Use View_Order_Detail class
        require_once __DIR__ . '/../views/View_Orders.php';
        $view = new View_Order_Detail($data);
        echo $view->toHtml();
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
        $userModel = $this->model('Model_User');
        $userModel->requireLogin('orders');

        // Load model
        $orderModel = $this->model('Model_Order');

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

        // Use View_Order_Invoice class
        require_once __DIR__ . '/../views/View_Order_Invoice.php';
        $view = new View_Order_Invoice($data);
        echo $view->toHtml();
    }

    /**
     * Display order success page
     * URL: /order/success/{id}
     */
    public function success($id = null)
    {
        if (!$id) {
            $this->redirect('orders');
            return;
        }

        $orderModel = $this->model('Model_Order');
        $order = $orderModel->getById($id);

        if (!$order) {
            $this->redirect('orders');
            return;
        }

        $data = [
            'pageTitle' => 'Order Successful!',
            'order' => $order
        ];

        require_once __DIR__ . '/../views/View_Orders.php';
        $view = new View_Order_Detail($data);
        echo $view->toHtml();
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
        if (file_exists(__DIR__ . '/../../order-place.php')) {
            require_once __DIR__ . '/../../order-place.php';
        } else {
            $this->redirect('checkout');
        }
    }
}
