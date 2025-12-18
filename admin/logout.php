<?php
session_start();
session_unset();
session_destroy();

// Sau khi logout, quay về trang giới thiệu cho khách
header("Location: ../public/index.php");
exit;
