<?php require_once '../app/views/layouts/header.php'; ?>

    <div class="container">
        <!-- FILTER PANEL -->
        <div class="filter-panel">
            <div class="filter-top">
                <h2 class="filter-heading">Filter Products</h2>
                <div class="view-controls">
                    <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=grid" 
                       class="view-control-btn <?php echo $viewMode === 'grid' ? 'active' : ''; ?>">◼</a>
                    <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=list" 
                       class="view-control-btn <?php echo $viewMode === 'list' ? 'active' : ''; ?>">☰</a>
                </div>
            </div>

            <div class="filter-content">
                <!-- Category Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Category</div>
                    <div class="filter-chips">
                        <a href="<?php echo BASE_URL; ?>/products?category=all&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedCategory === 'all' ? 'active' : ''; ?>">All</a>
                        <?php foreach ($allCategories as $category): ?>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $category['id']; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedCategory === $category['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Brand</div>
                    <div class="filter-chips">
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=all&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedBrand === 'all' ? 'active' : ''; ?>">All</a>
                        <?php foreach ($allBrands as $brand): ?>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo strtolower($brand['id']); ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedBrand === strtolower($brand['id']) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($brand['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Price Range</div>
                    <div class="filter-chips">
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=all&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'all' ? 'active' : ''; ?>">All</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=under5k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'under5k' ? 'active' : ''; ?>">Under ₹5K</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=5k-20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === '5k-20k' ? 'active' : ''; ?>">₹5K - ₹20K</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=above20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'above20k' ? 'active' : ''; ?>">Above ₹20K</a>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Rating</div>
                    <div class="filter-chips">
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=0&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 0 ? 'active' : ''; ?>">All</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4.5&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 4.5 ? 'active' : ''; ?>">★★★★★</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 4 ? 'active' : ''; ?>">★★★★☆</a>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=3&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 3 ? 'active' : ''; ?>">★★★☆☆</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUCTS GRID/LIST -->
        <div class="products-container <?php echo $viewMode === 'list' ? 'list-layout' : ''; ?>">
            <?php if (empty($displayProducts)): ?>
                <div class="empty-state grid-col-all">
                    <h3 class="auth-subtitle mb-1">No products found matching your filters</h3>
                    <a href="<?php echo BASE_URL; ?>/products" class="btn-primary">Clear Filters</a>
                </div>
            <?php else: ?>
                <?php foreach ($displayProducts as $product): ?>
                <a href="<?php echo BASE_URL; ?>/product/<?php echo $product['id']; ?>" class="product-item">
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['tags'])): ?>
                            <div class="product-tag"><?php echo ucfirst($product['tags'][0]); ?></div>
                        <?php endif; ?>
                        <?php if (strpos($product['image'], 'assets/images') === 0): ?>
                            <img src="<?php echo BASE_URL . '/' . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
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
                        <?php if (isset($product['shipping_type'])): ?>
                        <div class="badge <?php echo $product['shipping_type'] === 'Express' ? 'badge-express' : 'badge-freight'; ?> mt-0-5">
                            <?php echo $product['shipping_type'] === 'Express' ? '⚡' : '🚚'; ?> <?php echo $product['shipping_type']; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-center mt-3rem">
            Showing <?php echo count($displayProducts); ?> of <?php echo count($products); ?> products
        </div>
    </div>

<?php require_once '../app/views/layouts/footer.php'; ?>
