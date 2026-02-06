<?php
$pageTitle = "Products";
require_once __DIR__ . '/includes/header.php';

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
    $displayProducts = array_filter($displayProducts, function ($product) use ($selectedBrand) {
        return strtolower($product['brand']) === strtolower($selectedBrand);
    });
}

// Apply price filter
if ($selectedPriceRange !== 'all') {
    switch ($selectedPriceRange) {
        case 'under5k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] < 5000;
            });
            break;
        case '5k-20k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] >= 5000 && $p['price'] <= 20000;
            });
            break;
        case 'above20k':
            $displayProducts = array_filter($displayProducts, function ($p) {
                return $p['price'] > 20000;
            });
            break;
    }
}

// Apply rating filter
if ($selectedRating > 0) {
    $displayProducts = array_filter($displayProducts, function ($p) use ($selectedRating) {
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
                    class="view-control-btn <?php echo $viewMode === 'grid' ? 'active' : ''; ?>">&#9724;</a>
                <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=<?php echo $selectedRating; ?>&view=list"
                    class="view-control-btn <?php echo $viewMode === 'list' ? 'active' : ''; ?>">&#9776;</a>
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
                        class="filter-chip <?php echo $selectedPriceRange === 'under5k' ? 'active' : ''; ?>">Under
                        &#8377;15K</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=5k-20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedPriceRange === '5k-20k' ? 'active' : ''; ?>">&#8377;15K -
                        &#8377;120K</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=above20k&rating=<?php echo $selectedRating; ?>&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedPriceRange === 'above20k' ? 'active' : ''; ?>">Above
                        &#8377;120K</a>
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="filter-category">
                <div class="filter-category-title">Rating</div>
                <div class="filter-chips">
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=0&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedRating == 0 ? 'active' : ''; ?>">All</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4.5&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedRating == 4.5 ? 'active' : ''; ?>">&#9733;&#9733;&#9733;&#9733;&#9733;</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=4&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedRating == 4 ? 'active' : ''; ?>">&#9733;&#9733;&#9733;&#9733;&#9734;</a>
                    <a href="?category=<?php echo $selectedCategory; ?>&brand=<?php echo $selectedBrand; ?>&price=<?php echo $selectedPriceRange; ?>&rating=3&view=<?php echo $viewMode; ?>"
                        class="filter-chip <?php echo $selectedRating == 3 ? 'active' : ''; ?>">&#9733;&#9733;&#9733;&#9734;&#9734;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTS GRID/LIST -->
    <div class="products-container <?php echo $viewMode === 'list' ? 'list-layout' : ''; ?>">
        <?php if (empty($displayProducts)): ?>
            <div class="no-products">
                <h3 class="no-products-title">No products found matching your filters</h3>
                <a href="products.php" class="btn-clear-filters">Clear Filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($displayProducts as $product): ?>
                <a href="../../product-detail.php?id=<?php echo $product['id']; ?>" class="product-item">
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
                            for ($i = 0; $i < $fullStars; $i++)
                                echo '&#9733;';
                            if ($hasHalfStar)
                                echo '&#9733;';
                            for ($i = 0; $i < (5 - ceil($product['rating'])); $i++)
                                echo '&#9734;';
                            ?>
                            <span><?php echo $product['rating']; ?></span>
                        </div>
                        <div class="product-pricing">
                            <span class="product-current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['original_price'] > $product['price']): ?>
                                <span class="product-old-price"><?php echo formatPrice($product['original_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($product['shipping_type'])): ?>
                            <div
                                class="product-shipping-badge <?php echo $product['shipping_type'] === 'Express' ? 'badge-express' : 'badge-standard'; ?>">
                                <?php echo $product['shipping_type'] === 'Express' ? '&#9889;' : '&#128666;'; ?>
                                <?php echo $product['shipping_type']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="products-counter">
        Showing <?php echo count($displayProducts); ?> of <?php echo count($products); ?> products
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>