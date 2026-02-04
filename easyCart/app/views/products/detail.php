<?php require_once '../app/views/layouts/header.php'; ?>

    <div class="container mt-2">
        <!-- BREADCRUMB -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/home">Home</a> / 
            <a href="<?php echo BASE_URL; ?>/products">Products</a> / 
            <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $product['category']; ?>">
                <?php echo ucfirst($product['category']); ?>
            </a> / 
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="detail-layout">
            <!-- IMAGE SHOWCASE -->
            <div class="image-showcase">
                <div class="showcase-main">
                    <?php if (strpos($product['image'], 'assets/images') === 0): ?>
                        <img src="<?php echo BASE_URL . '/' . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <?php echo $product['image']; ?>
                    <?php endif; ?>
                </div>
                <div class="showcase-thumbs">
                    <?php 
                    $productImages = $product['images'] ?? []; 
                    ?>
                    <?php if (!empty($productImages)): ?>
                        <?php foreach ($productImages as $index => $img): ?>
                            <div class="showcase-thumb <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo BASE_URL . '/' . $img['image_url']; ?>" alt="Thumb <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Ensure we have 4 slots if less than 4 images -->
                        <?php for ($i = count($productImages); $i < 4; $i++): ?>
                            <div class="showcase-thumb">📷</div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="showcase-thumb active">
                            <?php if (strpos($product['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/' . $product['image']; ?>" alt="Thumb">
                            <?php else: ?>
                                <?php echo $product['image']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="showcase-thumb">📷</div>
                        <div class="showcase-thumb">🔍</div>
                        <div class="showcase-thumb">📐</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- INFO PANEL -->
            <div class="info-panel">
                <div class="info-breadcrumb">
                    <?php echo ucfirst($product['category']); ?> / <?php echo htmlspecialchars($product['brand']); ?>
                </div>
                <h1 class="info-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <!-- RATING -->
                <div class="mb-1-5">
                    <div class="star-rating">
                        <?php 
                        $fullStars = floor($product['rating']);
                        $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++) echo '★';
                        if ($hasHalfStar) echo '★';
                        for ($i = 0; $i < (5 - ceil($product['rating'])); $i++) echo '☆';
                        ?>
                        <span class="review-count">
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
                <div class="mb-2rem">
                    <p class="color-text-secondary lh-1-6">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                </div>

                <!-- STOCK STATUS -->
                <div class="mb-2rem <?php echo $product['stock'] > 0 ? 'badge-instock' : 'badge-outofstock'; ?>">
                    <strong>
                        <?php echo $product['stock'] > 0 ? '✓ In Stock (' . $product['stock'] . ' available)' : '✗ Out of Stock'; ?>
                    </strong>
                </div>

                <!-- SHIPPING TYPE -->
                <?php if (isset($product['shipping_type'])): ?>
                <div class="shipping-info-card <?php echo $product['shipping_type'] === 'Express' ? 'shipping-info-express' : 'shipping-info-freight'; ?>">
                    <div class="flex-center-gap-0-5 flex-start-gap-0-75">
                        <span class="fs-1-5"><?php echo $product['shipping_type'] === 'Express' ? '⚡' : '🚚'; ?></span>
                        <div>
                            <strong class="shipping-info-title">
                                <?php echo $product['shipping_type']; ?> Shipping
                            </strong>
                            <p class="text-muted-sm m-0 mt-0-25">
                                <?php echo $product['shipping_type'] === 'Express' ? 'Fast delivery for lightweight items' : 'Specialized handling for high-value items'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- VARIANTS -->
                <?php if (isset($product['variants']) && !empty($product['variants'])): ?>
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
                <form action="<?php echo BASE_URL; ?>/cart/add" method="POST" id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="redirect" value="product/<?php echo $product['id']; ?>">
                    
                    <div class="form-input-group">
                        <label class="variant-label">Quantity</label>
                        <div class="quantity-wrapper">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                                   class="form-input w-100px ta-center font-600">
                            <span class="text-muted-sm">Max: <?php echo $product['stock']; ?></span>
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

        <!-- CUSTOMER REVIEWS (Static) -->
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
        </div>

        <!-- RECOMMENDED PRODUCTS -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-3rem">
            <h2 class="section-title">You May Also Like</h2>
            <div class="products-container">
                <?php foreach ($relatedProducts as $recProduct): ?>
                    <a href="<?php echo BASE_URL; ?>/product/<?php echo $recProduct['id']; ?>" class="product-item">
                        <div class="product-image-wrapper">
                            <?php if (strpos($recProduct['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/' . $recProduct['image']; ?>" alt="<?php echo htmlspecialchars($recProduct['name']); ?>" class="product-img">
                            <?php else: ?>
                                <span><?php echo $recProduct['image']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <div class="product-meta"><?php echo ucfirst($recProduct['category']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($recProduct['name']); ?></h3>
                            <div class="product-pricing">
                                <span class="product-current-price">₹<?php echo number_format($recProduct['price']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once '../app/views/layouts/footer.php'; ?>
