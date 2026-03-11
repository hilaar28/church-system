<?php
/**
 * Helper Functions
 */

// Redirect helper
function redirect($url) {
    // Get the base URL from the request
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $scheme . '://' . $host . '/church-system/public';
    
    header('Location: ' . $baseUrl . $url);
    exit;
}

// Get current URL
function currentUrl() {
    return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}

// Get base URL
function baseUrl($path = '') {
    return SITE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

// Get assets URL
function assetUrl($path = '') {
    return baseUrl('public/' . ltrim($path, '/'));
}

// Format date
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (!$date) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

// Format datetime
function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (!$datetime) return '';
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    return date($format, $timestamp);
}

// Format time
function formatTime($time, $format = 'H:i') {
    if (!$time) return '';
    $timestamp = is_numeric($time) ? $time : strtotime($time);
    return date($format, $timestamp);
}

// Format currency
function formatCurrency($amount, $symbol = CURRENCY_SYMBOL) {
    return $symbol . number_format($amount, 2);
}

// Generate CSRF token
function csrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCsrfToken($token) {
    if (empty($token)) {
        return false;
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

// Get flash message
function flash($key, $message = null) {
    if ($message === null) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
    $_SESSION['flash'][$key] = $message;
}

// Old input value
function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

// Clear old input
function clearOld() {
    unset($_SESSION['old']);
}

// Save old input
function saveOld($data) {
    $_SESSION['old'] = $data;
}

// Truncate text
function truncate($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $append;
}

// Get status badge class
function getStatusClass($status) {
    $classes = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'present' => 'success',
        'absent' => 'danger',
        'excused' => 'warning',
        'visitor' => 'info',
        'member' => 'primary',
    ];
    
    return $classes[$status] ?? 'secondary';
}

// Escape HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Array get helper
function array_get($array, $key, $default = null) {
    if (!is_array($array)) {
        return $default;
    }
    
    return $array[$key] ?? $default;
}

// Check if request is AJAX
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get user IP
function getIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Calculate age
function calculateAge($birthDate) {
    if (!$birthDate) return null;
    
    $birth = new DateTime($birthDate);
    $today = new DateTime('today');
    $age = $birth->diff($today)->y;
    
    return $age;
}

// Get initials
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    
    foreach ($words as $word) {
        $initials .= strtoupper($word[0]);
    }
    
    return substr($initials, 0, 2);
}

// Paginate helper
function paginate($total, $perPage, $currentPage) {
    $totalPages = ceil($total / $perPage);
    $adjacents = 2;
    
    // Calculate start and end page numbers for pagination links
    if ($totalPages <= $adjacents * 2) {
        $start = 1;
        $end = $totalPages;
    } else {
        if ($currentPage <= $adjacents) {
            $start = 1;
            $end = $adjacents * 2 + 1;
        } elseif ($currentPage > $totalPages - $adjacents) {
            $start = $totalPages - $adjacents * 2;
            $end = $totalPages;
        } else {
            $start = $currentPage - $adjacents;
            $end = $currentPage + $adjacents;
        }
    }
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current' => $currentPage,
        'total_pages' => $totalPages,
        'start' => $start,
        'end' => $end,
        'from' => (($currentPage - 1) * $perPage) + 1,
        'to' => min($currentPage * $perPage, $total),
        'prev' => $currentPage > 1 ? $currentPage - 1 : false,
        'next' => $currentPage < $totalPages ? $currentPage + 1 : false,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}
