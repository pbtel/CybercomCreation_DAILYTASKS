<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>">Home</a> /
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
                <?php if (isset($product['image']) && (strpos($product['image'], 'assets/images') === 0 || strpos($product['image'], '/') === 0 || strpos($product['image'], 'http') === 0)): ?>
                    <img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : BASE_URL . '/' . ltrim($product['image'], '/'); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="detail-emoji"><?php echo $product['image'] ?? '📦'; ?></div>
                <?php endif; ?>
            </div>
            <div class="showcase-thumbs">
                <?php if (!empty($product['images'])): ?>
                    <?php foreach ($product['images'] as $index => $img): ?>
                        <div class="showcase-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
                            onclick="swapImage(event, this)">
                            <?php
                            $imgUrl = $img['image'] ?? $img['image_url'] ?? '';
                            ?>
                            <img src="<?php echo (strpos($imgUrl, 'http') === 0) ? $imgUrl : BASE_URL . '/' . ltrim($imgUrl, '/'); ?>"
                                alt="Thumb <?php echo $index + 1; ?>">
                        </div>
                    <?php endforeach; ?>

                    <!-- Ensure we have 4 slots if less than 4 images -->
                    <?php for ($i = count($product['images']); $i < 4; $i++): ?>
                        <div class="showcase-thumb" onclick="swapImage(event, this)">
                            <?php if (isset($product['image']) && (strpos($product['image'], 'assets/images') === 0 || strpos($product['image'], '/') === 0 || strpos($product['image'], 'http') === 0)): ?>
                                <img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : BASE_URL . '/' . ltrim($product['image'], '/'); ?>"
                                    alt="Filler Thumb">
                            <?php else: ?>
                                <div class="detail-emoji-thumb"><?php echo $product['image'] ?? '📦'; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                <?php else: ?>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="showcase-thumb <?php echo $i === 0 ? 'active' : ''; ?>" onclick="swapImage(event, this)">
                            <?php if (isset($product['image']) && strpos($product['image'], 'assets/images') === 0): ?>
                                <img src="<?php echo BASE_URL . '/' . ltrim($product['image'], '/'); ?>"
                                    alt="Thumb <?php echo $i + 1; ?>">
                            <?php else: ?>
                                <div class="detail-emoji-thumb"><?php echo $product['image'] ?? '📦'; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
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
                    for ($i = 0; $i < $fullStars; $i++)
                        echo '★';
                    if ($hasHalfStar)
                        echo '★';
                    for ($i = 0; $i < (5 - ceil($product['rating'])); $i++)
                        echo '☆';
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
                <div
                    class="shipping-info-card <?php echo $product['shipping_type'] === 'Express' ? 'shipping-info-express' : 'shipping-info-freight'; ?>">
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
            <!-- FORM START: Wraps Variants and Quantity -->
            <form action="<?php echo BASE_URL; ?>/api/cart-add" method="POST" id="addToCartForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                <!-- VARIANTS -->
                <?php if (isset($product['variants']) && !empty($product['variants'])): ?>
                    <?php foreach ($product['variants'] as $variantType => $options): ?>
                        <div class="variant-group">
                            <label class="variant-label">Choose <?php echo ucfirst($variantType); ?></label>
                            <div class="variant-choices">
                                <?php foreach ($options as $index => $option): ?>
                                    <button class="variant-choice <?php echo $index === 0 ? 'selected' : ''; ?>" type="button"
                                        data-value="<?php echo htmlspecialchars($option); ?>">
                                        <?php echo htmlspecialchars($option); ?>
                                    </button>
                                <?php endforeach; ?>
                                <input type="hidden" name="variant_<?php echo $variantType; ?>"
                                    value="<?php echo htmlspecialchars($options[0]); ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- QUANTITY -->
                <div class="form-group mt-1-5">
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
            <?php if (!empty($product['specs'])): ?>
                <?php foreach ($product['specs'] as $key => $value): ?>
                    <div class="spec-entry">
                        <div class="spec-key"><?php echo htmlspecialchars($key); ?></div>
                        <div class="spec-val"><?php echo htmlspecialchars($value); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Static Specs -->
                <div class="spec-entry">
                    <div class="spec-key">Model Year</div>
                    <div class="spec-val">2024 Edition</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Warranty</div>
                    <div class="spec-val">1 Year Brand Warranty</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Shipping Weight</div>
                    <div class="spec-val">1.2 kg</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Material</div>
                    <div class="spec-val">Premium Polymer & Alloy</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Country of Origin</div>
                    <div class="spec-val">Imported</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Battery Life</div>
                    <div class="spec-val">Up to 12 Hours</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Connectivity</div>
                    <div class="spec-val">Bluetooth 5.3, Wi-Fi 6E</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">Dimensions</div>
                    <div class="spec-val">32.5 x 22.7 x 1.8 cm</div>
                </div>
                <div class="spec-entry">
                    <div class="spec-key">In the Box</div>
                    <div class="spec-val">Device, Charger, Manual</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CUSTOMER REVIEWS (Static) -->
    <div class="reviews-wrapper">
        <h2 class="reviews-title">Customer Reviews</h2>
        <div class="reviews-grid">
            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">John Smith</div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-text">This product exceeded my expectations. Great quality and fast delivery. Highly
                    recommended!</p>
            </div>
            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">Sarah Wilson</div>
                    <div class="review-rating">★★★★☆</div>
                </div>
                <p class="review-text">Excellent value for money. The design is sleek and modern. Shipping was a bit
                    slow
                    but definitely worth the wait.</p>
            </div>
            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">Michael Brown</div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-text">Absolutely fantastic! I've been using this for a week now and it works perfectly.
                    Best in its class!</p>
            </div>
            <div class="review-box">
                <div class="review-top">
                    <div class="review-author">Emily Davis</div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-text">Perfect gift for my brother. He loves it! The packaging was secure and premium.
                    Five
                    stars!</p>
            </div>
        </div>
        <!-- Additional static reviews can remain... -->
    </div>

    <!-- RECOMMENDED PRODUCTS -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-3rem">
            <h2 class="section-title">You May Also Like</h2>
            <div class="products-container">
                <?php foreach ($relatedProducts as $recProduct): ?>
                    <a href="<?php echo BASE_URL; ?>/product/<?php echo !empty($recProduct['slug']) ? $recProduct['slug'] : $recProduct['id']; ?>"
                        class="product-item">
                        <div class="product-image-wrapper">
                            <?php if (!empty($recProduct['tags'])): ?>
                                <div class="product-tag"><?php echo ucfirst($recProduct['tags'][0]); ?></div>
                            <?php endif; ?>
                            <?php if (isset($recProduct['image']) && (strpos($recProduct['image'], 'assets/images') === 0 || strpos($recProduct['image'], '/') === 0 || strpos($recProduct['image'], 'http') === 0)): ?>
                                <img src="<?php echo (strpos($recProduct['image'], 'http') === 0) ? $recProduct['image'] : BASE_URL . '/' . ltrim($recProduct['image'], '/'); ?>"
                                    alt="<?php echo htmlspecialchars($recProduct['name']); ?>" class="product-img">
                            <?php else: ?>
                                <div class="product-emoji"><?php echo $recProduct['image'] ?? '📦'; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <div class="product-meta"><?php echo ucfirst($recProduct['category']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($recProduct['name']); ?></h3>
                            <div class="product-stars">
                                <?php
                                $fullStars = floor($recProduct['rating']);
                                $hasHalfStar = ($recProduct['rating'] - $fullStars) >= 0.5;
                                for ($i = 0; $i < $fullStars; $i++)
                                    echo '★';
                                if ($hasHalfStar)
                                    echo '★';
                                for ($i = 0; $i < (5 - ceil($recProduct['rating'])); $i++)
                                    echo '☆';
                                ?>
                                <span><?php echo $recProduct['rating']; ?></span>
                            </div>
                            <div class="product-pricing">
                                <span class="product-current-price">₹<?php echo number_format($recProduct['price']); ?></span>
                                <?php if ($recProduct['original_price'] > $recProduct['price']): ?>
                                    <span
                                        class="product-old-price">₹<?php echo number_format($recProduct['original_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($recProduct['shipping_type'])): ?>
                                <div
                                    class="badge <?php echo $recProduct['shipping_type'] === 'Express' ? 'badge-express' : 'badge-freight'; ?> mt-0-5">
                                    <?php echo $recProduct['shipping_type'] === 'Express' ? '⚡' : '🚚'; ?>
                                    <?php echo $recProduct['shipping_type']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ==========================================
        // THUMBNAIL INTERACTION
        // ==========================================
        const thumbs = document.querySelectorAll('.showcase-thumb');
        const mainImageContainer = document.querySelector('.showcase-main');

        if (thumbs.length > 0 && mainImageContainer) {
            thumbs.forEach(thumb => {
                thumb.addEventListener('click', function () {
                    // Update active state
                    thumbs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Get source
                    const img = this.querySelector('img');
                    const emoji = this.querySelector('.detail-emoji-thumb');

                    // Simple logic: Clear and rebuild content every time to ensure consistency
                    mainImageContainer.innerHTML = '';

                    if (img) {
                        const newImg = document.createElement('img');
                        newImg.src = img.src;
                        newImg.alt = img.alt || 'Product Image';
                        mainImageContainer.appendChild(newImg);
                    } else if (emoji) {
                        const newDiv = document.createElement('div');
                        newDiv.className = 'detail-emoji';
                        newDiv.textContent = emoji.textContent;
                        mainImageContainer.appendChild(newDiv);
                    }
                });
            });
        }

        // ==========================================
        // VARIANT SELECTION
        // ==========================================
        const variantButtons = document.querySelectorAll('.variant-choice');
        variantButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const group = this.closest('.variant-choices');
                if (group) {
                    const siblings = group.querySelectorAll('.variant-choice');
                    siblings.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');

                    // Update hidden input
                    const hiddenInput = group.querySelector('input[type="hidden"]');
                    if (hiddenInput && this.dataset.value) {
                        hiddenInput.value = this.dataset.value;
                    }
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>