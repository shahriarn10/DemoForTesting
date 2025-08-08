<?php
session_start();
?>
<nav>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="head_top">
        <span >Welcome to our Pharmacy </span> 
        <a href="../logout.php">Logout</a>
        </div>
        
    <?php else: ?>
        <a href="../login.php">Login</a>
    <?php endif; ?>
</nav>

<?php
include '../includes/db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Handle "Add to Cart"
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_to_cart'])) {
    $medicine_id = $_POST['medicine_id'];
    $quantity = 1;

    if (isset($_SESSION['cart'][$medicine_id])) {
        $_SESSION['cart'][$medicine_id] += $quantity;
    } else {
        $_SESSION['cart'][$medicine_id] = $quantity;
    }
}

// Handle search
$search = $_GET['search'] ?? '';
$search_sql = $search ? "WHERE name LIKE '%" . $conn->real_escape_string($search) . "%'" : "";

$medicines = $conn->query("SELECT * FROM medicines $search_sql");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy - HealthBridge</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background: #f4f4f4; }
        header { background: #23cba7; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; }
        nav a { margin: 0 15px; color: white; text-decoration: none; font-weight: bold; }
        .search-bar { text-align: center; margin: 20px; }
        .search-bar input[type="text"] { padding: 8px; width: 300px; }
        .search-bar input[type="submit"] { padding: 8px 12px; background: #0077cc; color: white; border: none; }
        .medicines { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .card { background: white; padding: 15px; width: 220px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 150px; object-fit: cover; }
        .card h3 { font-size: 18px; margin: 10px 0 5px; }
        .card p { margin: 5px 0; }
        .card form { margin-top: 10px; }
        .card button { background: #28a745; color: white; border: none; padding: 8px 12px; cursor: pointer; }
        .head_top {background: #0c94a0ff; color: white; border: none; padding: 8px 12px; cursor: pointer;}
    </style>
</head>
<body>

<header>
    <div class="logo">HealthBridge</div>
    <div class="search">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search medicines..." value="<?= htmlspecialchars($search) ?>">
            <input type="submit" value="Search">
        </form>
    </div>
    <nav>
        <a href="../appointment/dashboard_patient.php">Home </a>
        <a href="cart.php">Cart (<?= count($_SESSION['cart']) ?>)</a>
    </nav>
</header>

<main class="medicines">
    <?php while($row = $medicines->fetch_assoc()): ?>
        <div class="card">
            <?php $img = $row['image'] ? 'images/' . $row['image'] : 'images/default_medicine.jpg'; ?>
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p>Price: ৳<?= $row['price'] ?></p>
            <p>Stock: <?= $row['stock'] ?></p>
            <?php if ($row['stock'] > 0): ?>
                <form method="POST">
                    <input type="hidden" name="medicine_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="add_to_cart">Buy</button>
                </form>
            <?php else: ?>
                <p style="color: red;">Out of Stock</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</main>

</body>
</html>
