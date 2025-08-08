<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard | HealthBridge</title>
  <link rel="stylesheet" href="../blood_donation/css/blood.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f8ff;
      margin: 0;
      padding: 0;
    }

    header {
      background: #23cba7;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .dashboard {
      display: flex;
      justify-content: space-around;
      align-items: center;
      margin: 40px auto;
      max-width: 1000px;
      gap: 20px;
    }

    .feature {
      flex: 1;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 30px;
      transition: 0.3s;
      position: relative;
      cursor: pointer;
    }

    .feature:hover {
      transform: translateY(-10px);
      background: #e0f7f4;
      border: 2px solid #32d4cfff;
      /* border-radius: 20px 0 20px 0; */
    }

    .feature.active::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: #23cba7;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }

    .feature img {
      width: 60px;
      height: 60px;
      margin-bottom: 15px;
    }

    .register-donor {
      text-align: center;
      margin-top: 20px;
      font-size: 1.2rem;
    }

    .profile-box {
      text-align: center;
      margin-top: 40px;
    }

    .profile-box a {
      color: #23cba7;
      text-decoration: none;
      font-weight: bold;
    }

    footer {
      background-color: #23cba7;
      width: 100%;
      height: 50px;
      margin-top: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      
    }

  </style>
</head>
<body>

<header>
  <h1>Welcome to HealthBridge</h1>
  <p>A one-stop solution for your health.</p>
</header>

<div class="dashboard">
  <div class="feature" onclick="window.location.href='../appointment/book.php'">
    <img src="../images/appointment-icon.png" alt="Appointment">
    <h3>Book Appointment</h3>
  </div>

  <div class="feature" onclick="window.location.href='../blood_donation/request_blood.php'">
    <img src="../images/blood-icon.png" alt="Blood Donation">
    <h3>Request Blood</h3>
  </div>

  <div class="feature" onclick="window.location.href='../pharmacy/search_medicine.php'">
    <img src="../images/pharmacy-icon.png" alt="Pharmacy">
    <h3>Pharmacy</h3>
  </div>
</div>
<div class="register-donor">
    <p>Want to become a donor? <a href="../blood_donation/register_donor.php">Become a Donor</a></p>
</div>
<div class="profile-box">
  <p>Want to update your profile? <a href="../appointment/edit_profile.php">Click here to edit</a></p>
</div>

<footer>
  <p>&copy; 2025 HealthBridge. All Rights Reserved.</p>
</footer>

</body>
</html>