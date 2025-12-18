<?php
include '../config/db.php';
include '../includes/auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND is_active=1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        loginUser($user['user_id'], $user['role'], $user['username']);
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../public/index.php");
        }
        exit();
    } else {
        $message = "Tai khoan khong hop le. Moi nhap lai";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Đăng Nhập</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang chủ</a>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm fade-in">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">Đăng Nhập</h2>

          <!-- Hiển thị thông báo -->
          <?php if ($message !== ''): ?>
            <div class="alert alert-danger text-center">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>

          <!-- Form đăng nhập -->
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Tên Đăng Nhập</label>
              <input type="text" name="username" class="form-control" placeholder="Enter your username">
            </div>
            <div class="mb-3">
              <label class="form-label">Mật Khẩu</label>
              <input type="password" name="password" class="form-control" placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
          </form>

          <p class="mt-3 text-center">
            Chưa có tài khoản ? <a href="register.php">Đăng Ký Ở Đây </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
