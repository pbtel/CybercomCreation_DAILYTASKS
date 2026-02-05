<?php

/**
 * Main Application Bootstrap Class
 * Handles URL parsing and routing to appropriate controllers
 */
class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        
        $controllerName = 'home';
        if (isset($url[0])) {
            $controllerName = $url[0];
        }

        // Handle special aliases
        if ($controllerName == 'login') {
            $url[0] = 'auth';
            if (!isset($url[1])) $url[1] = 'login';
        } elseif ($controllerName == 'signup') {
            $url[0] = 'auth';
            if (!isset($url[1])) $url[1] = 'signup';
        } elseif ($controllerName == 'logout') {
            $url[0] = 'auth';
            if (!isset($url[1])) $url[1] = 'logout';
        } elseif ($controllerName == 'product' && isset($url[1]) && is_numeric($url[1])) {
            $url[0] = 'product';
            array_splice($url, 1, 0, 'show'); // Insert 'show' before the ID
        } elseif ($controllerName == 'order' && isset($url[1]) && is_numeric($url[1])) {
            $url[0] = 'order';
            array_splice($url, 1, 0, 'show');
        }

        // Check if controller exists
        $controllerFile = __DIR__ . '/../controllers/' . ucfirst($url[0]) . 'Controller.php';
        if (file_exists($controllerFile)) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        // Require the controller
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Check if method exists
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call the controller method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL from query string
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        
        return ['home'];
    }
}
