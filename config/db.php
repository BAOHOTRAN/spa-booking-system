<?php
// InfinityFree Database Configuration
$host = 'sql206.infinityfree.com';
$db   = 'if0_40668200_spa_db'; // Thay XXX bằng tên database thực tế
$user = 'if0_40668200';
$pass = 'baohotran682004'; // Điền password thực tế vào đây
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

/*
HƯỚNG DẪN CẬP NHẬT CHO INFINITYFREE:

1. Thay thế thông tin database:
   - $host: sql206.infinityfree.com
   - $user: if0_40668200
   - $pass: [Điền password thực tế từ InfinityFree]
   - $db: if0_40668200_spa_db (hoặc tên database bạn tạo)

2. Tạo database trên InfinityFree:
   - Vào MySQL Databases
   - Tạo database mới với tên: spa_db
   - Database name sẽ là: if0_40668200_spa_db

3. Import SQL:
   - Vào phpMyAdmin
   - Chọn database vừa tạo
   - Import file sql/spa_db.sql

4. Cập nhật password:
   - Lấy password từ MySQL Connection Details
   - Thay thế vào biến $pass ở trên
*/
