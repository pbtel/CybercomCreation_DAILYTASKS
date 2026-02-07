$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Normalize URI (remove double slashes)
$uri = preg_replace('#/+#', '/', $uri);

// 1. Block direct access to .php files (except index.php and legacy action scripts)
// Whitelist needed for form actions that haven't been migrated to controllers yet
$whitelist = [
'/index.php',
'/products.php',
'/cart.php',
'/orders.php',
'/login.php',
'/logout.php',
'/signup.php',
'/order-place.php',
'/login-process.php',
'/signup-process.php',
'/cart-add.php',
'/cart-update.php',
'/cart-remove.php',
'/cart-clear.php',
'/api/cart-add-ajax.php',
'/api/cart-remove-ajax.php',
'/api/cart-update-ajax.php',
'/api/cart-summary-ajax.php',
'/api/chart-data-ajax.php',
'/api/coupon-apply-ajax.php',
'/api/coupon-remove-ajax.php',
'/api/shipping-calculate-ajax.php',
'/api/shipping-method-update.php'
];

if (preg_match('/\.php$/i', $uri) && !in_array($uri, $whitelist)) {
http_response_code(403);
echo "Forbidden";
exit;
}

// 2. Serve static files directly
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
// Let PHP built-in server handle MIME types by returning false
return false;
}

// 3. Route everything else to index.php
// Emulate the rewrite rule: RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
// We need to set the $_GET['url'] parameter so App.php can parse it

// Robustly find the URL relative to the project root
$fullUri = $_SERVER['REQUEST_URI'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // e.g. /easyCart/public
$baseDir = preg_replace('/\/public$/i', '', $scriptDir); // e.g. /easyCart

// Remove baseDir from fullUri and parse it
$urlPath = parse_url($fullUri, PHP_URL_PATH);
$route = $urlPath;
if (!empty($baseDir) && $baseDir !== '/' && stripos($urlPath, $baseDir) === 0) {
$route = substr($urlPath, strlen($baseDir));
}
$route = ltrim($route, '/');
// If route is public/something, strip the public/ part
if (stripos($route, 'public/') === 0) {
$route = substr($route, 7);
}

$_GET['url'] = $route;

// Include the main entry point
require_once __DIR__ . '/index.php';