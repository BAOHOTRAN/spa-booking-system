<?php
// Test Database Connection for Lab8_4
include 'config/config.php';

echo "<h2>Test Database Connection - Lab8_4</h2>";
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
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>❌ Database connection failed:</strong> " . $e->getMessage() . "</p>";
}

// Test using configDB array
echo "<hr><h3>Test configDB Array:</h3>";
try {
    $pdo2 = new PDO("mysql:host=" . $configDB['host'] . ";dbname=" . $configDB['database'] . ";charset=utf8", 
                    $configDB['username'], $configDB['password']);
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'><strong>✅ configDB connection successful!</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>❌ configDB connection failed:</strong> " . $e->getMessage() . "</p>";
}
?>