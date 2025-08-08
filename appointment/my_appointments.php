<?php
session_start();
include '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch appointments joined with doctors and user info
$sql = "SELECT a.appointment_id, a.appointment_date, a.status,
               d.name AS doctor_name, d.specialization,
               u.name AS user_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u ON a.user_id = u.user_id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link rel="stylesheet" href="../css/blood.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
        }

        h2 {
            color: #23cba7;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #23cba7;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background-color: #23cba7;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <h2>My Appointments</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['appointment_id']) ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td>Dr. <?= htmlspecialchars($row['doctor_name']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p style="text-align:center;">You have no appointments booked.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="../dashboard/user_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
