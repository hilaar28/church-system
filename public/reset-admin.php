<?php
/**
 * Reset Admin Password Script
 * Run this to reset the admin password to 'admin123'
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $libPath = __DIR__ . '/../app/Libraries/' . $class . '.php';
    if (file_exists($libPath)) {
        require_once $libPath;
    }
});

// Initialize database
$db = Database::getInstance();

// Hash the new password
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Update the admin user
$result = $db->update('users', [
    'password_hash' => $hash,
    'is_active' => 1
], ['email' => 'admin@church.org']);

if ($result) {
    echo "✓ Admin password reset successfully!<br>";
    echo "Email: admin@church.org<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='index.php?route=login'>Go to Login</a>";
} else {
    echo "✗ Failed to reset password";
}
