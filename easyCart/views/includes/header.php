<?php
// Include necessary files - paths relative to root
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/products.php';
require_once __DIR__ . '/../../includes/categories.php';
require_once __DIR__ . '/../../includes/brands.php';

$cartCount = getCartCount();
$user = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>EasyCart
    </title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Jetbrains+Mono:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js" defer></script>
    <script src="../../assets/js/cart-ajax.js" defer></script>
</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="header-wrapper">
            <div class="logo">
                <a href="../../index.php" class="logo-link">EasyCart</a>
            </div>
            <nav class="header-nav">
                <a href="../../index.php">Home</a>
                <a href="../../products.php">Products</a>
                <a href="../../cart.php">Cart<span class="cart-badge <?php echo $cartCount > 0 ? '' : 'hidden'; ?>"
                        id="cartBadge">
                        <?php echo $cartCount > 0 ? $cartCount : '0'; ?>
                    </span></a>
                <a href="../../orders.php">Orders</a>
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-icon">&#127769;</span>
                </button>
                <?php if ($user['logged_in']): ?>
                    <a href="../../logout.php">Logout (
                        <?php echo htmlspecialchars($user['name']); ?>)
                    </a>
                <?php else: ?>
                    <a href="../../login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Toast Container for notifications -->
    <div id="toastContainer" class="toast-container"></div>

    <?php
    // Display flash messages as toast notifications
    if (hasFlashMessage()):
        $flash = getFlashMessage();
        $flashType = $flash['type']; // success, error, info
        $flashMessage = $flash['message'];
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast('<?php echo addslashes($flashMessage); ?>', '<?php echo $flashType; ?>');
            });
        </script>
    <?php endif; ?>