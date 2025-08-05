<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO blood_requests (name, blood_group, phone, location) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $blood_group, $phone, $location);
    $stmt->execute();

    echo "<script>alert('Blood request submitted successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Blood</title>
    <link rel="stylesheet" href="../css/blood.css">
</head>
<body>
    <div class="container">
        <h2>Request Blood</h2>
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
            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>
