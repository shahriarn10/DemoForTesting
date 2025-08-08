<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'pharmacist'])) {
    header("Location: ../login.php");
    exit();
}
?>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthcare_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Add Medicine
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = "images/";

    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $target_path = $upload_dir . basename($image_name);
    move_uploaded_file($image_tmp, $target_path);

    $stmt = $conn->prepare("INSERT INTO medicines (name, price, stock, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $stock, $image_name);

    $stmt->execute();
}

// Handle Update Medicine
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $upload_dir = "images/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    if (!empty($_FILES['image']['name'])) {
        // If a new image is uploaded
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $target_path = $upload_dir . basename($image_name);
        move_uploaded_file($image_tmp, $target_path);

        $stmt = $conn->prepare("UPDATE medicines SET name=?, price=?, stock=?, image=? WHERE id=?");
        $stmt->bind_param("sdisi", $name, $price, $stock, $image_name, $id);
    } else {
        // If image is not updated
        $stmt = $conn->prepare("UPDATE medicines SET name=?, price=?, stock=? WHERE id=?");
        $stmt->bind_param("sddi", $name, $price, $stock, $id);
    }

    $stmt->execute();
}

// Handle Delete Medicine
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM medicines ORDER BY id DESC");

// Fetch orders for admin view
$orders_result = $conn->query("
    SELECT o.*, m.name AS medicine_name, u.name AS user_name, u.email AS user_email
    FROM orders o
    JOIN medicines m ON o.medicine_id = m.id
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Pharmacy</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f4f4f4; }
        h2 { color: #333; }
        form { background: white; padding: 20px; margin-bottom: 30px; border-radius: 10px; width: 400px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        button { margin-top: 15px; padding: 10px 15px; background: #0077cc; color: white; border: none; cursor: pointer; }
        table { width: 100%; background: white; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #0077cc; color: white; }
        .edit-form { background: #fff7e6; padding: 10px; margin-top: 10px; }
        a { text-decoration: none; color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>Add New Medicine</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Medicine Name:</label>
    <input type="text" name="name" required>

    <label>Price (৳):</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock Quantity:</label>
    <input type="number" name="stock" required>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit" name="add">Add Medicine</button>
</form>

<h2>Existing Medicines</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price (৳)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this medicine?')">Delete</a>
            </td>
        </tr>

        <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']): ?>
            <tr>
                <td colspan="5">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <label>Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

                        <label>Price:</label>
                        <input type="number" name="price" value="<?= $row['price'] ?>" step="0.01" required>

                        <label>Stock:</label>
                        <input type="number" name="stock" value="<?= $row['stock'] ?>" required>

                        <label>Image:</label>
                        <input type="file" name="image" accept="image/*">
                        
                        <button type="submit" name="update">Update</button>
                    </form>
                </td>
            </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>

<h2>Order History</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Email</th>
        <th>Medicine</th>
        <th>Quantity</th>
        <th>Payment Method</th>
        <th>bKash Number</th>
        <th>Transaction ID</th>
        <th>Delivery Name</th>
        <th>Delivery Phone</th>
        <th>Delivery Address</th>
        <th>Delivery City</th>
        <th>Order Date</th>
    </tr>
    <?php while ($order = $orders_result->fetch_assoc()): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td><?= htmlspecialchars($order['user_email']) ?></td>
            <td><?= htmlspecialchars($order['medicine_name']) ?></td>
            <td><?= $order['quantity'] ?></td>
            <td><?= htmlspecialchars($order['payment_method']) ?></td>
            <td><?= htmlspecialchars($order['bkash_number'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['bkash_txn'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['delivery_name']) ?></td>
            <td><?= htmlspecialchars($order['delivery_phone']) ?></td>
            <td><?= htmlspecialchars($order['delivery_address']) ?></td>
            <td><?= htmlspecialchars($order['delivery_city']) ?></td>
            <td><?= $order['order_date'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>