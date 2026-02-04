<?php

/**
 * Home Controller
 * Handles homepage display
 */
class HomeController extends Controller {
    
    public function index() {
        // Load models
        $productModel = $this->model('ProductModel');
        $categoryModel = $this->model('CategoryModel');
        $brandModel = $this->model('BrandModel');
        
        // Get data
        $featuredProducts = $productModel->getFeatured();
        $allCategories = $categoryModel->getAll();
        $allBrands = $brandModel->getAll();
        
        // Pass data to view
        $data = [
            'pageTitle' => 'Home',
            'featuredProducts' => $featuredProducts,
            'allCategories' => $allCategories,
            'allBrands' => $allBrands
        ];
        
        $this->view('home/index', $data);
    }
}
