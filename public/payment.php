<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer

$message = '';

// Lấy danh sách lịch hẹn của user chưa thanh toán
$stmt = $pdo->prepare("SELECT a.appointment_id, a.appointment_date, a.appointment_time, s.name_en, s.price
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.user_id=? 
    AND a.status IN ('pending','confirmed')
    AND a.appointment_id NOT IN (SELECT appointment_id FROM payments WHERE status='paid')");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $amount = intval($_POST['amount']);
    $method = $_POST['method'];

    // Kiểm tra hợp lệ
    if ($amount <= 0) {
        $message = "Amount must be > 0.";
    } elseif (!in_array($method, ['cash','bank_transfer'])) {
        $message = "Invalid payment method.";
    } else {
        // Kiểm tra appointment thuộc về user
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id=? AND user_id=?");
        $stmt->execute([$appointment_id, $_SESSION['user_id']]);
        $appt = $stmt->fetch();

        if (!$appt) {
            $message = "Appointment not found.";
        } else {
            // Tạo payment_id tự động (MaHDxx)
            $stmt = $pdo->query("SELECT payment_id FROM payments ORDER BY payment_id DESC LIMIT 1");
            $last = $stmt->fetch();
            $nextId = 'MaHD01';
            if ($last) {
                $num = intval(substr($last['payment_id'], 5)) + 1;
                $nextId = 'MaHD' . str_pad($num, 2, '0', STR_PAD_LEFT);
            }

            $stmt = $pdo->prepare("INSERT INTO payments (payment_id, appointment_id, amount, method, status) VALUES (?,?,?,?,?)");
            $stmt->execute([$nextId, $appointment_id, $amount, $method, 'paid']);

            $message = "Payment successful with ID $nextId.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Thanh Toan</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ</a>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">Thanh Toán</h2>

          <!-- Hiển thị thông báo -->
          <?php if ($message !== ''): ?>
            <div class="alert alert-info text-center">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>

          <!-- Form thanh toán -->
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Lịch Hẹn</label>
              <select name="appointment_id" class="form-select">
                <?php foreach ($appointments as $a): ?>
                  <option value="<?php echo htmlspecialchars($a['appointment_id']); ?>">
                    <?php echo htmlspecialchars($a['appointment_id']); ?> - 
                    <?php echo htmlspecialchars($a['name_en']); ?> - 
                    <?php echo htmlspecialchars($a['appointment_date']); ?> <?php echo htmlspecialchars($a['appointment_time']); ?> - 
                    Price: <?php echo number_format($a['price']); ?> VND
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Chọn lịch hẹn cần thanh toán.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Số Tiền</label>
              <input type="number" name="amount" class="form-control" placeholder="Enter amount">
              <div class="form-text">Số tiền cần thanh toán (VND).</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Phương Thức</label>
              <select name="method" class="form-select">
                <option value="cash">Tiền Mặt</option>
                <option value="bank_transfer">Bank Chuyển Khoản</option>
              </select>
              <div class="form-text">Chọn phương thức thanh toán.</div>
            </div>

            <button type="submit" class="btn btn-success w-100">Thanh Toán</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

