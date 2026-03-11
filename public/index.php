<?php
/**
 * Church Management System - Entry Point
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    // Set cookie parameters for subdirectory installation
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', '');
    ini_set('session.cookie_secure', '0');
    ini_set('session.cookie_httponly', '1');
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('APP_PATH', BASE_PATH . '/app');

// Load configuration
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

// Load helpers
require_once BASE_PATH . '/helpers/helpers.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // Check in libraries
    $libPath = APP_PATH . '/Libraries/' . $class . '.php';
    if (file_exists($libPath)) {
        require_once $libPath;
        return;
    }
    
    // Check in models
    $modelPath = APP_PATH . '/Models/' . $class . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }
    
    // Check in controllers
    $controllerPath = APP_PATH . '/Controllers/' . $class . '.php';
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        return;
    }
});

// Initialize database
try {
    Database::getInstance();
} catch (Exception $e) {
    die('Database connection failed. Please run the setup script: <a href="/setup.php">Setup</a>');
}

// Load routes
$routes = require CONFIG_PATH . '/routes.php';
Router::load($routes);

// Get current URL - check both REQUEST_URI and query string
$url = $_GET['route'] ?? $_SERVER['REQUEST_URI'];

if (isset($_GET['route'])) {
    $url = '/' . $_GET['route'];
} else {
    $url = $_SERVER['REQUEST_URI'];
    $url = strtok($url, '?');
    
    // Get the base path from SCRIPT_NAME
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    // Normalize to forward slashes
    $scriptPath = str_replace('\\', '/', $scriptPath);
    
    // Remove the script path from the URL
    if (!empty($scriptPath) && $scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
        $url = substr($url, strlen($scriptPath));
    }
}

// Ensure URL starts with /
if (empty($url) || $url[0] !== '/') {
    $url = '/' . $url;
}

// Remove trailing slash except for root
if ($url !== '/' && substr($url, -1) === '/') {
    $url = rtrim($url, '/');
}

// Parse route
$route = Router::parse($url);

// Get controller and method
$controllerName = $route['controller'] . 'Controller';
$method = $route['method'] ?? 'index';
$params = $route['params'] ?? [];

// Check if controller exists
$controllerPath = APP_PATH . '/Controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    http_response_code(404);
    echo '<h1>404 - Controller Not Found</h1>';
    echo '<p>Looking for: ' . $controllerName . '.php</p>';
    echo '<p>URL was: ' . $url . '</p>';
    exit;
}

// Include controller
require_once $controllerPath;

// Check if method exists
if (!method_exists($controllerName, $method)) {
    http_response_code(404);
    echo '<h1>404 - Method Not Found</h1>';
    exit;
}

// Instantiate controller and call method
$controller = new $controllerName();
$controller->$method($params);

// Clear old input after request
clearOld();
