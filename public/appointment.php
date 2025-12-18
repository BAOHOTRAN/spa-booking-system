<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer

$message = '';
$service_id = $_GET['service_id'] ?? null;

// Lấy thông tin dịch vụ
if ($service_id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id=?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
    if (!$service) {
        die("Dịch vụ không tồn tại.");
    }
} else {
    die("Không thấy dịch vụ mà quý khách đang tìm kiểm.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];

    $nowDate = date('Y-m-d');
    $nowTime = date('H:i'); 

    $chosenDateTime = strtotime("$date $time");
    $currentDateTime = strtotime("$nowDate $nowTime");

    $workStart = strtotime("$date 08:00");
    $workEnd   = strtotime("$date 21:00");

    if ($chosenDateTime < $currentDateTime) {
        $message = "Quý khách không được đặt lịch hẹn với thời gian trong quá khứ.";
    } elseif ($chosenDateTime < $workStart || $chosenDateTime > $workEnd) {
        $message = "Xin lỗi quý khách, chúng tôi chỉ phục vụ từ 8h sáng đến 21h tối.";
    } else {
        // Kiểm tra end_time không vượt quá 21:00
        $endTime = $chosenDateTime + $service['duration_minutes']*60;
        if ($endTime > $workEnd) {
            $message = "Xin lỗi quý khách, giờ đặt lịch của quý khách vượt quá thời gian nghỉ làm.";
        } else {
            // Kiểm tra trùng slot và chồng chéo thời gian
            $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM appointments 
                WHERE appointment_date=? AND appointment_time=? 
                AND status IN ('pending','confirmed')");
            $stmt->execute([$date, $time]);
            $cnt = $stmt->fetch()['cnt'];
            
            if ($cnt > 0) {
                $message = "Khung giờ này đã có khách hàng khác đặt, mời quý khách chọn giờ khác.";
            } else {
                // Kiểm tra chồng chéo thời gian với các lịch hẹn khác
                $newStartTime = strtotime("$date $time");
                $newEndTime = $newStartTime + ($service['duration_minutes'] * 60);
                
                $stmt = $pdo->prepare("
                    SELECT a.*, s.duration_minutes 
                    FROM appointments a 
                    JOIN services s ON a.service_id = s.service_id 
                    WHERE a.appointment_date = ? 
                    AND a.status IN ('pending','confirmed')
                ");
                $stmt->execute([$date]);
                $existingAppts = $stmt->fetchAll();
                
                $hasOverlap = false;
                foreach ($existingAppts as $existing) {
                    $existingStart = strtotime($existing['appointment_date'] . ' ' . $existing['appointment_time']);
                    $existingEnd = $existingStart + ($existing['duration_minutes'] * 60);
                    
                    // Kiểm tra chồng chéo: (start1 < end2) && (start2 < end1)
                    if (($newStartTime < $existingEnd) && ($existingStart < $newEndTime)) {
                        $hasOverlap = true;
                        break;
                    }
                }
                
                if ($hasOverlap) {
                    $message = "Xin lỗi quý khách, thời gian này bị chồng chéo với lịch hẹn khác. Mời quý khách chọn giờ khác.";
                } else {
                // Kiểm tra số lượng lịch trong ngày
                $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM appointments 
                    WHERE appointment_date=? AND status IN ('pending','confirmed')");
                $stmt->execute([$date]);
                $cntDay = $stmt->fetch()['cnt'];
                if ($cntDay >= 10) {
                    $message = "Xin lỗi quý khách, tiệm chúng tôi hôm nay đã nhận đủ số lượng đặt lịch, quý khách thông cảm chọn ngày khác.";
                } else {
                    // Tạo appointment_id tự động (LHxx)
                    $stmt = $pdo->query("SELECT appointment_id FROM appointments ORDER BY appointment_id DESC LIMIT 1");
                    $last = $stmt->fetch();
                    $nextId = 'LH01';
                    if ($last) {
                        $num = intval(substr($last['appointment_id'], 2)) + 1;
                        $nextId = 'LH' . str_pad($num, 2, '0', STR_PAD_LEFT);
                    }

                    $stmt = $pdo->prepare("INSERT INTO appointments 
                        (appointment_id, user_id, service_id, appointment_date, appointment_time, status) 
                        VALUES (?,?,?,?,?,?)");
                    $stmt->execute([$nextId, $_SESSION['user_id'], $service_id, $date, $time, 'pending']);

                    $message = "Đã đặt lịch hẹn, mã lịch hẹn của quý khách là $nextId.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Đặt Lịch Hẹn</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ </a>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">
            Dat lich hen cho <?php echo htmlspecialchars($service['name_en']); ?>
          </h2>

          <!-- Thông tin dịch vụ -->
          <ul class="list-group mb-4">
            <li class="list-group-item">
              <strong>Giá Tiền:</strong> <?php echo number_format($service['price']); ?> VND
            </li>
            <li class="list-group-item">
              <strong>Thời Lượng Phục Vụ:</strong> <?php echo $service['duration_minutes']; ?> Phút
            </li>
          </ul>

          <!-- Hiển thị thông báo -->
          <?php if ($message !== ''): ?>
            <div class="alert alert-info text-center">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>

          <!-- Form đặt lịch -->
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Ngày Đặt</label>
              <input type="date" name="date" class="form-control" 
                     min="<?php echo date('Y-m-d'); ?>">
              <div class="form-text">Chỉ đặt lịch từ hôm nay trở đi.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Giờ Đặt</label>
              <input type="time" name="time" class="form-control">
              <div class="form-text">Khung giờ phục vụ: 08:00 đến 21:00.</div>
            </div>

            <button type="submit" class="btn btn-success w-100">Đặt lịch</button>
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
