<?php
include '../includes/auth.php';
checkAdmin();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
  <!-- Sidebar -->
  <div class="admin-sidebar text-white p-3 slide-in-left">
    <h4 class="mb-4">Admin Menu</h4>
    <a href="../public/login_info.php" class="btn btn-outline-light w-100 mb-2">Thông tin đăng nhập</a>
    <a href="../public/LabThucHanh/" class="btn btn-outline-light w-100 mb-2">Thực hành Web</a>
    <a href="manage_services.php" class="btn btn-outline-light w-100 mb-2">Quản Lý Dịch Vụ</a>
    <a href="manage_appointments.php" class="btn btn-outline-light w-100 mb-2">Quản Lý Lịch Hẹn</a>
    <a href="manage_users.php" class="btn btn-outline-light w-100 mb-2">Quản Lý Khách Hàng</a>
    <a href="dashboard.php" class="btn btn-outline-light w-100 mb-2">Thống Kê</a>
    <a href="../public/logout.php" class="btn btn-danger w-100">Đăng Xuất</a>
  </div>

  <!-- Content -->
<div class="flex-grow-1 p-4 d-flex justify-content-center">
  <div class="admin-content fade-in">
    <h2 class="text-center">
        Chào Khách Hàng <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
    </h2>
    <p class="fs-5 text-center">
      Đây là khu vực quản trị của hệ thống <strong>SPA Mini</strong>.
      Với vai trò admin, bạn có thể:
    </p>
    <ul class="fs-5">
      <li><strong>Quản lý dịch vụ:</strong> thêm, sửa, xóa các dịch vụ spa.</li>
      <li><strong>Quản lý lịch hẹn:</strong> xem và điều chỉnh các cuộc hẹn của khách hàng.</li>
      <li><strong>Quản lý người dùng:</strong> kiểm soát  tài khoản khách hàng </li>
      <li><strong>Xem báo cáo:</strong> theo dõi doanh thu, lịch sử giao dịch và hiệu quả hoạt động.</li>
    </ul>
    <p class="fs-5">
      Hãy sử dụng menu bên trái để truy cập nhanh các chức năng quản trị.
    </p>
  </div>
</div>
</div>
</body>
</html>
