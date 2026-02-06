<?php
/**
 * AJAX Endpoint - Update Cart Quantity
 * Handles updating cart item quantity via AJAX
 */

// Start output buffering to catch any stray output
ob_start();

require_once '../includes/session.php';
require_once '../includes/products.php';

// Helper to send clean JSON response
function sendJson($data)
{
    if (ob_get_length())
        ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Register shutdown function to catch fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        // Clear buffer
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Fatal Error',
            'error' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
});

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Invalid request method']);
}

// Get POST data
$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate cart key
if (empty($cartKey)) {
    sendJson([
        'success' => false,
        'message' => 'Invalid cart item'
    ]);
}

// Validate quantity
if ($quantity <= 0) {
    sendJson([
        'success' => false,
        'message' => 'Quantity must be at least 1'
    ]);
}

// Get cart items to validate stock
// This returns items populated with product details (price, stock, etc.) via JOIN
$cart = getCurrentCart();

if (!isset($cart[$cartKey])) {
    sendJson([
        'success' => false,
        'message' => 'Cart item not found'
    ]);
}

$cartItem = $cart[$cartKey];

// Check stock availability using the data from cart item (which comes from fresh DB join)
if ($quantity > $cartItem['product']['stock']) {
    sendJson([
        'success' => false,
        'message' => 'Requested quantity exceeds available stock (' . $cartItem['product']['stock'] . ' available)'
    ]);
}

// Update cart quantity
try {
    // Log the attempt
    error_log("Cart Update Attempt - Key: $cartKey, Quantity: $quantity");

    $updated = updateCartQuantity($cartKey, $quantity);

    error_log("updateCartQuantity returned: " . ($updated ? 'true' : 'false'));

    if (!$updated) {
        error_log("Cart Update Failed - updateCartQuantity returned false");
        sendJson([
            'success' => false,
            'message' => 'Cart item not found or could not be updated',
            'debug' => 'updateCartQuantity returned false'
        ]);
    }

    // Get updated cart data
    $cartItems = getCartItemsWithDetails();
    $cartCount = getCartCount();
    $cartSubtotal = getCartSubtotal();

    // Calculate item subtotal with first-unit discount
    // Uses price from cart item which is reliable
    $discountInfo = calculateItemTotalWithDiscount($cartItem['product']['price'], $quantity);
    $itemSubtotal = $discountInfo['total'];

    error_log("Cart Update Success - Count: $cartCount, Subtotal: $cartSubtotal");

    $responseData = [
        'success' => true,
        'message' => 'Cart updated successfully!',
        'cart_count' => $cartCount,
        'cart_subtotal' => $cartSubtotal,
        'item_subtotal' => $itemSubtotal,
        'quantity' => $quantity
    ];

    $jsonOutput = json_encode($responseData);

    if ($jsonOutput === false) {
        error_log("JSON Encode Failed: " . json_last_error_msg());
        // Fallback for infinity/nan
        $responseData['cart_subtotal'] = 0;
        $responseData['item_subtotal'] = 0;
        $jsonOutput = json_encode($responseData);
        if ($jsonOutput === false) {
            throw new Exception("JSON Encoding Failed: " . json_last_error_msg());
        }
    }

    // Clean output buffer one last time before sending JSON
    if (ob_get_length())
        ob_clean();
    header('Content-Type: application/json');
    echo $jsonOutput;
} catch (Exception $e) {
    // Log the full error
    $errorMsg = $e->getMessage();
    $errorTrace = $e->getTraceAsString();
    error_log("Cart Update Exception: $errorMsg");
    error_log("Stack Trace: $errorTrace");

    sendJson([
        'success' => false,
        'message' => 'Error: ' . $errorMsg, // Show actual error
        'error' => $errorMsg,
        'trace' => explode("\n", $errorTrace)
    ]);
}