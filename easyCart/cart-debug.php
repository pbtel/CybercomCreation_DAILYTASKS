<?php
/**
 * Cart Debug Page - Helps diagnose cart issues
 */
$pageTitle = "Cart Debug";
require_once 'includes/header.php';

// Get cart data
$cart = getCurrentCart();
$cartItems = getCartItemsWithDetails();
$cartCount = getCartCount();
$isLoggedIn = isLoggedIn();
$userData = getUserData();
?>

<div class="container" style="padding: 2rem;">
    <h1 style="font-size: 2rem; margin-bottom: 2rem;">🔍 Cart Debug Information</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">User Session Status</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">Logged In:</td>
                <td style="padding: 0.75rem;"><?php echo $isLoggedIn ? '✅ Yes' : '❌ No'; ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">Session Type:</td>
                <td style="padding: 0.75rem;"><?php echo $_SESSION['session_type']; ?></td>
            </tr>
            <?php if ($isLoggedIn): ?>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">User ID:</td>
                <td style="padding: 0.75rem;"><?php echo $userData['user_id']; ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">User Name:</td>
                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($userData['name']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Cart Status</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">Cart Count:</td>
                <td style="padding: 0.75rem;"><?php echo $cartCount; ?> items</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">Cart Empty:</td>
                <td style="padding: 0.75rem;"><?php echo empty($cart) ? '❌ Yes (PROBLEM!)' : '✅ No'; ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.75rem; font-weight: 600;">Raw Cart Keys:</td>
                <td style="padding: 0.75rem;"><?php echo empty($cart) ? 'None' : count($cart); ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($cart)): ?>
    <div style="background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Raw Cart Data</h2>
        <pre style="background: #f3f4f6; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.875rem;"><?php echo htmlspecialchars(print_r($cart, true)); ?></pre>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Cart Items with Product Details</h2>
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $key => $item): ?>
            <div style="border: 1px solid #e5e7eb; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <h3 style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                <p><strong>Product ID:</strong> <?php echo $item['product']['id']; ?></p>
                <p><strong>Quantity:</strong> <?php echo $item['quantity']; ?></p>
                <p><strong>Price:</strong> ₹<?php echo number_format($item['product']['price']); ?></p>
                <p><strong>Subtotal:</strong> ₹<?php echo number_format($item['subtotal']); ?></p>
                <?php if (!empty($item['variant'])): ?>
                <p><strong>Variants:</strong> <?php echo htmlspecialchars(json_encode($item['variant'])); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #ef4444; font-weight: 600;">⚠️ No cart items with details found!</p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="background: #fef2f2; border: 2px solid #ef4444; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #ef4444;">❌ Cart is Empty!</h2>
        <p style="margin-bottom: 1rem;">Your cart is currently empty. This could be because:</p>
        <ul style="margin-left: 1.5rem; margin-bottom: 1rem;">
            <li>You haven't added any items yet</li>
            <li>Items were cleared from the cart</li>
            <li>Session data was lost</li>
        </ul>
        <a href="products.php" style="display: inline-block; padding: 0.75rem 1.5rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
            Go to Products Page
        </a>
    </div>
    <?php endif; ?>

    <div style="background: white; padding: 2rem; border-radius: 12px;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Session Data</h2>
        <pre style="background: #f3f4f6; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.875rem; max-height: 400px;"><?php 
        $sessionCopy = $_SESSION;
        // Remove sensitive data
        if (isset($sessionCopy['users_db'])) {
            $sessionCopy['users_db'] = '[' . count($sessionCopy['users_db']) . ' users]';
        }
        if (isset($sessionCopy['carts_db'])) {
            $sessionCopy['carts_db'] = '[' . count($sessionCopy['carts_db']) . ' user carts]';
        }
        echo htmlspecialchars(print_r($sessionCopy, true)); 
        ?></pre>
    </div>

    <div style="margin-top: 2rem; padding: 1rem; background: #eff6ff; border-radius: 8px;">
        <p style="font-size: 0.875rem; color: #1e40af;">
            💡 <strong>Tip:</strong> This page shows the current state of your cart and session. Use it to diagnose why checkout might not be working.
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
