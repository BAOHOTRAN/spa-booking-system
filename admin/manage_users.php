<?php
include '../config/db.php';
include '../includes/auth.php';
checkAdmin(); // chỉ cho admin

$message = '';

// Xử lý khóa tài khoản
if (isset($_GET['lock_id'])) {
    $id = $_GET['lock_id'];
    $stmt = $pdo->prepare("UPDATE users SET is_active=0 WHERE user_id=? AND role='customer'");
    $stmt->execute([$id]);
    $message = "User $id locked.";
}

// Xử lý mở khóa tài khoản
if (isset($_GET['unlock_id'])) {
    $id = $_GET['unlock_id'];
    $stmt = $pdo->prepare("UPDATE users SET is_active=1 WHERE user_id=? AND role='customer'");
    $stmt->execute([$id]);
    $message = "User $id unlocked.";
}

// Lấy danh sách khách hàng
$stmt = $pdo->query("SELECT user_id, username, email, role, is_active FROM users ORDER BY user_id");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Quan Ly Khach Hang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary mb-3">← Quay lai trang Admin</a>
  <h2 class="mb-4 text-center">Quan Ly Khach Hang</h2>

  <!-- Hiển thị thông báo -->

  <!-- Bảng danh sách user -->
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Mã Khách hàng</th>
        <th>Tên Tài Khoản</th>
        <th>Email</th>
        <th>Vai trò</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?php echo htmlspecialchars($u['user_id']); ?></td>
          <td><?php echo htmlspecialchars($u['username']); ?></td>
          <td><?php echo htmlspecialchars($u['email']); ?></td>
          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="badge bg-dark">Admin</span>
            <?php else: ?>
              <span class="badge bg-primary">Khách Hàng</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($u['is_active']): ?>
              <span class="badge bg-success">Hoạt Động</span>
            <?php else: ?>
              <span class="badge bg-danger">Khóa</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($u['role'] === 'customer'): ?>
              <?php if ($u['is_active']): ?>
                <a href="?lock_id=<?php echo urlencode($u['user_id']); ?>" 
                   class="btn btn-outline-danger btn-sm"
                   onclick="return confirm('Có chắc chắn muốn chặn người này ?')">Khóa</a>
              <?php else: ?>
                <a href="?unlock_id=<?php echo urlencode($u['user_id']); ?>" 
                   class="btn btn-outline-success btn-sm"
                   onclick="return confirm('Mở khóa người này ?')">Mở Khóa</a>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">Đây là admin, không thể thay đổi</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
