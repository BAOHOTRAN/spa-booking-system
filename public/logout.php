<?php
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        logoutUser();
        exit;
    } else {
        // Nếu chọn No thì quay về trang index
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Xác Nhận Đăng Xuất</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3 class="card-title mb-4">Xác Nhận Đăng Xuất</h3>
          <p class="mb-4">Bạn có chắc chắn muốn đăng xuất không ?</p>
          <form method="post" class="d-flex justify-content-center gap-3">
            <button type="submit" name="confirm" value="yes" class="btn btn-danger">Có</button>
            <button type="submit" name="confirm" value="no" class="btn btn-secondary">Không</button>
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
