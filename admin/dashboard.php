<?php
include '../config/db.php';
include '../includes/auth.php';
checkAdmin(); // chỉ cho admin

$message = '';

// Xử lý fix payment
if (isset($_GET['fix_payment'])) {
    $paymentId = $_GET['fix_payment'];
    
    $stmt = $pdo->prepare("UPDATE payments SET status='paid' WHERE payment_id=? AND status='unpaid'");
    $result = $stmt->execute([$paymentId]);
    
    if ($result && $stmt->rowCount() > 0) {
        $message = "✅ Đã cập nhật payment $paymentId thành 'paid'. Doanh thu sẽ được cập nhật.";
    } else {
        $message = "❌ Không thể cập nhật payment $paymentId.";
    }
    
    // Redirect để tránh resubmit
    header("Location: dashboard.php?msg=" . urlencode($message));
    exit;
}

// Hiển thị message từ redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// === THỐNG KÊ LỊCH HẸN ===
// Tổng số lịch hẹn (tất cả)
$stmt = $pdo->query("SELECT COUNT(*) as total_appt FROM appointments");
$totalAppt = $stmt->fetch()['total_appt'];

// Lịch hẹn đã hoàn thành
$stmt = $pdo->query("SELECT COUNT(*) as completed_appt FROM appointments WHERE status='completed'");
$completedAppt = $stmt->fetch()['completed_appt'];

// Lịch hẹn bị hủy
$stmt = $pdo->query("SELECT COUNT(*) as canceled_appt FROM appointments WHERE status='canceled'");
$canceledAppt = $stmt->fetch()['canceled_appt'];

// Lịch hẹn đang chờ/xác nhận
$stmt = $pdo->query("SELECT COUNT(*) as pending_appt FROM appointments WHERE status IN ('pending','confirmed')");
$pendingAppt = $stmt->fetch()['pending_appt'];

// Tổng doanh thu (chỉ tính các payment đã paid)
$stmt = $pdo->query("SELECT SUM(amount) as total_revenue FROM payments WHERE status='paid'");
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

// === TOP DỊCH VỤ PHỔ BIẾN ===
// Top 3 dịch vụ (chỉ tính completed, không tính canceled)
$stmt = $pdo->query("
    SELECT s.service_id, s.name_en, s.name_vi_no_dau, 
           COUNT(a.appointment_id) as total_bookings,
           SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) as completed_bookings,
           SUM(CASE WHEN a.status='canceled' THEN 1 ELSE 0 END) as canceled_bookings
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    GROUP BY s.service_id, s.name_en, s.name_vi_no_dau
    ORDER BY completed_bookings DESC, total_bookings DESC
    LIMIT 3
");
$popularServices = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bảng Thống kê</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang Admin</a>
  <h2 class="mb-4 text-center">Bảng Thống Kê</h2>


  <!-- Cards thống kê đơn giản -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="fas fa-calendar-check text-primary mb-2" style="font-size: 2rem;"></i>
          <h5 class="card-title">Tổng Lịch Hẹn</h5>
          <p class="display-6 text-primary"><?php echo $totalAppt; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
          <h5 class="card-title">Đã Hoàn Thành</h5>
          <p class="display-6 text-success"><?php echo $completedAppt; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="fas fa-times-circle text-danger mb-2" style="font-size: 2rem;"></i>
          <h5 class="card-title">Đã Hủy</h5>
          <p class="display-6 text-danger"><?php echo $canceledAppt; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="fas fa-money-bill-wave text-success mb-2" style="font-size: 2rem;"></i>
          <h5 class="card-title">Tổng Doanh Thu</h5>
          <p class="display-6 text-success"><?php echo number_format($totalRevenue); ?> VND</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Top 3 dịch vụ phổ biến -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-3">Top 3 Dịch Vụ Phổ Biến</h4>
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Mã Dịch Vụ</th>
            <th>Tên Dịch Vụ</th>
            <th>Số Lần Đặt</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($popularServices)): ?>
            <tr>
              <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
            </tr>
          <?php else: ?>
            <?php foreach ($popularServices as $s): ?>
            <tr>
              <td><?php echo htmlspecialchars($s['service_id']); ?></td>
              <td>
                <strong><?php echo htmlspecialchars($s['name_en']); ?></strong><br>
                <small class="text-muted">(<?php echo htmlspecialchars($s['name_vi_no_dau']); ?>)</small>
              </td>
              <td>
                <span class="badge bg-primary"><?php echo $s['completed_bookings']; ?></span>
                <small class="text-muted ms-1">lần hoàn thành</small>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

