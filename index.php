<?php
session_start();
include 'includes/header.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="log_container">
        <h2 class="log_heading">Admin Panel</h2>
        <p>Welcome, <?php echo $_SESSION['user']['name']; ?>!</p>
        <ul>
            <li><a href="appointment/admin.php">Medical Appointment System</a></li>
            <li><a href="blood/blood_admin.php">Blood Donation System</a></li>
            <li><a href="pharmacy/admin_panel.php">Pharmacy Management System</a></li>
        </ul>
        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>