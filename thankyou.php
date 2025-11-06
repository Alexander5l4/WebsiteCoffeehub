<?php
session_start();
include 'db.php'; // your PDO connection

// Redirect if cart is empty or form not submitted properly
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}

// Validate required fields
$name = trim($_POST['name'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($name === '' || $address === '' || $phone === '') {
    header("Location: checkout.php");
    exit;
}

// Get user ID
$stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
$user_id = $user['id'] ?? 0;

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $id => $item) {
    $total += $item['price'] * $item['qty'];
}

// Save order
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Pending')");
$stmt->execute([$user_id, $total]);
$order_id = $pdo->lastInsertId();

// Save order items
$stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['cart'] as $id => $item) {
    $stmt->execute([$order_id, $id, $item['qty'], $item['price']]);
}

// Copy cart for display, then clear
$ordered_items = $_SESSION['cart'];
$_SESSION['cart'] = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You | CoffeeHub</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial, sans-serif; background:#f5f0ea; margin:0; padding:0; }
.container {
    max-width:700px;
    margin:80px auto;
    background:#fffaf5;
    padding:30px;
    border-radius:15px;
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
    text-align:center;
}
h2 { color:#8b4513; margin-bottom:20px; }
p { font-size:16px; color:#555; margin:10px 0; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#d2b48c; color:#fff; }
a {
    display:inline-block;
    margin-top:20px;
    padding:12px 25px;
    background:#8b4513;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}
a:hover { background:#5a2e0d; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>🎉 Thank You, <?= htmlspecialchars($name); ?>!</h2>
    <p>Your order has been placed successfully.</p>
    <p><strong>Order ID:</strong> <?= $order_id ?></p>
    <p>It will be delivered to: <b><?= nl2br(htmlspecialchars($address)); ?></b></p>
    <p>We will contact you at: <b><?= htmlspecialchars($phone); ?></b></p>

    <h3>Your Ordered Items:</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price (₹)</th>
            <th>Subtotal (₹)</th>
        </tr>
        <?php foreach ($ordered_items as $id => $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= number_format($item['price'] * $item['qty'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="3">Total</th>
            <th>₹<?= number_format($total,2) ?></th>
        </tr>
    </table>

    <a href="index.php">Back to Shop</a>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
