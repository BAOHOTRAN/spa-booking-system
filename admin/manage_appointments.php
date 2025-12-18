<?php
include '../config/db.php';
include '../includes/auth.php';
checkAdmin(); // chỉ cho admin

$message = '';

// Xử lý cập nhật trạng thái
if (isset($_GET['update_id']) && isset($_GET['status'])) {
    $id = $_GET['update_id'];
    $status = $_GET['status'];

    if (in_array($status, ['confirmed','canceled','completed'])) {
        $stmt = $pdo->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
        $stmt->execute([$status, $id]);
        $message = "Appointment $id updated to $status.";
    } else {
        $message = "Invalid status.";
    }
}

// Lấy danh sách lịch hẹn
$stmt = $pdo->query("
    SELECT a.*, u.username, s.name_en, s.name_vi_no_dau 
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY a.appointment_date, a.appointment_time
");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Quản Lý Lịch Hẹn</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang Admin</a>
  <h2 class="mb-4 text-center">Quản Lý Lịch Hẹn</h2>

  <!-- Hiển thị thông báo -->
  <?php if ($message !== ''): ?>
    <div class="alert alert-info text-center">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <!-- Bảng lịch hẹn -->
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Mã Lịch Hẹn</th>
        <th>Người Dùng </th>
        <th>Dịch Vụ</th>
        <th>Ngày</th>
        <th>Giờ</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?php echo htmlspecialchars($a['appointment_id']); ?></td>
        <td><?php echo htmlspecialchars($a['username']); ?></td>
        <td>
          <strong><?php echo htmlspecialchars($a['name_en']); ?></strong><br>
          <small class="text-muted">(<?php echo htmlspecialchars($a['name_vi_no_dau']); ?>)</small>
        </td>
        <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
        <td>
          <?php if ($a['status'] === 'pending'): ?>
            <span class="badge bg-warning text-dark">Chờ Xử Lý</span>
          <?php elseif ($a['status'] === 'confirmed'): ?>
            <span class="badge bg-primary">Xác Nhận</span>
          <?php elseif ($a['status'] === 'completed'): ?>
            <span class="badge bg-success">Đã Hoàn Thành</span>
          <?php elseif ($a['status'] === 'canceled'): ?>
            <span class="badge bg-danger">Đã Hủy</span>
          <?php else: ?>
            <?php echo htmlspecialchars($a['status']); ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($a['status'] === 'pending'): ?>
            <a href="?update_id=<?php echo urlencode($a['appointment_id']); ?>&status=confirmed" 
               class="btn btn-outline-primary btn-sm">Xác Nhận</a>
            <a href="?update_id=<?php echo urlencode($a['appointment_id']); ?>&status=canceled" 
               class="btn btn-outline-danger btn-sm">Hủy</a>
          <?php elseif ($a['status'] === 'confirmed'): ?>
            <a href="?update_id=<?php echo urlencode($a['appointment_id']); ?>&status=completed" 
               class="btn btn-outline-success btn-sm">Hoàn Thành </a>
            <a href="?update_id=<?php echo urlencode($a['appointment_id']); ?>&status=canceled" 
               class="btn btn-outline-danger btn-sm">Hủy</a>
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
