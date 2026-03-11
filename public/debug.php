<?php
// Debug file - access this to see URL parsing

// Set proper headers
header('Content-Type: text/plain');

// Simulate what index.php does
echo "=== URL Parsing Debug ===\n\n";

$url = $_SERVER['REQUEST_URI'];
echo "1. REQUEST_URI: $url\n";

$url = strtok($url, '?');
echo "2. After strtok: $url\n";

$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$scriptPath = str_replace('\\', '/', $scriptPath);
echo "3. Script path: $scriptPath\n";

if (!empty($scriptPath) && $scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
    $url = substr($url, strlen($scriptPath));
}
echo "4. After removing script path: $url\n";

if (empty($url) || $url[0] !== '/') {
    $url = '/' . $url;
}
echo "5. After ensuring /: $url\n";

if ($url !== '/' && substr($url, -1) === '/') {
    $url = rtrim($url, '/');
}
echo "6. After rtrim: $url\n";

echo "\n=== Expected Routes ===\n";
echo "/login should match\n";
echo "/ should match\n";
