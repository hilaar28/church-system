<?php
/**
 * Base Controller Class
 */

class Controller {
    protected $db;
    protected $auth;
    protected $data = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->auth = new Auth();
        
        // Check authentication for protected routes
        $this->checkAuth();
    }

    /**
     * Check authentication
     */
    protected function checkAuth() {
        $publicRoutes = ['/login', '/login/authenticate', '/register', '/register/store', '/forgot-password', '/reset-password', '/dashboard'];
        $currentRoute = Router::getCurrentRoute();
        
        if (!$this->auth->isLoggedIn() && !in_array($currentRoute, $publicRoutes)) {
            redirect('/login');
        }
    }

    /**
     * Load view
     */
    protected function view($view, $data = []) {
        // Add auth to data so it's available in views
        $data['auth'] = $this->auth;
        
        extract(array_merge($this->data, $data));
        
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            $this->notFound();
        }
    }

    /**
     * Load model
     */
    protected function model($model) {
        $modelFile = MODEL_PATH . '/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        
        return null;
    }

    /**
     * Redirect
     */
    protected function redirect($url) {
        // Get the base URL from the request
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $scheme . '://' . $host . '/church-system/public';
        
        header('Location: ' . $baseUrl . $url);
        exit;
    }

    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get input
     */
    protected function input($key = null, $default = null) {
        if ($key === null) {
            return array_merge($_GET, $_POST);
        }
        
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get sanitized input
     */
    protected function sanitize($key) {
        return Validator::sanitize($this->input($key));
    }

    /**
     * 404 error
     */
    protected function notFound() {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        exit;
    }

    /**
     * Access denied
     */
    protected function accessDenied() {
        http_response_code(403);
        echo '<h1>403 - Access Denied</h1>';
        exit;
    }

    /**
     * Get current user
     */
    protected function user() {
        return $this->auth->user();
    }

    /**
     * Check permission
     */
    protected function hasPermission($permission) {
        return $this->auth->hasPermission($permission);
    }

    /**
     * Flash message
     */
    protected function flash($key, $message = null) {
        if ($message === null) {
            // Get message
            return $_SESSION['flash'][$key] ?? null;
        }
        
        // Set message
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Old input
     */
    protected function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }

    /**
     * Clear old input
     */
    protected function clearOld() {
        unset($_SESSION['old']);
    }
}
