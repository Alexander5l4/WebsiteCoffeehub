<?php
session_start();
include 'db.php'; // provides $pdo

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        $success = "Thank you! Your message has been sent successfully.";
    } else {
        $error = "Please fill out all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeeHub - Contact</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <style>
        /* Modern Contact Form */
        .contact-section { max-width: 900px; margin: 50px auto; padding: 20px; background: #fdf6f0; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .contact-section h2 { text-align: center; margin-bottom: 30px; color: #6f4e37; }
        .contact-form { display: flex; flex-direction: column; gap: 15px; }
        .contact-form input, .contact-form textarea, .contact-form select, .contact-form button { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        .contact-form button { background: #8B4513; color: #fff; border: none; cursor: pointer; transition: background 0.3s; }
        .contact-form button:hover { background: #6f4e37; }
        .message { text-align: center; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-left">
        <div class="logo">CoffeeHub</div>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="review.php">Reviews</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="cart.php">🛒 Cart</a></li>
    </ul>
</nav>

<!-- Contact Section -->
<section class="contact-section">
    <h2>Contact Us</h2>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" rows="6" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<footer class="footer flex-center">
    <p>&copy; <?= date('Y') ?> CoffeeHub. All rights reserved.</p>
</footer>

</body>
</html>
