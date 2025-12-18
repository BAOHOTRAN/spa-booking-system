<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer

$message = '';

// Xử lý hủy lịch
if (isset($_GET['cancel_id'])) {
    $cancelId = $_GET['cancel_id'];

    // Lấy lịch hẹn
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id=? AND user_id=?");
    $stmt->execute([$cancelId, $_SESSION['user_id']]);
    $appt = $stmt->fetch();

    if ($appt) {
        $today = date('Y-m-d');
        $diff = strtotime($appt['appointment_date']) - strtotime($today);

        if ($diff < 86400) { // < 1 ngày
            $message = "Xin lỗi quý khách, chỉ có thể hủy lịch hẹn trước ít nhất 1 ngày.";
        } elseif ($appt['status'] === 'pending' || $appt['status'] === 'confirmed') {
            $stmt = $pdo->prepare("UPDATE appointments SET status='canceled' WHERE appointment_id=?");
            $stmt->execute([$cancelId]);
            $message = "Đã hủy lịch hẹn $cancelId thành công.";
        } else {
            $message = "Chỉ có thể hủy lịch hẹn đang chờ xử lý hoặc đã xác nhận.";
        }
    } else {
        $message = "Không tìm thấy lịch hẹn hoặc lịch hẹn không thuộc về bạn.";
    }
}

// Lấy danh sách lịch hẹn của user
$stmt = $pdo->prepare("SELECT a.*, s.name_en, s.name_vi_no_dau 
    FROM appointments a 
    JOIN services s ON a.service_id = s.service_id 
    WHERE a.user_id=? ORDER BY a.appointment_date, a.appointment_time");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Lich Hen Cua Toi</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ</a>
  <h2 class="mb-4 text-center">Lịch Hẹn Của Bạn</h2>

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
        <th>Mã Khách Hàng</th>
        <th>Loại Dịch Vụ</th>
        <th>Ngày</th>
        <th>Giờ</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($appointments)): ?>
        <tr>
          <td colspan="6" class="text-center text-muted">Bạn hiện tại chưa có lịch hẹn nào hết .</td>
        </tr>
      <?php else: ?>
        <?php foreach ($appointments as $a): ?>
        <tr>
          <td><?php echo htmlspecialchars($a['appointment_id']); ?></td>
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
              <span class="badge bg-primary">Đã Xác Nhận</span>
            <?php elseif ($a['status'] === 'completed'): ?>
              <span class="badge bg-success">Đã Hoàn Thành</span>
            <?php elseif ($a['status'] === 'canceled'): ?>
              <span class="badge bg-danger">Đã Hủy</span>
            <?php else: ?>
              <?php echo htmlspecialchars($a['status']); ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($a['status'] === 'pending' || $a['status'] === 'confirmed'): ?>
              <a href="?cancel_id=<?php echo urlencode($a['appointment_id']); ?>" 
                 class="btn btn-outline-danger btn-sm"
                 onclick="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này ?');">
                Hủy lịch hẹn
              </a>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
