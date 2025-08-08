<?php
include '../includes/db.php';
include '../includes/auth.php';

$user = $_SESSION['user'];

// Fetch doctors and their specialization
$sql = "SELECT d.doctor_id, u.name, d.specialization
        FROM doctors d
        JOIN users u ON d.user_id = u.id";
$result = $conn->query($sql);

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $user['id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $reason);

    if ($stmt->execute()) {
        $message = "<p style='color:green;'>✅ Appointment booked successfully!</p>";
    } else {
        $message = "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Appointment</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #eaf6ff;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background: #1abc9c;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      color: white;
      font-size: 18px;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: bold;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #1abc9c;
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #333;
    }

    select, input[type="date"], input[type="text"], button, input[type="search"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button {
      background: #1abc9c;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #149a82;
    }

    .appointments-link {
      float: right;
      margin-top: -30px;
    }

    .appointments-link a {
      color: #1abc9c;
      font-weight: bold;
      text-decoration: none;
    }

    .appointments-link a:hover {
      text-decoration: underline;
    }

    .home_btn {
      display: inline-block;
      padding: 5px 12px;
      color: white;
      border-radius: 5px;
      transition: background 0.3s ease;
    }

    .home_btn:hover {
      background-color: #1ea98d;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div><strong>HealthBridge</strong></div>
    <div>
      <a class="home_btn" href="../appointment/dashboard_patient.php">Home</a>
      <a class="home_btn" href="../logout.php">Logout</a>
    </div>
  </div>

  <div class="container">
    <div class="appointments-link">
      <a href="../appointment/my_appointments.php">📅 My Appointments</a>
    </div>

    <h2>Book Appointment</h2>

    <?= $message ?>

    <!-- Searchable input -->
    <input type="search" id="doctorSearch" placeholder="Search doctor by name or specialization" onkeyup="filterDoctors()">

    <form method="POST" action="">
      <label for="doctor">Select Doctor:</label>
      <select name="doctor_id" id="doctor" required>
        <option value="">-- Choose a Doctor --</option>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <option value="<?= $row['doctor_id'] ?>">
            Dr. <?= htmlspecialchars($row['name']) ?> - <?= htmlspecialchars($row['specialization']) ?>
          </option>
        <?php } ?>
      </select>

      <label for="date">Appointment Date:</label>
      <input type="date" name="appointment_date" id="date" required>

      <label for="reason">Reason:</label>
      <input type="text" name="reason" placeholder="E.g., General Checkup, Consultation" required>

      <button type="submit" name="submit">Book Appointment</button>
    </form>
  </div>

  <script>
    function filterDoctors() {
      let input = document.getElementById("doctorSearch").value.toLowerCase();
      let select = document.getElementById("doctor");
      let options = select.options;

      for (let i = 0; i < options.length; i++) {
        let text = options[i].text.toLowerCase();
        options[i].style.display = text.includes(input) ? "" : "none";
      }
    }
  </script>
</body>
</html>
