<?php
// Test Database Connection for Lab8_5
include 'config/config.php';

echo "<h2>Test Database Connection - Lab8_5</h2>";
echo "<p><strong>Host:</strong> " . HOST . "</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Username:</strong> " . DB_USER . "</p>";
echo "<p><strong>Base URL:</strong> " . BASE_URL . "</p>";

try {
    // Test PDO connection
    $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'><strong>✅ Database connection successful!</strong></p>";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p><strong>MySQL Version:</strong> " . $version['version'] . "</p>";
    
    // Show tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<p><strong>Available Tables:</strong></p><ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>❌ Database connection failed:</strong> " . $e->getMessage() . "</p>";
}
?>