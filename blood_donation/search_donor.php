<?php
include '../includes/db.php';

$donors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'];

    $stmt = $conn->prepare("SELECT * FROM donors WHERE blood_group = ?");
    $stmt->bind_param("s", $blood_group);
    $stmt->execute();
    $result = $stmt->get_result();

    $donors = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Donor</title>
    <link rel="stylesheet" href="../css/blood.css">
</head>
<body>
    <div class="form-container">
        <h2>Search Donor</h2>
        <form method="POST">
            <input type="text" name="blood_group" placeholder="Enter Blood Group (e.g., A+)" required><br>
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (!empty($donors)) : ?>
        <div class="result-container">
            <h3>Matching Donors:</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Blood Group</th>
                    <th>Contact</th>
                    <th>Location</th>
                </tr>
                <?php foreach ($donors as $donor): ?>
                    <tr>
                        <td><?= htmlspecialchars($donor['name']) ?></td>
                        <td><?= htmlspecialchars($donor['blood_group']) ?></td>
                        <td><?= htmlspecialchars($donor['contact']) ?></td>
                        <td><?= htmlspecialchars($donor['location']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
