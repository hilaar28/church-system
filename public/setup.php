<?php
/**
 * Database Setup Script
 * Run this once to set up the database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<h1>Church Management System - Database Setup</h1>";

try {
    // Connect without database
    $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database created or already exists</p>";
    
    // Connect to database
    $pdo->exec("USE " . DB_NAME);
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    
    // Remove USE statement from schema
    $schema = str_replace('USE church_system;', '', $schema);
    
    // Split by statement and execute
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (!empty($statement) && strpos($statement, '--') !== 0) {
            try {
                $pdo->exec($statement);
                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
                // Only show first few errors
                if ($errorCount <= 5) {
                    echo "<p style='color:orange'>⚠ Error: " . substr($e->getMessage(), 0, 100) . "...</p>";
                }
            }
        }
    }
    
    echo "<p>✓ Database tables created successfully</p>";
    echo "<p>Statements executed: $successCount, Errors: $errorCount</p>";
    
    // Test connection
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Database is ready! Users count: " . $result['count'] . "</p>";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Default login: <strong>admin@church.org</strong> / <strong>admin123</strong></p>";
    echo "<p><a href='../public/'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials in config/database.php</p>";
}
