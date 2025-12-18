<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer

// Lấy lịch sử thanh toán của user
$stmt = $pdo->prepare("SELECT p.payment_id, p.amount, p.method, p.status, 
                              a.appointment_date, a.appointment_time, 
                              s.name_en
                       FROM payments p
                       JOIN appointments a ON p.appointment_id = a.appointment_id
                       JOIN services s ON a.service_id = s.service_id
                       WHERE a.user_id=? 
                       ORDER BY p.payment_id DESC");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Payment History</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ</a>
  <h2 class="mb-4 text-center">Lịch Sử Thanh Toán</h2>

  <!-- Bảng lịch sử -->
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Mã Thanh Toán</th>
        <th>Dịch Vụ</th>
        <th>Ngày Thanh Toán</th>
        <th>Giờ Thanh Toán</th>
        <th>Sô Tiền</th>
        <th>Phương Thức</th>
        <th>Trạng Thái</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($history)): ?>
        <tr>
          <td colspan="7" class="text-center text-muted">Hiện Tại Bạn Chưa Có Lịch Sử Thanh Toán.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($history as $h): ?>
        <tr>
          <td><?php echo htmlspecialchars($h['payment_id']); ?></td>
          <td><?php echo htmlspecialchars($h['name_en']); ?></td>
          <td><?php echo htmlspecialchars($h['appointment_date']); ?></td>
          <td><?php echo htmlspecialchars($h['appointment_time']); ?></td>
          <td><?php echo number_format($h['amount']); ?> VND</td>
          <td>
            <?php if ($h['method'] === 'cash'): ?>
              <span class="badge bg-secondary">Tiền Mặt</span>
            <?php elseif ($h['method'] === 'bank_transfer'): ?>
              <span class="badge bg-info text-dark">Chuyển Khoản</span>
            <?php else: ?>
              <?php echo htmlspecialchars($h['method']); ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($h['status'] === 'paid'): ?>
              <span class="badge bg-success">Thanh Toán Thành Công</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($h['status']); ?> Bạn Chưa Thanh Toán !</span>
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
