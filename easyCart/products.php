<?php
$pageTitle = "Products";
require_once 'includes/header.php';

// Get filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$selectedBrand = isset($_GET['brand']) ? $_GET['brand'] : 'all';
$selectedPriceRange = isset($_GET['price']) ? $_GET['price'] : 'all';
$selectedRating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid';

// Get products based on filters
$displayProducts = $products;

// Apply category filter
if ($selectedCategory !== 'all') {
    $displayProducts = getProductsByCategory($selectedCategory);
}

// Apply brand filter
if ($selectedBrand !== 'all') {
    $displayProducts = array_filter($displayProducts, function($product) use ($selectedBrand) {
        return strtolower($product['brand']) === strtolower($selectedBrand);
    });
}

// Apply price filter
if ($selectedPriceRange !== 'all') {
    switch ($selectedPriceRange) {
        case 'under5k':
            $displayProducts = array_filter($displayProducts, function($p) { return $p['price'] < 5000; });
            break;
        case '5k-20k':
            $displayProducts = array_filter($displayProducts, function($p) { return $p['price'] >= 5000 && $p['price'] <= 20000; });
            break;
        case 'above20k':
            $displayProducts = array_filter($displayProducts, function($p) { return $p['price'] > 20000; });
            break;
    }
}

// Apply rating filter
if ($selectedRating > 0) {
    $displayProducts = array_filter($displayProducts, function($p) use ($selectedRating) { 
        return $p['rating'] >= $selectedRating; 
    });
}

// Get all categories for filter
$allCategories = getAllCategories();
// Get all brands for filter
$allBrands = getAllBrands();
?>

    <div class="container">
        <!-- FILTER PANEL -->
        <div class="filter-panel">
            <div class="filter-top">
                <h2 class="filter-heading">Filter Products</h2>
                <div class="view-controls">
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=grid" 
                       class="view-control-btn <?php echo $viewMode === 'grid' ? 'active' : ''; ?>">◼</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=list" 
                       class="view-control-btn <?php echo $viewMode === 'list' ? 'active' : ''; ?>">☰</a>
                </div>
            </div>

            <div class="filter-content">
                <!-- Category Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Category</div>
                    <div class="filter-chips">
                        <a href="?category=all&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedCategory === 'all' ? 'active' : ''; ?>">All</a>
                        <?php foreach ($allCategories as $category): ?>
                        <a href="?category=<?php echo $category['id']; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
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
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=all&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedBrand === 'all' ? 'active' : ''; ?>">All</a>
                        <?php foreach ($allBrands as $brand): ?>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo strtolower($brand['id']); ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
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
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=all&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'all' ? 'active' : ''; ?>">All</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=under5k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'under5k' ? 'active' : ''; ?>">Under ₹5K</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=5k-20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === '5k-20k' ? 'active' : ''; ?>">₹5K - ₹20K</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=above20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedPriceRange === 'above20k' ? 'active' : ''; ?>">Above ₹20K</a>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="filter-category">
                    <div class="filter-category-title">Rating</div>
                    <div class="filter-chips">
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=0&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 0 ? 'active' : ''; ?>">All</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4.5&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 4.5 ? 'active' : ''; ?>">★★★★★</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 4 ? 'active' : ''; ?>">★★★★☆</a>
                        <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=3&view=<?php echo $viewMode; ?>" 
                           class="filter-chip <?php echo $selectedRating == 3 ? 'active' : ''; ?>">★★★☆☆</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUCTS GRID/LIST -->
        <div class="products-container <?php echo $viewMode === 'list' ? 'list-layout' : ''; ?>">
            <?php if (empty($displayProducts)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <h3 style="font-size: 1.5rem; color: var(--text-secondary);">No products found matching your filters</h3>
                    <a href="products.php" style="margin-top: 1rem; display: inline-block; padding: 0.75rem 2rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px;">Clear Filters</a>
                </div>
            <?php else: ?>
                <?php foreach ($displayProducts as $product): ?>
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-item">
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['tags'])): ?>
                            <div class="product-tag"><?php echo ucfirst($product['tags'][0]); ?></div>
                        <?php endif; ?>
                        <span><?php echo $product['image']; ?></span>
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
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 3rem; color: var(--text-secondary);">
            Showing <?php echo count($displayProducts); ?> of <?php echo count($products); ?> products
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
