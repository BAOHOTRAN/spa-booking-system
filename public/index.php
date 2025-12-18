<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SPA Mini Website</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body class="d-flex">
<?php if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])): ?>
  <!-- Sidebar khi đã login -->
  <div class="sidebar d-flex flex-column p-3 slide-in-left">
    <h3 class="text-center mb-4">SPA Menu</h3>
    <p class="text-center mb-3">
      Chào mừng bạn đến với trang của chúng tôi, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></strong><br>
      (Vai Trò: <?php echo htmlspecialchars($_SESSION['role']); ?>)
    </p>
    <!-- Hai nút chuyển sang sidebar khi đã login -->
    <a href="login_info.php" class="btn btn-outline-light mb-2">Thông tin đăng nhập</a>
    <a href="LabThucHanh/" class="btn btn-outline-light mb-2">Thực hành Web</a>
    <a href="services.php" class="btn btn-outline-light mb-2">Danh Sách Dịch Vụ</a>
    <a href="my_appointments.php" class="btn btn-outline-light mb-2">Lịch Hẹn Của Quý Khách</a>
    <a href="profile.php" class="btn btn-outline-light mb-2">Thông Tin Cá Nhân</a>
    <a href="payment.php" class="btn btn-outline-light mb-2">Thanh Toán</a>
    <a href="history.php" class="btn btn-outline-light mb-2">Lịch Sử Đặt Lịch Và Thanh Toán</a>
    <a href="logout.php" class="btn btn-outline-danger mb-2">Đăng Xuất</a>
  </div>
<?php endif; ?>

<!-- Content -->
<div class="content fade-in">

  <!-- Topbar bên trái chỉ hiện khi chưa login -->
  <?php if (empty($_SESSION['user_id'])): ?>
    <div class="topbar-left">
      <a href="login_info.php" class="btn btn-info me-2">Thông tin đăng nhập</a>
      <a href="LabThucHanh/" class="btn btn-warning">Thực hành Web</a>
    </div>
  <?php endif; ?>

  <?php if (empty($_SESSION['user_id'])): ?>
    <!-- Topbar login/register khi chưa login -->
    <div class="topbar">
      <a href="login.php" class="btn btn-primary me-2">Đăng Nhập</a>
      <a href="register.php" class="btn btn-success">Đăng Ký</a>
    </div>
  <?php endif; ?>

  <!-- Hero Section -->
  <div class="spa-hero-card">
    <h1 class="spa-hero-title">Welcome to SPA Mini Website</h1>
    <p class="spa-hero-subtitle">🌸 <strong>Relax • Refresh • Renew</strong> 🌸</p>

    <div class="spa-highlight-box">
      Khám phá hành trình chăm sóc bản thân đẳng cấp – nơi mỗi dịch vụ không chỉ là một liệu trình,
      mà là một <strong>trải nghiệm nâng niu tinh thần và cơ thể</strong>.
    </div>

    <div class="spa-content-section">
      <p class="spa-description">
        Tại <strong>SPA Mini</strong>, chúng tôi tin rằng <em>sự thư giãn là nghệ thuật sống</em>.  
        Với đội ngũ chuyên viên tận tâm và dịch vụ đa dạng từ massage trị liệu, chăm sóc da, đến gói chăm sóc toàn diện,  
        bạn sẽ được đắm chìm trong không gian yên bình, tách biệt khỏi nhịp sống hối hả.
      </p>

      <p class="spa-motto">
        ✨ <strong>Hãy để mỗi phút giây tại SPA Mini trở thành khoảnh khắc tái tạo năng lượng, khơi nguồn cảm hứng và làm mới chính mình.</strong> ✨
      </p>
    </div>

    <?php if (!empty($_SESSION['user_id'])): ?>
      <div class="spa-cta-section">
        <a href="services.php" class="btn btn-lg btn-outline-primary me-3">Khám phá dịch vụ</a>
        <a href="appointment.php?service_id=DV01" class="btn btn-lg btn-success">Đặt lịch ngay</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
