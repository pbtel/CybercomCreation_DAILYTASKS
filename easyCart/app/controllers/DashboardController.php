<?php

/**
 * Dashboard Controller
 * Routes users to their appropriate dashboard (Admin or Order History)
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Require login
        $userModel = $this->model('UserModel');
        $userModel->requireLogin('dashboard');

        $user = Session::get('user', ['logged_in' => false]);

        if ($user['email'] === 'admin@easycart.com') {
            // Admin user goes to admin dashboard
            $this->redirect('admin');
            return;
        }

        // Fetch data for user dashboard
        $orderModel = $this->model('OrderModel');
        $userId = $user['user_id'];

        $stats = $orderModel->getStats($userId);
        $allOrders = $orderModel->getUserOrders($userId);
        $recentOrders = array_slice($allOrders, 0, 5); // Just first 5
        $chartData = $orderModel->getChartData();

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

        $this->view('user/dashboard', $data);
    }
}
