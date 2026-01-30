<?php
/**
 * API: Update Selected Shipping Method
 * Saves the user's shipping method selection to session
 */

require_once '../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get shipping method from POST data
$shippingMethod = $_POST['shipping_method'] ?? '';

if (empty($shippingMethod)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Shipping method is required']);
    exit;
}

// Validate and set shipping method
$result = setSelectedShippingMethod($shippingMethod);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Shipping method updated successfully',
        'selected_method' => $shippingMethod
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid shipping method for current cart'
    ]);
}
?>
