<?php
$pageTitle = "Home";
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO BANNER -->
<section class="hero-banner">
    <div class="banner-slider">
        <div class="banner-panel banner-panel-1">
            <h1 class="banner-headline">New Collection</h1>
            <p class="banner-tagline">Discover the Latest Trends</p>
            <a href="<?= BASE_URL ?>/products" class="banner-cta">Shop Now</a>
        </div>
        <div class="banner-panel banner-panel-2">
            <h1 class="banner-headline">50% OFF Sale</h1>
            <p class="banner-tagline">Limited Time Offer</p>
            <a href="<?= BASE_URL ?>/products?filter=sale" class="banner-cta">Grab Deals</a>
        </div>
        <div class="banner-panel banner-panel-3">
            <h1 class="banner-headline">Free Delivery</h1>
            <p class="banner-tagline">On Orders Above &#8377;999</p>
            <a href="<?= BASE_URL ?>/products" class="banner-cta">Learn More</a>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<div class="container">
    <h2 class="home-section-title">Featured Products</h2>

    <div class="products-container">
        <?php foreach ($featuredProducts as $product): ?>
            <a href="<?= BASE_URL ?>/product/<?php echo $product['id']; ?>" class="product-item">
                <div class="product-image-wrapper">
                    <?php if (!empty($product['tags'])): ?>
                        <div class="product-tag">
                            <?php echo ucfirst($product['tags'][0]); ?>
                        </div>
                    <?php endif; ?>
                    <span>
                        <?php echo $product['image']; ?>
                    </span>
                </div>
                <div class="product-details">
                    <div class="product-meta">
                        <?php echo ucfirst($product['category']); ?>
                    </div>
                    <h3 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>
                    <div class="product-stars">
                        <?php
                        $fullStars = floor($product['rating']);
                        $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++)
                            echo '&#9733;';
                        if ($hasHalfStar)
                            echo '&#9733;';
                        for ($i = 0; $i < (5 - ceil($product['rating'])); $i++)
                            echo '&#9734;';
                        ?>
                        <span>
                            <?php echo $product['rating']; ?>
                        </span>
                    </div>
                    <div class="product-pricing">
                        <span class="product-current-price"><?php echo formatPrice($product['price']); ?>
                        </span>
                        <?php if ($product['original_price'] > $product['price']): ?>
                            <span class="product-old-price"><?php echo formatPrice($product['original_price']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- CATEGORIES SECTION -->
<div class="container home-section">
    <h2 class="home-section-title">Shop by Category</h2>

    <div class="products-container">
        <?php foreach ($allCategories as $category): ?>
            <a href="<?= BASE_URL ?>/products?category=<?php echo $category['id']; ?>" class="product-item">
                <div class="product-image-wrapper category-card-image">
                    <span class="category-icon">
                        <?php echo $category['icon']; ?>
                    </span>
                </div>
                <div class="product-details">
                    <h3 class="product-title">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </h3>
                    <p class="product-meta">
                        <?php echo $category['product_count']; ?> Products
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- BRANDS SECTION -->
<div class="container home-section">
    <h2 class="home-section-title">Popular Brands</h2>

    <div class="products-container">
        <?php foreach ($allBrands as $brand): ?>
            <a href="<?= BASE_URL ?>/products?brand=<?php echo strtolower($brand['id']); ?>" class="product-item">
                <div class="product-image-wrapper brand-card-image">
                    <span class="brand-logo">
                        <?php echo $brand['logo']; ?>
                    </span>
                </div>
                <div class="product-details">
                    <h3 class="product-title">
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </h3>
                    <p class="brand-desc">
                        <?php echo htmlspecialchars($brand['description']); ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>