<?php
include '../config/db.php';
include '../includes/auth.php';
checkAdmin();

$service_id = $_GET['service_id'] ?? null;
if (!$service_id) { die("Chua chon dich vu."); }

$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id=?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();
if (!$service) { die("Khong tim thay dich vu."); }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Chi tiết dịch vụ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="manage_services.php" class="btn btn-outline-secondary">← Quay lại quản lý dịch vụ</a>
  <h2 class="mb-4">Chi Tiết Dịch Vụ : <?php echo htmlspecialchars($service['name_en']); ?></h2>
  <div class="row">
    <div class="col-md-4">
      <?php if (!empty($service['image_url'])): ?>
        <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="Service" class="img-fluid rounded">
      <?php else: ?>
        <div class="alert alert-secondary">Chưa có hình ảnh.</div>
      <?php endif; ?>
    </div>
    <div class="col-md-8">
      <ul class="list-group">
        <li class="list-group-item"><strong>Mã Dịch Vụ:</strong> <?php echo htmlspecialchars($service['service_id']); ?></li>
        <li class="list-group-item"><strong>Tên Tiếng Anh:</strong> <?php echo htmlspecialchars($service['name_en']); ?></li>
        <li class="list-group-item"><strong>Ten Tiếng Việt:</strong> <?php echo htmlspecialchars($service['name_vi_no_dau']); ?></li>
        <li class="list-group-item"><strong>Giá tiền:</strong> <?php echo number_format($service['price']); ?> VND</li>
        <li class="list-group-item"><strong>Thời Lượng Phục Vụ:</strong> <?php echo $service['duration_minutes']; ?> Phút</li>
        <li class="list-group-item"><strong>Mô Tả:</strong> <?php echo htmlspecialchars($service['description']); ?></li>
      </ul>
      <a href="manage_services.php" class="btn btn-secondary mt-3">Quay lại</a>
    </div>
  </div>
</div>
</body>
</html>
