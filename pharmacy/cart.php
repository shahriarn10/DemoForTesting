<?php
session_start();
include '../includes/db.php';

// Handle Remove Item
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php"); // Refresh the page
    exit;
}

// Handle Reduce Item
if (isset($_GET['reduce'])) {
    $reduce_id = $_GET['reduce'];
    if (isset($_SESSION['cart'][$reduce_id])) {
        $_SESSION['cart'][$reduce_id]--;
        if ($_SESSION['cart'][$reduce_id] <= 0) {
            unset($_SESSION['cart'][$reduce_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty.</h2>";
    echo "<a href='search_medicine.php'>Back to Pharmacy</a>";
    exit;
}

// Dummy user_id (replace with session login logic)
$user = $_SESSION['user'];

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
    $delivery_name = $_SESSION['user']['name'];   // From session
    $delivery_phone = $_SESSION['user']['phone']; // From session
    $delivery_address = $_POST['delivery_address']; // From form
    $delivery_city = $_POST['delivery_city'];       // From form

    $payment_method = $_POST['payment_method'];
    $bkash_number = $_POST['bkash_number'] ?? null;
    $bkash_txn = $_POST['bkash_txn'] ?? null;

    // Validate required fields
    if ($payment_method === "bKash" && (empty($bkash_number) || empty($bkash_txn))) {
        echo "<p style='color:red;'>Please provide both bKash number and transaction ID.</p>";
        exit;
    }

    $user_id = $_SESSION['user']['id']; // ✅ FIXED: Get actual user ID

    $conn->begin_transaction();

    try {
        foreach ($medicines as $med) {
            if ($med['quantity'] > $med['stock']) {
                throw new Exception("Insufficient stock for {$med['name']}");
            }

            $stmt = $conn->prepare("INSERT INTO orders 
(user_id, medicine_id, quantity, payment_method, bkash_number, bkash_txn, order_date,
delivery_name, delivery_phone, delivery_address, delivery_city)
VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)");

            $stmt->bind_param(
                "iiisssssss",
                $user_id,
                $med['id'],
                $med['quantity'],
                $payment_method,
                $bkash_number,
                $bkash_txn,
                $delivery_name,
                $delivery_phone,
                $delivery_address,
                $delivery_city
            );


            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $med['quantity'], $med['id']);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['cart'] = [];
        echo "<h2>Order placed successfully using <strong>" . htmlspecialchars($payment_method) . "</strong>!</h2>";
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
        body {
            font-family: Arial;
            margin: 30px;
            background: #f4f4f4;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #0077cc;
            color: white;
        }

        h2 {
            color: #333;
        }

        .total {
            font-size: 20px;
            text-align: right;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }

        a {
            text-decoration: none;
            color: #0077cc;
        }
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
                <td>
                    <?= $med['quantity'] ?>
                    <a href="?reduce=<?= $med['id'] ?>" style="margin-left:10px; color:orange;">[-]</a>
                    <a href="?remove=<?= $med['id'] ?>" style="margin-left:10px; color:red;">[Remove]</a>
                </td>
                <td>৳<?= number_format($med['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

    <div class="total"><strong>Total: ৳<?= number_format($total, 2) ?></strong></div>

    <form method="POST">
        <h3>Select Payment Method:</h3>
        <label><input type="radio" name="payment_method" value="Cash on Delivery" required onchange="showPaymentFields()"> Cash on Delivery</label><br>
        <label><input type="radio" name="payment_method" value="bKash" onchange="showPaymentFields()"> bKash</label><br>

        <div id="bkashFields" style="display: none;">
            <label>bKash Number: <input type="text" name="bkash_number"></label><br>
            <label>Transaction ID: <input type="text" name="bkash_txn"></label><br><br>
        </div>
        <h3>Delivery Info:</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user']['phone']) ?></p>

        <h3>Delivery Address:</h3>
        <label>Address: <textarea name="delivery_address" required></textarea></label><br>
        <label>City: <input type="text" name="delivery_city" required></label><br><br>

        <button type="submit" name="confirm_order">Confirm Order</button>
    </form>

    <script>
        function showPaymentFields() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            document.getElementById('bkashFields').style.display = (method === 'bKash') ? 'block' : 'none';
        }
    </script>



    <p><a href="search_medicine.php">← Back to Search</a></p>

</body>

</html>