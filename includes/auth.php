<?php
// includes/auth.php
// Quản lý session, phân quyền, kiểm tra đăng nhập

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hàm kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Hàm kiểm tra quyền admin
function checkAdmin() {
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        echo "Access denied. Admin only.";
        exit();
    }
}

// Hàm kiểm tra quyền customer
function checkCustomer() {
    checkLogin();
    if ($_SESSION['role'] !== 'customer') {
        echo "Access denied. Customer only.";
        exit();
    }
}

// Hàm đăng nhập (sau khi verify mật khẩu thành công)
function loginUser($user_id, $role, $user_name) {
    session_regenerate_id(delete_old_session: true); // chống session fixation
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role']    = $role;
    $_SESSION['username'] = $user_name;
}

// Hàm đăng xuất
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
