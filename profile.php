<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: auth.php?tab=login");
    exit;
}

$username = $_SESSION['username'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['username']);
    $dob = $_POST['dob'];
    $email = trim($_POST['email']);

    if ($newName && $email) {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, dob=? WHERE id=?");
        $stmt->execute([$newName, $email, $dob, $user['id']]);
        $_SESSION['username'] = $newName; // refresh session
        header("Location: profile.php?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile - CoffeeHub</title>
<style>
body { font-family: Arial, sans-serif; background:#f5f0ea; margin:0; }
.container {
  max-width: 600px; margin:50px auto; background:white; padding:25px;
  border-radius:15px; box-shadow:0 6px 15px rgba(0,0,0,0.2);
}
h2 { color:#8b4513; }
input, button {
  width:100%; padding:12px; margin:10px 0;
  border:1px solid #ccc; border-radius:8px;
}
button {
  background:#8b4513; color:white; border:none; cursor:pointer;
}
button:hover { background:#5a2e0d; }
a { color:#8b4513; font-weight:bold; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="container">
  <h2>My Profile</h2>
  <?php if (isset($_GET['updated'])): ?>
    <p style="color:green;font-weight:bold;">Profile updated successfully ✅</p>
  <?php endif; ?>

  <form method="POST">
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Date of Birth</label>
    <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>">

    <button type="submit">Update Profile</button>
  </form>

  <hr>
  <h3>My Account</h3>
  <p><a href="cart.php">🛒 View Cart</a></p>
  <p><a href="orders.php">📦 My Orders</a></p>
</div>
</body>
</html>
