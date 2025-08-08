<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialization = $_POST['specialization'];

    $stmt1 = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
    $stmt1->bind_param("sssi", $name, $email, $phone, $user_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE doctors SET specialization=? WHERE user_id=?");
    $stmt2->bind_param("si", $specialization, $user_id);
    $stmt2->execute();

    $success = "Profile updated successfully!";
}

$stmt = $conn->prepare("SELECT u.*, d.specialization FROM users u JOIN doctors d ON u.id = d.user_id WHERE u.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor Profile</title>
    <link rel="stylesheet" href="../css/edit_profile.css">
</head>
<body>
    <div class="profile-container">
        <h2>Edit Profile</h2>

        <?php if (!empty($success)) { ?>
            <p class="success-msg"><?= $success ?></p>
        <?php } ?>

        <form method="POST" class="profile-form">
            <label for="name">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($data['phone']) ?>" required>

            <label for="specialization">Specialization</label>
            <input type="text" name="specialization" value="<?= htmlspecialchars($data['specialization']) ?>" required>

            <button type="submit">Update Profile</button>
        </form>
        <a href="dashboard_doctor.php" class="back-link">← Back to Dashboard</a>
    </div>
    
</body>

</html>
