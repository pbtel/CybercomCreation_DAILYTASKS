<?php
require_once 'includes/header.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($productId);

// If product not found, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'];
$category = getCategoryById($product['category']);
?>

    <div class="container" style="margin-top: 2rem;">
        <!-- BREADCRUMB -->
        <div style="margin-bottom: 2rem; font-size: 0.875rem; color: var(--text-secondary);">
            <a href="index.php" style="color: var(--primary);">Home</a> / 
            <a href="products.php" style="color: var(--primary);">Products</a> / 
            <a href="products.php?category=<?php echo $product['category']; ?>" style="color: var(--primary);">
                <?php echo htmlspecialchars($category['name']); ?>
            </a> / 
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="detail-layout">
            <!-- IMAGE SHOWCASE -->
            <div class="image-showcase">
                <div class="showcase-main"><?php echo $product['image']; ?></div>
                <div class="showcase-thumbs">
                    <div class="showcase-thumb"><?php echo $product['image']; ?></div>
                    <div class="showcase-thumb">📷</div>
                    <div class="showcase-thumb">🔍</div>
                    <div class="showcase-thumb">📐</div>
                </div>
            </div>

            <!-- INFO PANEL -->
            <div class="info-panel">
                <div class="info-breadcrumb">
                    <?php echo htmlspecialchars($category['name']); ?> / <?php echo htmlspecialchars($product['brand']); ?>
                </div>
                <h1 class="info-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <!-- RATING -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: inline-block; color: #fbbf24; font-size: 1.125rem;">
                        <?php 
                        $fullStars = floor($product['rating']);
                        $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++) echo '★';
                        if ($hasHalfStar) echo '★';
                        for ($i = 0; $i < (5 - ceil($product['rating'])); $i++) echo '☆';
                        ?>
                        <span style="color: var(--text-secondary); font-size: 0.9375rem; margin-left: 0.5rem;">
                            <?php echo $product['rating']; ?> (<?php echo $product['reviews_count']; ?> reviews)
                        </span>
                    </div>
                </div>

                <!-- PRICE CARD -->
                <div class="info-price-card">
                    <div class="info-price-current">₹<?php echo number_format($product['price']); ?></div>
                    <?php if ($product['original_price'] > $product['price']): ?>
                        <div class="info-price-old">₹<?php echo number_format($product['original_price']); ?></div>
                        <div class="info-price-save">
                            Save ₹<?php echo number_format($product['original_price'] - $product['price']); ?> 
                            (<?php echo $product['discount_percent']; ?>% OFF)
                        </div>
                    <?php endif; ?>
                </div>

                <!-- DESCRIPTION -->
                <div style="margin-bottom: 2rem;">
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                </div>

                <!-- STOCK STATUS -->
                <div style="margin-bottom: 2rem; padding: 1rem; background: <?php echo $product['stock'] > 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; border-radius: 8px;">
                    <strong style="color: <?php echo $product['stock'] > 0 ? 'var(--accent)' : '#ef4444'; ?>;">
                        <?php echo $product['stock'] > 0 ? '✓ In Stock (' . $product['stock'] . ' available)' : '✗ Out of Stock'; ?>
                    </strong>
                </div>

                <!-- SHIPPING TYPE -->
                <?php if (isset($product['shipping_type'])): ?>
                <div style="margin-bottom: 2rem; padding: 1rem; background: <?php echo $product['shipping_type'] === 'Express' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(168, 85, 247, 0.1)'; ?>; border-radius: 8px; border: 2px solid <?php echo $product['shipping_type'] === 'Express' ? '#3b82f6' : '#a855f7'; ?>;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.5rem;"><?php echo $product['shipping_type'] === 'Express' ? '⚡' : '🚚'; ?></span>
                        <div>
                            <strong style="color: <?php echo $product['shipping_type'] === 'Express' ? '#3b82f6' : '#a855f7'; ?>; font-size: 1.125rem;">
                                <?php echo $product['shipping_type']; ?> Shipping
                            </strong>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: var(--text-secondary);">
                                <?php echo $product['shipping_type'] === 'Express' ? 'Fast delivery for lightweight items' : 'Specialized handling for high-value items'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- VARIANTS -->
                <?php if (isset($product['variants'])): ?>
                    <?php foreach ($product['variants'] as $variantType => $options): ?>
                        <div class="variant-group">
                            <label class="variant-label">Choose <?php echo ucfirst($variantType); ?></label>
                            <div class="variant-choices">
                                <?php foreach ($options as $index => $option): ?>
                                    <button class="variant-choice <?php echo $index === 0 ? 'selected' : ''; ?>" type="button">
                                        <?php echo htmlspecialchars($option); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- QUANTITY & ADD TO CART -->
                <form action="cart-add.php" method="POST" id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <!-- Variant inputs will be added dynamically by JavaScript -->
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label class="variant-label">Quantity</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                                   style="width: 100px; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px; font-weight: 600; text-align: center;">
                            <span style="color: var(--text-secondary); font-size: 0.875rem;">Max: <?php echo $product['stock']; ?></span>
                        </div>
                    </div>

                    <button type="submit" class="action-button" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- SPECIFICATIONS -->
        <div class="specs-wrapper">
            <h2 class="specs-title">Technical Specifications</h2>
            <div class="specs-list">
                <?php foreach ($product['specs'] as $key => $value): ?>
                <div class="spec-entry">
                    <div class="spec-key"><?php echo htmlspecialchars($key); ?></div>
                    <div class="spec-val"><?php echo htmlspecialchars($value); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CUSTOMER REVIEWS (Static for now) -->
        <div class="reviews-wrapper">
            <h2 class="reviews-title">Customer Reviews</h2>

            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">John Smith</div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-text">This product exceeded my expectations. Great quality and fast delivery. Highly recommended!</p>
            </div>

            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">Sarah Johnson</div>
                    <div class="review-rating">★★★★☆</div>
                </div>
                <p class="review-text">Really happy with this purchase. Good value for money and works perfectly.</p>
            </div>

            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">Mike Chen</div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-text">Best purchase I've made this year. The quality is outstanding and it arrived quickly.</p>
            </div>
        </div>

        <!-- RECOMMENDED PRODUCTS -->
        <?php
        // Get 4 random products from the same category
        $recommendedProducts = array_slice(getProductsByCategory($product['category']), 0, 4);
        if (!empty($recommendedProducts)):
        ?>
        <div style="margin-top: 4rem;">
            <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">You May Also Like</h2>
            <div class="products-container">
                <?php foreach ($recommendedProducts as $recProduct): ?>
                    <?php if ($recProduct['id'] !== $product['id']): ?>
                    <a href="product-detail.php?id=<?php echo $recProduct['id']; ?>" class="product-item">
                        <div class="product-image-wrapper">
                            <span><?php echo $recProduct['image']; ?></span>
                        </div>
                        <div class="product-details">
                            <div class="product-meta"><?php echo ucfirst($recProduct['category']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($recProduct['name']); ?></h3>
                            <div class="product-pricing">
                                <span class="product-current-price">₹<?php echo number_format($recProduct['price']); ?></span>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once 'includes/footer.php'; ?>
