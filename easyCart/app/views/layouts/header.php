<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>EasyCart</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Jetbrains+Mono:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/cart-ajax.js?v=<?php echo time(); ?>"></script>
</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="header-wrapper">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>/home" class="logo-link">EasyCart</a>
            </div>
            <div class="header-search">
                <form action="<?php echo BASE_URL; ?>/products" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Search for products..."
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
            <nav class="header-nav">
                <a href="<?php echo BASE_URL; ?>/home">Home</a>
                <a href="<?php echo BASE_URL; ?>/products">Products</a>
                <a href="<?php echo BASE_URL; ?>/cart">Cart<span
                        class="cart-badge <?php echo Session::getCartCount() > 0 ? '' : 'hidden'; ?>"
                        id="cartBadge"><?php echo Session::getCartCount(); ?></span></a>
                <a href="<?php echo BASE_URL; ?>/orders">Orders</a>
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
                <?php $user = Session::get('user', ['logged_in' => false]); ?>
                <?php if ($user['logged_in']): ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard">Dashboard</a>
                    <?php if ($user['email'] === 'admin@easycart.com'): ?>
                        <a href="<?php echo BASE_URL; ?>/admin" style="color: var(--primary); font-weight: 700;">Admin</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Toast Container for notifications -->
    <div id="toastContainer" class="toast-container"></div>

    <?php if (Session::hasFlash()): ?>
        <?php $flash = Session::getFlash(); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof showToast === 'function') {
                    showToast('<?php echo addslashes($flash['message']); ?>', '<?php echo $flash['type']; ?>');
                }
            });
        </script>
    <?php endif; ?>