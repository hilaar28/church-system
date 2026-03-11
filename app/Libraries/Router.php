<?php
/**
 * Router Class
 */

class Router {
    private static $routes = [];
    private static $currentRoute = '';

    /**
     * Load routes
     */
    public static function load($routes) {
        self::$routes = $routes;
    }

    /**
     * Get current route
     */
    public static function getCurrentRoute() {
        return self::$currentRoute;
    }

    /**
     * Parse request
     */
    public static function parse($url) {
        // Remove query string
        $url = strtok($url, '?');
        
        // Remove trailing slash
        $url = rtrim($url, '/');
        
        // Default to home
        if (empty($url)) {
            $url = '/';
        }
        
        // Check if route exists
        if (isset(self::$routes[$url])) {
            self::$currentRoute = $url;
            return self::$routes[$url];
        }
        
        // Check for dynamic routes
        foreach (self::$routes as $route => $handler) {
            if (strpos($route, ':') !== false) {
                $pattern = preg_replace('/\/:([^:]+)/', '/(?P<$1>[^/]+)', $route);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $url, $matches)) {
                    self::$currentRoute = $route;
                    
                    // Extract named parameters
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (!is_numeric($key)) {
                            $params[$key] = $value;
                        }
                    }
                    
                    return [
                        'controller' => $handler['controller'],
                        'method' => $handler['method'],
                        'params' => $params
                    ];
                }
            }
        }
        
        // Route not found
        return [
            'controller' => 'Error',
            'method' => 'notFound'
        ];
    }

    /**
     * Get route URL
     */
    public static function getUrl($route, $params = []) {
        $url = $route;
        
        foreach ($params as $key => $value) {
            $url = str_replace(':' . $key, $value, $url);
        }
        
        return $url;
    }
}
