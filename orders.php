<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: auth.php?tab=login");
    exit;
}

$username = $_SESSION['username'];

// Fetch user ID
$stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit;
}

$user_id = $user['id'];

// Fetch orders for this user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - CoffeeHub</title>
<style>
body {
  font-family: Arial, sans-serif;
  background:#f5f0ea;
  margin:0;
  padding:0;
}
.container {
  max-width: 700px;
  margin:50px auto;
  background:white;
  padding:30px;
  border-radius:15px;
  box-shadow:0 6px 15px rgba(0,0,0,0.2);
}
h2 {
  color:#8b4513;
  margin-bottom:20px;
}
.order {
  border:1px solid #ddd;
  border-radius:10px;
  padding:15px;
  margin-bottom:15px;
  background:#fffaf5;
}
.order strong {
  color:#8b4513;
}
.empty {
  text-align:center;
  padding:30px;
  font-size:18px;
  color:#555;
}
a {
  display:inline-block;
  margin-top:15px;
  padding:10px 20px;
  background:#8b4513;
  color:white;
  border-radius:8px;
  text-decoration:none;
}
a:hover {
  background:#5a2e0d;
}
</style>
</head>
<body>
<div class="container">
  <h2>📦 My Orders</h2>

  <?php if ($orders): ?>
    <?php foreach ($orders as $order): ?>
      <div class="order">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Total:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="empty">
      <p>😊 You haven’t placed any orders yet.</p>
      <p>Start shopping and enjoy our coffee collection ☕</p>
      <a href="index.php#products">Shop Now</a>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
