<?php
/**
 * Setup Default Users Script
 * Run this to create default users for each role
 * Access: http://localhost/church-system/public/setup-users.php
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

// Password hash for 'admin123'
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Default users to create
$users = [
    [
        'username' => 'admin',
        'email' => 'admin@church.org',
        'password_hash' => $hash,
        'role' => 'admin',
        'first_name' => 'System',
        'last_name' => 'Administrator',
        'is_active' => 1
    ],
    [
        'username' => 'finance',
        'email' => 'finance@church.org',
        'password_hash' => $hash,
        'role' => 'finance',
        'first_name' => 'John',
        'last_name' => 'Finance',
        'is_active' => 1
    ],
    [
        'username' => 'secretariat',
        'email' => 'secretariat@church.org',
        'password_hash' => $hash,
        'role' => 'secretariat',
        'first_name' => 'Jane',
        'last_name' => 'Secretary',
        'is_active' => 1
    ],
    [
        'username' => 'pastor',
        'email' => 'pastor@church.org',
        'password_hash' => $hash,
        'role' => 'pastor',
        'first_name' => 'Rev',
        'last_name' => 'Pastor',
        'is_active' => 1
    ],
    [
        'username' => 'leader',
        'email' => 'leader@church.org',
        'password_hash' => $hash,
        'role' => 'leader',
        'first_name' => 'Mike',
        'last_name' => 'Leader',
        'is_active' => 1
    ],
    [
        'username' => 'member',
        'email' => 'member@church.org',
        'password_hash' => $hash,
        'role' => 'member',
        'first_name' => 'Bob',
        'last_name' => 'Member',
        'is_active' => 1
    ]
];

echo "<h2>Setting up default users...</h2>";

foreach ($users as $user) {
    // Check if user already exists
    $db->select('users', '*', ['email' => $user['email']]);
    $existing = $db->first();
    
    if ($existing) {
        // Update existing user
        $db->update('users', $user, ['email' => $user['email']]);
        echo "✓ Updated user: " . $user['email'] . " (" . $user['role'] . ")<br>";
    } else {
        // Insert new user
        $db->insert('users', $user);
        echo "✓ Created user: " . $user['email'] . " (" . $user['role'] . ")<br>";
    }
}

echo "<h3>Setup complete!</h3>";
echo "<p><strong>All users:</strong></p>";
echo "<ul>";
foreach ($users as $user) {
    echo "<li>" . $user['email'] . " - Role: " . $user['role'] . " - Password: admin123</li>";
}
echo "</ul>";
echo "<p><a href='index.php?route=login'>Go to Login</a></p>";
