<?php

/**
 * Product Controller
 * Handles product listing and detail pages
 */
class ProductController extends Controller {
    
    /**
     * Product listing page with filters
     */
    public function index() {
        // Load models
        $productModel = $this->model('ProductModel');
        $categoryModel = $this->model('CategoryModel');
        $brandModel = $this->model('BrandModel');
        
        // Get filter parameters
        $selectedCategory = $this->get('category', 'all');
        $selectedBrand = $this->get('brand', 'all');
        $selectedPriceRange = $this->get('price', 'all');
        $selectedRating = floatval($this->get('rating', 0));
        $viewMode = $this->get('view', 'grid');
        
        // Get products based on filters
        if ($selectedCategory !== 'all') {
            $displayProducts = $productModel->getByCategory($selectedCategory);
        } else {
            $displayProducts = $productModel->getAll();
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
        
        // Get all categories and brands for filter
        $allCategories = $categoryModel->getAll();
        $allBrands = $brandModel->getAll();
        
        // Pass data to view
        $data = [
            'pageTitle' => 'Products',
            'displayProducts' => $displayProducts,
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'selectedCategory' => $selectedCategory,
            'selectedBrand' => $selectedBrand,
            'selectedPriceRange' => $selectedPriceRange,
            'selectedRating' => $selectedRating,
            'viewMode' => $viewMode,
            'products' => $productModel->getAll() // For count
        ];
        
        $this->view('products/index', $data);
    }
    
    /**
     * Product detail page
     * URL: /product/{id}
     */
    public function show($id = null) {
        if (!$id) {
            $this->redirect('products');
            return;
        }
        
        // Load model
        $productModel = $this->model('ProductModel');
        
        // Get product
        $product = $productModel->getById($id);
        
        if (!$product) {
            Session::setFlash('error', 'Product not found');
            $this->redirect('products');
            return;
        }

        // Get additional product data
        $product['images'] = $productModel->getImages($id);
        $product['variants'] = $productModel->getVariants($id);
        
        // Get related products (same category)
        $relatedProducts = $productModel->getByCategory($product['category']);
        // Remove current product from related
        $relatedProducts = array_filter($relatedProducts, function($p) use ($id) {
            return $p['id'] != $id;
        });
        // Limit to 4 products
        $relatedProducts = array_slice($relatedProducts, 0, 4);
        
        // Pass data to view
        $data = [
            'pageTitle' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];
        
        $this->view('products/detail', $data);
    }
}
