<?php
// InfinityFree Database Configuration for Lab8_5
define("HOST", "sql206.infinityfree.com");
define("DB_NAME", "if0_40668200_bookstore");
define("DB_USER", "if0_40668200");
define("DB_PASS", "baohotran682004");

// Tự động detect BASE_URL cho hosting
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}

$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$path = dirname($script) . '/';

define('BASE_URL', $protocol . $host . $path);

// PDO Database Connection
try {
    $dsn = "mysql:host=" . HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>