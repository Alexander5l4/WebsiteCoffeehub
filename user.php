<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
include 'db.php'; // provides $pdo

// fetch user profile
$stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE email = ?");
$stmt->execute([$_SESSION['email'] ?? '']);
$user = $stmt->fetch();

// fetch user orders (assumes orders table has user_email — adapt to your schema)
$orders = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_email = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['email'] ?? '']);
    $orders = $stmt->fetchAll();
} catch (Exception $e) {
    // orders table might not exist yet — we'll handle gracefully
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Your Account - CoffeeHub</title>
<link rel="stylesheet" href="style.css?v=<?= time(); ?>" />
<style>
.container{max-width:1000px;margin:32px auto;padding:16px;}
.card{background:#fff5e6;padding:18px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.05);margin-bottom:16px;}
</style>
</head>
<body>
<nav style="background:#8B4513;color:#fff;padding:12px 18px;">
    <a href="index.php" style="color:#fff;text-decoration:none;font-weight:700;">← Back to Shop</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? $_SESSION['email'] ?? '') ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? $_SESSION['role'] ?? 'user') ?></p>
    </div>

    <div class="card">
        <h3>Your Orders</h3>
        <?php if (empty($orders)): ?>
            <p>You have no orders yet. Your orders will appear here once you place them.</p>
            <p><a href="products.php" style="color:#6f4e37;font-weight:600;">Browse products</a></p>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach($orders as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['id']) ?></td>
                        <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
                        <td>₹<?= number_format($o['total'] ?? 0, 2) ?></td>
                        <td><?= htmlspecialchars($o['status'] ?? 'Pending') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
