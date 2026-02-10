<?php

/**
 * Product Controller
 * Handles product listing and detail pages
 */
class Controller_Product extends Controller
{

    /**
     * Product listing page with filters
     */
    public function index()
    {
        // Load models
        $productModel = $this->model('Model_Product');
        $categoryModel = $this->model('Model_Category');
        $brandModel = $this->model('Model_Brand');

        // Get filter parameters
        $selectedCategory = $this->get('category', 'all');
        $selectedBrand = $this->get('brand', 'all');
        $selectedPriceRange = $this->get('price', 'all');
        $selectedRating = floatval($this->get('rating', 0));
        $viewMode = $this->get('view', 'grid');
        $searchQuery = $this->get('q', '');

        // Get products based on filters or search
        if (!empty($searchQuery)) {
            $displayProducts = $productModel->search($searchQuery);
        } elseif ($selectedCategory !== 'all') {
            $displayProducts = $productModel->getByCategory($selectedCategory);
        } else {
            $displayProducts = $productModel->getAll();
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

        // Apply pagination
        $itemsPerPage = 24;
        $currentPage = $this->get('page', 1);
        $totalItems = count($displayProducts);
        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        // Slice products for current page
        $pagedProducts = array_slice($displayProducts, $pagination->getOffset(), $pagination->getLimit());

        // Get all categories and brands for filter
        $allCategories = $categoryModel->getAll();
        $allBrands = $brandModel->getAll();

        // Pass data to view
        $data = [
            'pageTitle' => 'Products',
            'displayProducts' => $pagedProducts,
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'selectedCategory' => $selectedCategory,
            'selectedBrand' => $selectedBrand,
            'selectedPriceRange' => $selectedPriceRange,
            'selectedRating' => $selectedRating,
            'viewMode' => $viewMode,
            'searchQuery' => $searchQuery,
            'pagination' => $pagination,
            'totalResults' => $totalItems,
            'products' => $productModel->getAll() // For count
        ];

        // Use View_Product_Index class
        require_once __DIR__ . '/../views/View_Products.php';
        $view = new View_Product_Index($data);
        echo $view->toHtml();
    }

    /**
     * Product detail page
     * URL: /product/{id} or /product/{slug}
     */
    public function show($param = null)
    {
        if (!$param) {
            $this->redirect('products');
            return;
        }

        // Load model
        $productModel = $this->model('Model_Product');

        // Get product by ID or Slug
        if (is_numeric($param)) {
            // If accessed via ID, redirect to Slug URL for SEO consistency
            $product = $productModel->getById($param);
            if ($product && !empty($product['slug'])) {
                header("HTTP/1.1 301 Moved Permanently");
                $this->redirect('product/' . $product['slug']);
                return;
            }
        } else {
            $product = $productModel->getBySlug($param);
        }

        if (!$product) {
            Session::setFlash('error', 'Product not found');
            $this->redirect('products');
            return;
        }

        $id = $product['id']; // Get numeric ID for further queries

        // Get additional product data
        $product['images'] = $productModel->getImages($id);

        // Get related products (same category)
        $relatedProducts = [];
        if (isset($product['category'])) {
            $relatedProducts = $productModel->getByCategory($product['category']);
            // Remove current product from related
            $relatedProducts = array_filter($relatedProducts, function ($p) use ($id) {
                return $p['id'] != $id;
            });
            // Limit to 4 products
            $relatedProducts = array_slice($relatedProducts, 0, 4);
        }

        // Pass data to view
        $data = [
            'pageTitle' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];

        // Use View_Product_Detail class
        require_once __DIR__ . '/../views/View_Products.php';
        $view = new View_Product_Detail($data);
        echo $view->toHtml();
    }
}
