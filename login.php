<?php
session_start();
include 'includes/db.php';
//include 'includes/header.php';
// <?php include 'includes/footer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // Redirect based on role
            switch ($user['role']) {
                case 'patient':
                    header("Location: appointment/dashboard_patient.php");
                    break;
                case 'doctor':
                    header("Location: appointment/dashboard_doctor.php");
                    break;
                case 'pharmacist':
                    header("Location: pharmacy/admin_panel.php");
                    break;
                case 'admin':
                    header("Location: index.php"); // admin selects a panel
                    break;
                default:
                    echo "Invalid role.";
            }
            exit();
        }
    }
    echo "Invalid credentials.";
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="log_container">
        <h2 class="log_heading">Login</h2>
        <form method="POST" action="">
            <input class="log_input" type="text" name="username" placeholder="Username" required><br>
            <input class="log_input" type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <p class="log_register" style="color: green; text-align:center;">Don't have an account? <a href="register.php">Register Now</a></p>
    </div>
  </form>
</body>
</html>

