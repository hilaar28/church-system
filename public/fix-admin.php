<?php
/**
 * Fix Admin Password Script
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h1>Fix Admin Password</h1>";

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Hash password
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Update or insert admin user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, role, first_name, last_name, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE password_hash = ?
    ");
    
    $stmt->execute([
        'admin',
        'admin@church.org',
        $hash,
        'admin',
        'System',
        'Administrator',
        1,
        $hash
    ]);
    
    echo "<p>✓ Admin password has been set/updated</p>";
    echo "<p>Login: <strong>admin@church.org</strong> / <strong>admin123</strong></p>";
    echo "<p><a href='../public/'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
