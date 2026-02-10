<?php

/**
 * Main Application Bootstrap Class
 * Handles URL parsing and routing to appropriate controllers
 */
class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $segments = $this->parseUrl();

        // 1. Normalize controller aliases (Plural to singular, auth routes)
        $alias = isset($segments[0]) ? strtolower($segments[0]) : 'home';

        if ($alias === 'products') {
            $segments[0] = 'product';
        } elseif ($alias === 'orders') {
            $segments[0] = 'order';
        } elseif ($alias === 'login') {
            $segments[0] = 'auth';
            $segments[1] = 'login';
        } elseif ($alias === 'signup') {
            $segments[0] = 'auth';
            $segments[1] = 'signup';
        } elseif ($alias === 'logout') {
            $segments[0] = 'auth';
            $segments[1] = 'logout';
        }

        // 2. Resolve Controller
        $controllerName = isset($segments[0]) ? ucfirst(strtolower($segments[0])) : 'Home';
        $controllerClass = $controllerName . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        if (file_exists($controllerFile)) {
            $this->controller = $controllerClass;
            unset($segments[0]);
        } else {
            if ($controllerName !== 'Home') {
                http_response_code(404);
                header('Content-Type: application/json');
                die(json_encode(['success' => false, 'message' => "Endpoint /$controllerName not found"]));
            }
        }

        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Re-index segments so methods and params are always at fixed indices
        $segments = array_values($segments);

        // 3. Handle API Method Mapping
        if ($controllerName === 'Api' && isset($segments[0])) {
            $apiRoutes = [
                'cart-add' => 'cartAdd',
                'cart-update' => 'cartUpdate',
                'cart-remove' => 'cartRemove',
                'cart-summary' => 'cartSummary',
                'coupon-apply' => 'couponApply',
                'coupon-remove' => 'couponRemove',
                'shipping-calculate' => 'shippingCalculate',
                'shipping-method-update' => 'shippingMethodUpdate',
                'chart-data' => 'chartData'
            ];
            if (isset($apiRoutes[$segments[0]])) {
                $segments[0] = $apiRoutes[$segments[0]];
            }
        }

        // 4. Handle Parameterized Routes (e.g. product/1 or product/slug -> product/show/param)
        $currentControllerName = str_replace('Controller', '', get_class($this->controller));
        if (in_array(strtolower($currentControllerName), ['product', 'order'])) {
            // Check if first segment is a valid method
            $isMethod = isset($segments[0]) && method_exists($this->controller, $segments[0]);

            // If it's NOT a method, treat it as a parameter for 'show'
            // This covers both numeric IDs and string Slugs
            if (isset($segments[0]) && !$isMethod) {
                // Prepend 'show' as the method
                array_unshift($segments, 'show');
            }
        }

        // 5. Resolve Method
        if (isset($segments[0])) {
            if (method_exists($this->controller, $segments[0])) {
                $this->method = $segments[0];
                unset($segments[0]);
            }
        }

        // 6. Dispatch
        $this->params = $segments ? array_values($segments) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL from query string or server environment
     */
    protected function parseUrl()
    {
        $url = null;

        // 1. Priority: $_GET['url'] (Set by .htaccess or manual proxy)
        if (isset($_GET['url']) && !empty($_GET['url'])) {
            $url = $_GET['url'];
        }
        // 2. Fallback: REQUEST_URI manual parsing
        else {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $scriptName = $_SERVER['SCRIPT_NAME'];

            // Get the directory of the script being executed
            $scriptDir = str_replace('\\', '/', dirname($scriptName));

            // Try to find the base part (strip /public if it's there)
            $basePath = preg_replace('/\/public$/i', '', $scriptDir);

            // If the URI starts with the base path, strip it
            if ($basePath !== '' && $basePath !== '/' && stripos($requestUri, $basePath) === 0) {
                $url = substr($requestUri, strlen($basePath));
            } else {
                $url = $requestUri;
            }
        }

        if ($url) {
            // Clean up common entry point noise
            $url = preg_replace('/^\/?(public\/index\.php|index\.php|public)\/?/i', '', $url);
            $url = trim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);

            if ($url === '')
                return ['home'];

            $segments = explode('/', $url);
            // Filter out empty segments and re-index
            return array_values(array_filter($segments, function ($s) {
                return $s !== '';
            }));
        }

        return ['home'];
    }
}
