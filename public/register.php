<?php
include '../config/db.php';
include '../includes/auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username === '' || $email === '' || $password === '') {
        $message = "Please fill all fields.";
    } else {
        // Kiểm tra trùng username/email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $message = "Username or email already exists.";
        } else {
            // Tạo user_id tự động (KHxx)
            $stmt = $pdo->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
            $last = $stmt->fetch();
            $nextId = 'KH01';
            if ($last) {
                $num = intval(substr($last['user_id'], 2)) + 1;
                $nextId = 'KH' . str_pad($num, 2, '0', STR_PAD_LEFT);
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (user_id, username, email, password_hash, role, is_active) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$nextId, $username, $email, $hash, 'customer', 1]);

            $message = "Register successful. You can login now.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register</title>
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
          <h2 class="card-title text-center mb-4">Đăng Ký</h2>

          <!-- Hiển thị thông báo -->
          <?php if ($message !== ''): ?>
            <div class="alert alert-info text-center">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>

          <!-- Form đăng ký -->
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Tên Đăng Nhập</label>
              <input type="text" name="username" class="form-control" placeholder="Enter your username">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter your email">
            </div>
            <div class="mb-3">
              <label class="form-label">Mật Khẩu</label>
              <input type="password" name="password" class="form-control" placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
          </form>

          <p class="mt-3 text-center">
            Đã có sẵn tài khoản rồi sao ? <a href="login.php">Đăng nhập ở đây</a>
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

