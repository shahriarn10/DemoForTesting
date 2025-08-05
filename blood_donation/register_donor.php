<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $last_donation = $_POST['last_donation'];

    $stmt = $conn->prepare("INSERT INTO donors (name, blood_group, phone, location, last_donation) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $blood_group, $phone, $location, $last_donation);
    $stmt->execute();

    echo "<script>alert('Donor registered successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Donor</title>
    <link rel="stylesheet" href="../css/blood.css">
</head>
<body>
    <div class="container">
        <h2>Register Blood Donor</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <select name="blood_group" required>
                <option value="">Select Blood Group</option>
                <option>A+</option>
                <option>A-</option>
                <option>B+</option>
                <option>B-</option>
                <option>AB+</option>
                <option>AB-</option>
                <option>O+</option>
                <option>O-</option>
            </select>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="date" name="last_donation" placeholder="Last Donation Date">
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
