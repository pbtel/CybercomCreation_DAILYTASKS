<?php

/**
 * Base Controller Class
 * All controllers extend this class
 */
class Controller {
    
    /**
     * Load a model
     */
    public function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    /**
     * Load a view with data
     */
    public function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Check if view file exists
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }

    /**
     * Redirect to another URL
     */
    public function redirect($url) {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Return JSON response
     */
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data
     */
    public function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Get GET data
     */
    public function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Check if request is POST
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is GET
     */
    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
