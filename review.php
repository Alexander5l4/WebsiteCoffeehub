<?php
session_start();
include 'db.php'; // provides $pdo

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $comment = trim($_POST['comment']);
    $rating = (int)$_POST['rating'];

    if ($name && $comment && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (name, comment, rating) VALUES (?, ?, ?)");
        $stmt->execute([$name, $comment, $rating]);
        // Optional: redirect to avoid resubmission
        header("Location: review.php");
        exit();
    }
}

// Fetch all reviews
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeeHub - Reviews</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
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

<!-- Review Form -->
<section class="reviews-section">
    <h2>Customer Reviews</h2>

    <form method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <textarea name="comment" placeholder="Your Review" rows="4" required></textarea>
        <label for="rating">Rating:</label>
        <select name="rating" required>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
        </select>
        <button type="submit">Submit</button>
    </form>

    <!-- Display all reviews -->
    <h3>All Reviews:</h3>
    <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <h4><?= htmlspecialchars($review['name']) ?></h4>
                    <p>"<?= htmlspecialchars($review['comment']) ?>"</p>
                    <p>Rating: <?= str_repeat('⭐', (int)$review['rating']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review!</p>
        <?php endif; ?>
    </div>
</section>

<footer class="footer flex-center">
    <p>&copy; <?= date('Y') ?> CoffeeHub. All rights reserved.</p>
</footer>

</body>
</html>
