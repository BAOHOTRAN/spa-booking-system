<?php
// Demo sử dụng PDO - Lab8_5
include 'config/config.php';

echo "<h2>Demo PDO Usage - Lab8_5</h2>";

// Kiểm tra kết nối
if (isset($pdo)) {
    echo "<p style='color: green;'>✅ PDO Connection established successfully!</p>";
    
    try {
        // Hiển thị thông tin database
        $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as version");
        $info = $stmt->fetch();
        echo "<p><strong>Database:</strong> " . $info['db_name'] . "</p>";
        echo "<p><strong>MySQL Version:</strong> " . $info['version'] . "</p>";
        
        // Hiển thị các bảng có sẵn
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Các bảng trong database:</h3>";
        if (empty($tables)) {
            echo "<p><em>Chưa có bảng nào. Hãy tạo bảng cho bài lab của bạn.</em></p>";
        } else {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        }
        
        // Tạo bảng mẫu cho lab
        echo "<h3>Tạo bảng mẫu cho Lab:</h3>";
        $createTable = "
        CREATE TABLE IF NOT EXISTS lab_products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            stock INT DEFAULT 0,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($createTable);
        echo "<p>✅ Bảng 'lab_products' đã được tạo</p>";
        
        // Insert dữ liệu mẫu
        $checkData = $pdo->query("SELECT COUNT(*) FROM lab_products")->fetchColumn();
        if ($checkData == 0) {
            $insertSQL = "INSERT INTO lab_products (name, category, price, stock, description) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertSQL);
            
            $products = [
                ['Laptop Dell', 'Electronics', 15000000, 10, 'Laptop Dell Inspiron 15'],
                ['iPhone 15', 'Electronics', 25000000, 5, 'iPhone 15 Pro Max'],
                ['Áo thun', 'Fashion', 200000, 50, 'Áo thun cotton 100%'],
                ['Giày Nike', 'Fashion', 2500000, 20, 'Giày Nike Air Max'],
                ['Sách PHP', 'Books', 150000, 100, 'Sách học lập trình PHP']
            ];
            
            foreach ($products as $product) {
                $stmt->execute($product);
            }
            echo "<p>✅ Đã thêm dữ liệu mẫu</p>";
        }
        
        // Hiển thị dữ liệu
        echo "<h3>Dữ liệu trong bảng lab_products:</h3>";
        $stmt = $pdo->query("SELECT * FROM lab_products ORDER BY id");
        $products = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá</th><th>Tồn kho</th><th>Mô tả</th></tr>";
        
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . htmlspecialchars($product['name']) . "</td>";
            echo "<td>" . htmlspecialchars($product['category']) . "</td>";
            echo "<td>" . number_format($product['price']) . " VND</td>";
            echo "<td>" . $product['stock'] . "</td>";
            echo "<td>" . htmlspecialchars($product['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ PDO connection not available</p>";
}

echo "<hr>";
echo "<h3>Template code PDO cho Lab:</h3>";
echo "<pre>";
echo htmlspecialchars('
<?php
// Include config để có $pdo
include "config/config.php";

// SELECT với WHERE
$stmt = $pdo->prepare("SELECT * FROM lab_products WHERE category = ?");
$stmt->execute(["Electronics"]);
$products = $stmt->fetchAll();

// INSERT
$stmt = $pdo->prepare("INSERT INTO lab_products (name, category, price, stock) VALUES (?, ?, ?, ?)");
$stmt->execute(["Sản phẩm mới", "Category", 100000, 10]);

// UPDATE
$stmt = $pdo->prepare("UPDATE lab_products SET price = ? WHERE id = ?");
$stmt->execute([200000, 1]);

// DELETE
$stmt = $pdo->prepare("DELETE FROM lab_products WHERE id = ?");
$stmt->execute([1]);

// COUNT
$count = $pdo->query("SELECT COUNT(*) FROM lab_products")->fetchColumn();
?>
');
echo "</pre>";
?>