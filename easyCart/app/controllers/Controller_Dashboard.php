<?php

/**
 * Dashboard Controller
 * Routes users to their appropriate dashboard (Admin or Order History)
 */
class Controller_Dashboard extends Controller
{
    public function index()
    {
        // Require login
        $userModel = $this->model('Model_User');
        $userModel->requireLogin('dashboard');

        $user = $userModel->getCurrentUser();

        if ($user['email'] === 'admin@easycart.com') {
            // Admin user goes to admin dashboard
            $this->redirect('admin');
            return;
        }

        // Fetch data for user dashboard
        $orderModel = $this->model('Model_Order');
        $userId = $user['user_id'];

        $stats = $orderModel->getStats($userId);
        $allOrders = $orderModel->getUserOrders($userId);
        $recentOrders = array_slice($allOrders, 0, 5); // Just first 5
        $chartData = $orderModel->getChartData($userId);

        // Calculate total spent
        $totalSpent = 0;
        foreach ($allOrders as $order) {
            if ($order['status'] !== 'cancelled') {
                $totalSpent += $order['final_amount'];
            }
        }

        $data = [
            'pageTitle' => 'My Dashboard',
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'totalSpent' => $totalSpent,
            'chartData' => $chartData
        ];

        // Use View_User_Dashboard class
        require_once __DIR__ . '/../views/View_Dashboard.php';
        $view = new View_User_Dashboard($data);
        echo $view->toHtml();
    }
}
