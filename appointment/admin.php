<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch appointments
$sql = "SELECT a.id, a.date, a.time, u.name as patient_name, d.name as doctor_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        JOIN doctors d ON a.doctor_id = d.id 
        ORDER BY a.date ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Appointments</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="log_container">
        <h2 class="log_heading">Medical Appointment - Admin Panel</h2>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Patient</th>
                <th>Doctor</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['patient_name'] ?></td>
                    <td><?= $row['doctor_name'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <a href="../index.php">Back to Admin Panel</a>
    </div>
</body>
</html>
