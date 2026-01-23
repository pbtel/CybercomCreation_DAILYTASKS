<?php
/**
 * Sync Cart - EasyCart
 * Syncs localStorage guest cart to PHP session on login
 */

require_once 'includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $guestCart = json_decode(file_get_contents('php://input'), true);
    
    if ($guestCart && is_array($guestCart)) {
        foreach ($guestCart as $item) {
            if (isset($item['productId']) && isset($item['quantity'])) {
                addToCart(
                    intval($item['productId']),
                    intval($item['quantity']),
                    $item['variants'] ?? []
                );
            }
        }
        echo json_encode(['success' => true, 'message' => 'Cart synced successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in or invalid request']);
}
?>
