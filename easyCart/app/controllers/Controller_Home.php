<?php

/**
 * Home Controller
 * Handles homepage display
 */
class Controller_Home extends Controller
{
    public function index()
    {
        // Load models
        $productModel = $this->model('Model_Product');
        $categoryModel = $this->model('Model_Category');
        $brandModel = $this->model('Model_Brand');

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

        // Use View_Home class
        require_once __DIR__ . '/../views/View_Home.php';
        $view = new View_Home($data);
        echo $view->toHtml();
    }
}
