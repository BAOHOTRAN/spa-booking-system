<?php
include '../config/db.php';
include '../includes/auth.php';
checkCustomer(); // chỉ cho customer

$message = '';
$messageType = 'info';

// Lấy thông tin user hiện tại
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die("Không tìm thấy thông tin người dùng.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_email') {
        $newEmail = trim($_POST['email']);
        
        // Validation email
        if (empty($newEmail)) {
            $message = "Email không được để trống.";
            $messageType = 'danger';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $message = "Định dạng email không hợp lệ.";
            $messageType = 'danger';
        } else {
            // Kiểm tra email đã tồn tại chưa (trừ email hiện tại)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
            $stmt->execute([$newEmail, $_SESSION['user_id']]);
            $emailExists = $stmt->fetchColumn();
            
            if ($emailExists > 0) {
                $message = "Email này đã được sử dụng bởi tài khoản khác.";
                $messageType = 'danger';
            } else {
                // Cập nhật email
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
                $stmt->execute([$newEmail, $_SESSION['user_id']]);
                
                $user['email'] = $newEmail; // Cập nhật để hiển thị
                $message = "Đã cập nhật email thành công.";
                $messageType = 'success';
            }
        }
    }
    
    elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($currentPassword)) {
            $message = "Vui lòng nhập mật khẩu hiện tại.";
            $messageType = 'danger';
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $message = "Mật khẩu hiện tại không đúng.";
            $messageType = 'danger';
        } elseif (empty($newPassword)) {
            $message = "Vui lòng nhập mật khẩu mới.";
            $messageType = 'danger';
        } elseif (strlen($newPassword) < 6) {
            $message = "Mật khẩu mới phải có ít nhất 6 ký tự.";
            $messageType = 'danger';
        } elseif ($newPassword !== $confirmPassword) {
            $message = "Mật khẩu xác nhận không khớp.";
            $messageType = 'danger';
        } else {
            // Cập nhật mật khẩu
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
            
            $message = "Đã đổi mật khẩu thành công.";
            $messageType = 'success';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Thông Tin Cá Nhân</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary mb-4">
    <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
  </a>
  
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">
            <i class="fas fa-user-edit me-2"></i>Thông Tin Cá Nhân
          </h4>
        </div>
        <div class="card-body">
          
          <!-- Hiển thị thông báo -->
          <?php if ($message !== ''): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
              <?php echo htmlspecialchars($message); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <!-- Thông tin không thể sửa -->
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card border-secondary">
                <div class="card-body">
                  <h6 class="card-title text-muted">
                    <i class="fas fa-id-card me-2"></i>Thông Tin Hệ Thống
                  </h6>
                  <p class="mb-2"><strong>Mã khách hàng:</strong> <?php echo htmlspecialchars($user['user_id']); ?></p>
                  <p class="mb-2"><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                  <p class="mb-0"><strong>Vai trò:</strong> 
                    <span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
                  </p>
                  <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>Thông tin này không thể thay đổi
                  </small>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card border-info">
                <div class="card-body">
                  <h6 class="card-title text-muted">
                    <i class="fas fa-calendar me-2"></i>Thông Tin Tài Khoản
                  </h6>
                  <p class="mb-2"><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                  <p class="mb-0"><strong>Trạng thái:</strong> 
                    <?php if ($user['is_active']): ?>
                      <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Bị khóa</span>
                    <?php endif; ?>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form cập nhật email -->
          <div class="card border-warning mb-4">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">
                <i class="fas fa-envelope me-2"></i>Cập Nhật Email
              </h6>
            </div>
            <div class="card-body">
              <form method="post">
                <input type="hidden" name="action" value="update_email">
                <div class="mb-3">
                  <label class="form-label">Email hiện tại</label>
                  <input type="email" name="email" class="form-control" 
                         value="<?php echo htmlspecialchars($user['email']); ?>" required>
                  <div class="form-text">Nhập email mới để thay đổi</div>
                </div>
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-save me-2"></i>Cập nhật Email
                </button>
              </form>
            </div>
          </div>

          <!-- Form đổi mật khẩu -->
          <div class="card border-danger">
            <div class="card-header bg-danger text-white">
              <h6 class="mb-0">
                <i class="fas fa-key me-2"></i>Đổi Mật Khẩu
              </h6>
            </div>
            <div class="card-body">
              <form method="post">
                <input type="hidden" name="action" value="change_password">
                
                <div class="mb-3">
                  <label class="form-label">Mật khẩu hiện tại</label>
                  <input type="password" name="current_password" class="form-control" required>
                  <div class="form-text">Nhập mật khẩu hiện tại để xác nhận</div>
                </div>
                
                <div class="mb-3">
                  <label class="form-label">Mật khẩu mới</label>
                  <input type="password" name="new_password" class="form-control" 
                         minlength="6" required>
                  <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                </div>
                
                <div class="mb-3">
                  <label class="form-label">Xác nhận mật khẩu mới</label>
                  <input type="password" name="confirm_password" class="form-control" 
                         minlength="6" required>
                  <div class="form-text">Nhập lại mật khẩu mới để xác nhận</div>
                </div>
                
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-lock me-2"></i>Đổi Mật Khẩu
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>