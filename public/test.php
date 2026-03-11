<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    define('CONFIG_PATH', BASE_PATH . '/config');
    define('APP_PATH', BASE_PATH . '/app');
    
    require_once CONFIG_PATH . '/config.php';
    require_once CONFIG_PATH . '/database.php';
    require_once BASE_PATH . '/helpers/helpers.php';
}

// Load routes
$routes = require CONFIG_PATH . '/routes.php';

// Get current URL
$url = $_SERVER['REQUEST_URI'];
$url = strtok($url, '?');

echo "Original URL: " . $url . "<br>";

$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$scriptPath = str_replace('\\', '/', $scriptPath);

echo "Script Path: " . $scriptPath . "<br>";

if (!empty($scriptPath) && $scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
    $url = substr($url, strlen($scriptPath));
}

echo "After removing script path: " . $url . "<br>";

// Check if route exists
if (isset($routes[$url])) {
    echo "Route found: " . $routes[$url]['controller'] . " -> " . $routes[$url]['method'];
} else {
    echo "Route NOT found for: " . $url;
    echo "<br>Available routes that match /login:<br>";
    foreach ($routes as $k => $v) {
        if (strpos($k, 'login') !== false) {
            echo $k . " => " . $v['controller'] . "<br>";
        }
    }
}
