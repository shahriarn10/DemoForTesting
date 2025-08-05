<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthcare_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty.</h2>";
    echo "<a href='search_medicine.php'>Back to Pharmacy</a>";
    exit;
}

// Dummy user_id (replace with session login logic)
$user_id = 1;

// Fetch medicine info
$cart = $_SESSION['cart'];
$medicine_ids = implode(',', array_keys($cart));
$result = $conn->query("SELECT * FROM medicines WHERE id IN ($medicine_ids)");

$medicines = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['quantity'] = $cart[$row['id']];
    $row['subtotal'] = $row['quantity'] * $row['price'];
    $total += $row['subtotal'];
    $medicines[] = $row;
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['confirm_order'])) {
    $conn->begin_transaction();

    try {
        foreach ($medicines as $med) {
            if ($med['quantity'] > $med['stock']) {
                throw new Exception("Insufficient stock for {$med['name']}");
            }

            // Insert into orders
            $stmt = $conn->prepare("INSERT INTO orders (user_id, medicine_id, quantity, order_date) VALUES (?, ?, ?, CURDATE())");
            $stmt->bind_param("iii", $user_id, $med['id'], $med['quantity']);
            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $med['quantity'], $med['id']);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['cart'] = [];
        echo "<h2>Order placed successfully!</h2>";
        echo "<a href='search_medicine.php'>Back to Pharmacy</a>";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "<h3>Error: " . $e->getMessage() . "</h3>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart - HealthBridge</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f4f4f4; }
        table { width: 100%; background: white; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
        th { background: #0077cc; color: white; }
        h2 { color: #333; }
        .total { font-size: 20px; text-align: right; margin-bottom: 20px; }
        button { padding: 10px 20px; background: green; color: white; border: none; cursor: pointer; }
        a { text-decoration: none; color: #0077cc; }
    </style>
</head>
<body>

<h2>Your Cart</h2>
<table>
    <tr>
        <th>Medicine Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($medicines as $med): ?>
        <tr>
            <td><?= htmlspecialchars($med['name']) ?></td>
            <td>৳<?= $med['price'] ?></td>
            <td><?= $med['quantity'] ?></td>
            <td>৳<?= number_format($med['subtotal'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<div class="total"><strong>Total: ৳<?= number_format($total, 2) ?></strong></div>

<form method="POST">
    <button type="submit" name="confirm_order">Confirm Order</button>
</form>

<p><a href="search_medicine.php">← Back to Search</a></p>

</body>
</html>
