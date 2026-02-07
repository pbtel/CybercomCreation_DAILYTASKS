<?php
// Include necessary files - paths relative to root
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/products.php';
require_once __DIR__ . '/../../includes/categories.php';
require_once __DIR__ . '/../../includes/brands.php';

// Safe Cart Count Initialization
$cartCount = function_exists('getCartCount') ? getCartCount() : 0;

// Safe User Data Initialization
$user = function_exists('getUserData') ? getUserData() : null;
if (!$user || !is_array($user)) {
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : [];
}
// Ensure specific keys exist to prevent 'Undefined array key' warnings
if (!isset($user['logged_in']))
    $user['logged_in'] = false;
if (!isset($user['name']))
    $user['name'] = 'Guest';
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/assets/js/script.js?v=<?= time() ?>" defer></script>
    <script src="<?= BASE_URL ?>/assets/js/cart-ajax.js?v=<?= time() ?>" defer></script>
</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="header-wrapper">
            <div class="logo">
                <a href="<?= BASE_URL ?>/" class="logo-link">EasyCart</a>
            </div>
            <nav class="header-nav">
                <a href="<?= BASE_URL ?>/">Home</a>
                <a href="<?= BASE_URL ?>/products">Products</a>
                <a href="<?= BASE_URL ?>/cart">Cart<span
                        class="cart-badge <?php echo $cartCount > 0 ? '' : 'hidden'; ?>" id="cartBadge">
                        <?php echo $cartCount > 0 ? $cartCount : '0'; ?>
                    </span></a>
                <a href="<?= BASE_URL ?>/orders">Orders</a>
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-icon">&#127769;</span>
                </button>
                <?php if ($user['logged_in']): ?>
                    <a href="<?= BASE_URL ?>/logout">Logout (
                        <?php echo htmlspecialchars($user['name']); ?>)
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login">Login</a>
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