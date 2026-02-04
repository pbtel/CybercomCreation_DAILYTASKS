<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Get featured products
$featuredProducts = getFeaturedProducts();
// Get all categories with product counts
$allCategories = getAllCategories();
// Get all brands
$allBrands = getAllBrands();
?>

    <!-- HERO BANNER -->
    <section class="hero-banner">
        <div class="banner-slider">
            <div class="banner-panel banner-panel-1">
                <h1 class="banner-headline">New Collection</h1>
                <p class="banner-tagline">Discover the Latest Trends</p>
                <a href="products.php" class="banner-cta">Shop Now</a>
            </div>
            <div class="banner-panel banner-panel-2">
                <h1 class="banner-headline">50% OFF Sale</h1>
                <p class="banner-tagline">Limited Time Offer</p>
                <a href="products.php?filter=sale" class="banner-cta">Grab Deals</a>
            </div>
            <div class="banner-panel banner-panel-3">
                <h1 class="banner-headline">Free Delivery</h1>
                <p class="banner-tagline">On Orders Above ₹999</p>
                <a href="products.php" class="banner-cta">Learn More</a>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        
        <div class="products-container">
            <?php foreach ($featuredProducts as $product): ?>
            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-item">
                <div class="product-image-wrapper">
                    <?php if (!empty($product['tags'])): ?>
                        <div class="product-tag"><?php echo ucfirst($product['tags'][0]); ?></div>
                    <?php endif; ?>
                    <?php if (strpos($product['image'], 'assets/images') === 0): ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                    <?php else: ?>
                        <span><?php echo $product['image']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-details">
                    <div class="product-meta"><?php echo ucfirst($product['category']); ?></div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-stars">
                        <?php 
                        $fullStars = floor($product['rating']);
                        $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++) echo '★';
                        if ($hasHalfStar) echo '★';
                        for ($i = 0; $i < (5 - ceil($product['rating'])); $i++) echo '☆';
                        ?>
                        <span><?php echo $product['rating']; ?></span>
                    </div>
                    <div class="product-pricing">
                        <span class="product-current-price">₹<?php echo number_format($product['price']); ?></span>
                        <?php if ($product['original_price'] > $product['price']): ?>
                            <span class="product-old-price">₹<?php echo number_format($product['original_price']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CATEGORIES SECTION -->
    <div class="container mt-4rem">
        <h2 class="section-title">Shop by Category</h2>
        
        <div class="products-container">
            <?php foreach ($allCategories as $category): ?>
            <a href="products.php?category=<?php echo $category['id']; ?>" class="product-item">
                <div class="product-image-wrapper aspect-16-9">
                    <?php if (strpos($category['image'], 'assets/images') === 0): ?>
                        <img src="<?php echo $category['image']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="category-img">
                    <?php else: ?>
                        <span class="icon-lg"><?php echo $category['image']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-details">
                    <h3 class="product-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p class="product-meta"><?php echo $category['product_count']; ?> Products</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BRANDS SECTION -->
    <div class="container mt-4rem">
        <h2 class="section-title">Popular Brands</h2>
        
        <div class="products-container">
            <?php foreach ($allBrands as $brand): ?>
            <a href="products.php?brand=<?php echo strtolower($brand['id']); ?>" class="product-item">
                <div class="product-image-wrapper aspect-16-9 brand-gradient">
                    <?php if (strpos($brand['image'], 'assets/images') === 0): ?>
                        <img src="<?php echo $brand['image']; ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>" class="brand-img">
                    <?php else: ?>
                        <span class="icon-md"><?php echo $brand['image']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-details">
                    <h3 class="product-title"><?php echo htmlspecialchars($brand['name']); ?></h3>
                    <p class="text-muted-sm"><?php echo htmlspecialchars($brand['description']); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
