<?php
// Include necessary files
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/products.php';
require_once __DIR__ . '/categories.php';
require_once __DIR__ . '/brands.php';

$cartCount = getCartCount();
$user = getUserData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>EasyCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Jetbrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/cart-storage.js" defer></script>
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-wrapper">
            <div class="logo">
                <a href="index.php" style="text-decoration: none; color: inherit;">EasyCart</a>
            </div>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart<?php if ($cartCount > 0): ?><span class="cart-badge"><?php echo $cartCount; ?></span><?php endif; ?></a>
                <a href="orders.php">Orders</a>
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
                <?php if ($user['logged_in']): ?>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($user['name']); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Toast Container for notifications -->
    <div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

    <?php
    // Display flash messages as toast notifications
    if (hasFlashMessage()):
        $flash = getFlashMessage();
        $flashType = $flash['type']; // success, error, info
        $flashMessage = $flash['message'];
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('<?php echo addslashes($flashMessage); ?>', '<?php echo $flashType; ?>');
        });
    </script>
    <?php endif; ?>
