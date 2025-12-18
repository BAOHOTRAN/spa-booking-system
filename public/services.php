<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer vào

// Phân trang
$limit = 5; // số dịch vụ mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Lọc theo giá/duration
$order = '';
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'price_asc') $order = 'ORDER BY price ASC';
    if ($_GET['sort'] === 'price_desc') $order = 'ORDER BY price DESC';
    if ($_GET['sort'] === 'duration_short') $order = 'ORDER BY duration_minutes ASC';
    if ($_GET['sort'] === 'duration_long') $order = 'ORDER BY duration_minutes DESC';
}

// Query
$sql = "SELECT * FROM services WHERE 1";
$params = [];
if ($search !== '') {
    $sql .= " AND (LOWER(name_en) LIKE ? OR LOWER(name_vi_no_dau) LIKE ?)";
    $params[] = "%".strtolower($search)."%";
    $params[] = "%".strtolower($search)."%";
}
$sql .= " $order LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll();

// Đếm tổng số dịch vụ để phân trang
$countSql = "SELECT COUNT(*) as total FROM services WHERE 1";
if ($search !== '') {
    $countSql .= " AND (LOWER(name_en) LIKE ? OR LOWER(name_vi_no_dau) LIKE ?)";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = $countStmt->fetch()['total'];
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Services</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-4 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ</a>
  <h2 class="mb-4 text-center">Danh Sách Dịch Vụ</h2>

  <!-- Search & Filter Form -->
  <form method="get" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="search" class="form-control" 
             placeholder="Mời bạn nhập dịch vụ để tìm kiếm..." 
             value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="col-md-4">
      <select name="sort" class="form-select">
        <option value="">Sắp Xếp</option>
        <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort']==='price_asc') echo 'selected'; ?>>Giá Tiền ↑</option>
        <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort']==='price_desc') echo 'selected'; ?>>Giá Tiền ↓</option>
        <option value="duration_short" <?php if(isset($_GET['sort']) && $_GET['sort']==='duration_short') echo 'selected'; ?>>Thời lượng ngắn</option>
        <option value="duration_long" <?php if(isset($_GET['sort']) && $_GET['sort']==='duration_long') echo 'selected'; ?>>Thời lượng dài</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Lọc</button>
    </div>
  </form>

  <!-- Service Table -->
<table class="table table-striped table-bordered align-middle">
  <thead class="table-light">
    <tr>
      <th>Mã Dịch Vụ</th>
      <th>Tên Dịch Vụ</th>
      <th>Giá Tiền</th>
      <th>Thời Lượng Phục Vụ</th>
      <th>Mô Tả</th>
      <th>Hành Động</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($services as $s): ?>
    <tr>
      <td><?php echo htmlspecialchars($s['service_id']); ?></td>
      <td>
        <strong><?php echo htmlspecialchars($s['name_en']); ?></strong><br>
        <small class="text-muted">(<?php echo htmlspecialchars($s['name_vi_no_dau']); ?>)</small>
      </td>
      <td><?php echo number_format($s['price']); ?> VND</td>
      <td><?php echo $s['duration_minutes']; ?> Phút</td>
      <td><?php echo htmlspecialchars($s['description']); ?></td>
      <td class="d-flex gap-2">
        <a href="service_detail.php?service_id=<?php echo urlencode($s['service_id']); ?>" 
           class="btn btn-outline-info btn-sm">Chi tiết</a>
        <a href="appointment.php?service_id=<?php echo urlencode($s['service_id']); ?>" 
           class="btn btn-success btn-sm">Đặt</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i=1; $i<=$totalPages; $i++): ?>
        <li class="page-item <?php if($i==$page) echo 'active'; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

