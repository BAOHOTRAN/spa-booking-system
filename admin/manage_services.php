<?php
include '../config/db.php';
include '../includes/auth.php';
checkAdmin(); // chỉ cho admin

$message = '';

// Cấu hình thư mục lưu ảnh
$UPLOAD_FS_PATH = __DIR__ . '/../uploads/services/';   // đường dẫn trên filesystem
$UPLOAD_WEB_PATH = '../uploads/services/';             // đường dẫn để <img src> hiển thị trên web

// Tạo thư mục nếu chưa có
if (!is_dir($UPLOAD_FS_PATH)) {
    @mkdir($UPLOAD_FS_PATH, 0775, true);
}

// Hàm xử lý upload ảnh, trả về URL web nếu thành công, null nếu không
function saveUploadedImage(string $fieldName, string $prefixId, string $fsDir, string $webDir, string &$msg): ?string {
    if (empty($_FILES[$fieldName]['name'])) return null;

    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) {
        $msg = "Invalid image type. Allowed: JPG, PNG, WEBP.";
        return null;
    }
    if ($_FILES[$fieldName]['error'] !== 0) {
        $msg = "Image upload error (code: " . intval($_FILES[$fieldName]['error']) . ").";
        return null;
    }
    if ($_FILES[$fieldName]['size'] > 3 * 1024 * 1024) {
        $msg = "Image too large. Max 3MB.";
        return null;
    }

    $safePrefix = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $prefixId);
    $filename = $safePrefix . '_' . time() . '.' . $ext;
    $targetFs = rtrim($fsDir, '/\\') . '/' . $filename;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFs)) {
        $msg = "Image upload failed.";
        return null;
    }
    // Trả về đường dẫn web để lưu vào DB/hiển thị
    return rtrim($webDir, '/\\') . '/' . $filename;
}

// Xử lý thêm dịch vụ
if (isset($_POST['add'])) {
    $id        = trim($_POST['service_id']);
    $name      = trim($_POST['name_en']);
    $name_vi   = trim($_POST['name_vi_no_dau']);
    $price     = intval($_POST['price']);
    $duration  = intval($_POST['duration_minutes']);
    $desc      = trim($_POST['description']);

    // Upload ảnh (nếu có)
    $imgMsg = '';
    $imageUrl = saveUploadedImage('image', $id, $UPLOAD_FS_PATH, $UPLOAD_WEB_PATH, $imgMsg);

    if ($price <= 0) {
        $message = "Price must be > 0.";
    } elseif (!in_array($duration, [15,30,45,60,75,90])) {
        $message = "Invalid duration.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO services (service_id, name_en, name_vi_no_dau, price, duration_minutes, description, image_url)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([$id, $name, $name_vi, $price, $duration, $desc, $imageUrl]);
        $message = "Service added." . ($imgMsg ? " ($imgMsg)" : "");
    }
}

// Xử lý xóa dịch vụ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM services WHERE service_id=?");
    $stmt->execute([$id]);
    $message = "Service deleted.";
}

// Xử lý cập nhật dịch vụ
if (isset($_POST['update'])) {
    $id        = $_POST['service_id'];
    $name      = trim($_POST['name_en']);
    $name_vi   = trim($_POST['name_vi_no_dau']);
    $price     = intval($_POST['price']);
    $duration  = intval($_POST['duration_minutes']);
    $desc      = trim($_POST['description']);

    $imgMsg = '';
    $imageUrl = saveUploadedImage('image', $id, $UPLOAD_FS_PATH, $UPLOAD_WEB_PATH, $imgMsg);

    if ($price <= 0) {
        $message = "Price must be > 0.";
    } elseif (!in_array($duration, [15,30,45,60,75,90])) {
        $message = "Invalid duration.";
    } else {
        if ($imageUrl) {
            $stmt = $pdo->prepare("
                UPDATE services
                SET name_en=?, name_vi_no_dau=?, price=?, duration_minutes=?, description=?, image_url=?
                WHERE service_id=?
            ");
            $stmt->execute([$name, $name_vi, $price, $duration, $desc, $imageUrl, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE services
                SET name_en=?, name_vi_no_dau=?, price=?, duration_minutes=?, description=?
                WHERE service_id=?
            ");
            $stmt->execute([$name, $name_vi, $price, $duration, $desc, $id]);
        }
        $message = "Service updated." . ($imgMsg ? " ($imgMsg)" : "");
    }
}

// Lấy danh sách dịch vụ
$stmt = $pdo->query("SELECT * FROM services ORDER BY service_id");
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5 fade-in">
  <a href="index.php" class="btn btn-outline-secondary">← Quay lại trang Admin</a>
  <h2 class="mb-4 text-center">Quản Lý Dịch Vụ</h2>

  <?php if ($message !== ''): ?>
    <div class="alert alert-info text-center">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <!-- Add Service -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h4 class="card-title mb-3">Thêm Dịch Vụ</h4>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Mã Dịch Vụ</label>
          <input type="text" name="service_id" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tên Tiếng Anh</label>
          <input type="text" name="name_en" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tên Tiếng Việt (không dấu)</label>
          <input type="text" name="name_vi_no_dau" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Giá Tiền (VND)</label>
          <input type="number" name="price" class="form-control" min="1" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Thời gian phục vụ (phút)</label>
          <input type="number" name="duration_minutes" class="form-control" list="durations" required>
          <datalist id="durations">
            <option value="15"><option value="30"><option value="45">
            <option value="60"><option value="75"><option value="90">
          </datalist>
        </div>
        <div class="col-md-6">
          <label class="form-label">Mô tả</label>
          <input type="text" name="description" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Ảnh</label>
          <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
          <div class="form-text">Tối đa 3MB. Định dạng: JPG, PNG, WEBP.</div>
        </div>
        <div class="col-md-12">
          <button type="submit" name="add" class="btn btn-success w-100">Thêm Dịch Vụ</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Existing Services -->
  <h4 class="mb-3">Các Dịch Vụ Hiện Có</h4>
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Ảnh</th>
        <th>Mã Dịch Vụ</th>
        <th>Tên Dịch Vụ</th>
        <th>Giá Tiền</th>
        <th>Thời Lượng Phục Vụ</th>
        <th>Mô Tả</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($services as $s): ?>
      <tr>
        <form method="post" enctype="multipart/form-data">
          <td class="service-image-container">
            <?php if (!empty($s['image_url'])): ?>
              <img src="<?php echo htmlspecialchars($s['image_url']); ?>" alt="Service"
                   class="service-image img-thumbnail">
            <?php else: ?>
              <span class="text-muted">Chưa có hình ảnh</span>
            <?php endif; ?>
            <input type="file" name="image" class="form-control form-control-sm mt-2" accept=".jpg,.jpeg,.png,.webp">
          </td>
          <td>
            <?php echo htmlspecialchars($s['service_id']); ?>
            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($s['service_id']); ?>">
          </td>
          <td class="service-name-container">
            <input type="text" name="name_en" value="<?php echo htmlspecialchars($s['name_en']); ?>" class="form-control mb-1">
            <input type="text" name="name_vi_no_dau" value="<?php echo htmlspecialchars($s['name_vi_no_dau']); ?>" class="form-control form-control-sm">
          </td>
          <td><input type="number" name="price" value="<?php echo htmlspecialchars($s['price']); ?>" class="form-control"></td>
          <td><input type="number" name="duration_minutes" value="<?php echo htmlspecialchars($s['duration_minutes']); ?>" class="form-control"></td>
          <td><input type="text" name="description" value="<?php echo htmlspecialchars($s['description']); ?>" class="form-control"></td>
          <td>
            <a href="service_detail.php?service_id=<?php echo urlencode($s['service_id']); ?>"
               class="btn btn-outline-info btn-sm mb-2">Chi tiết</a>
            <button type="submit" name="update" class="btn btn-primary btn-sm mb-2">Cập nhật</button>
            <a href="?delete=<?php echo urlencode($s['service_id']); ?>" 
               class="btn btn-outline-danger btn-sm"
               onclick="return confirm('Delete this service?')">Xóa</a>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
