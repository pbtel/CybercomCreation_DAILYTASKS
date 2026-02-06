<?php
/**
 * Chart Data AJAX Endpoint
 * Fetches order amounts grouped by date for visualization
 */

require_once '../includes/session.php';
require_once '../database/orders.php';

// Set JSON header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION['user']['user_id'];

    // Get chart data
    $chartData = getUserOrderChartDataDB($userId);

    // Format for Chart.js (labels: dates, data: amounts)
    $labels = [];
    $amounts = [];

    foreach ($chartData as $row) {
        $labels[] = date('M d', strtotime($row['date'])); // Format: Jan 01
        $amounts[] = (float) $row['total'];
    }

    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'data' => $amounts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
