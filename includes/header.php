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
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
    <a href="../public/index.php">Home</a>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="../public/register.php">Register</a>
        <a href="../public/login.php">Login</a>
    <?php else: ?>
        <a href="../public/services.php">Services</a>
        <a href="../public/my_appointments.php">My Appointments</a>
        <a href="../public/payment.php">Payment</a>
        <a href="../public/history.php">History</a>
        <a href="../public/logout.php">Logout</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            | <a href="../admin/manage_services.php">Manage Services</a>
            <a href="../admin/manage_appointments.php">Manage Appointments</a>
            <a href="../admin/manage_users.php">Manage Users</a>
            <a href="../admin/dashboard.php">Dashboard</a>
        <?php endif; ?>
    <?php endif; ?>
</nav>
