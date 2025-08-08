<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['user']['id'];

// Fetch doctor details
$dstmt = $conn->prepare("SELECT * FROM doctors WHERE user_id = ?");
$dstmt->bind_param("i", $doctor_id);
$dstmt->execute();
$doctorDetails = $dstmt->get_result()->fetch_assoc();

// Fetch appointments
$apptStmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, u.phone AS patient_phone
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.doctor_id = ?
");
$apptStmt->bind_param("i", $doctor_id);
$apptStmt->execute();
$appointments = $apptStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<header>
    <div class="logo">HealthBridge</div>
    <nav>.
        <a class="edit-profile-btn" href="edit_profile.php">Edit Profile</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a class="edit-profile-btn" href="../logout.php">Logout</a>
        <?php else: ?>
            <a href="../login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/doctor_dashboard.css">
      <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f9f9;
            color: #333;
        }

        header {
            background-color: #23cba7;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        header nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <h2>Welcome Dr. <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h2>
        
        <p>
            Email: <?php echo htmlspecialchars($_SESSION['user']['email']); ?><br>
            Specialization: <?php echo htmlspecialchars($doctorDetails['specialization']); ?>
        </p>

        <h3>Your Appointments</h3>
        <table>
            <tr>
                <th>Patient</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
            <?php while ($row = $appointments->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                </tr>
            <?php } ?>
        </table>
        
    </div>
</body>
</html>

