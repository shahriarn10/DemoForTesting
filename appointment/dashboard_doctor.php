<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/header.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['user']['id'];
$sql = "SELECT a.id, a.date, a.time, u.name as patient_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        WHERE a.doctor_id = $doctor_id
        ORDER BY a.date ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="log_container">
        <h2 class="log_heading">Welcome Dr. <?= $_SESSION['user']['name'] ?></h2>
        <h3>Scheduled Appointments</h3>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Patient</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['patient_name'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <a href="../logout.php">Logout</a>
    </div>
</body>
</html>


<?php include '../includes/footer.php'; ?>
