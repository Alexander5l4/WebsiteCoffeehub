<?php
session_start();
include 'db.php'; // provides $pdo

// Fetch products and reviews
$products = $pdo->query('SELECT * FROM products')->fetchAll();
$reviews  = $pdo->query('SELECT * FROM reviews')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>CoffeeHub Shop</title>
<link rel="stylesheet" href="style.css?v=<?= time(); ?>" />
<style>
/* Navbar */
.navbar { display:flex; align-items:center; justify-content:space-between; padding:12px 20px; background:#8B4513; color:#fff; }
.nav-left { display:flex; align-items:center; gap:12px; }
.logo { font-weight:700; font-size:1.2rem; }
.nav-links { list-style:none; display:flex; gap:12px; align-items:center; margin:0; padding:0; }
.nav-links a { color:inherit; text-decoration:none; padding:6px 8px; }
.icon-btn { display:flex; align-items:center; gap:8px; color:#fff; text-decoration:none; padding:6px; border-radius:6px; }
.icon-svg { width:28px; height:28px; fill:#fff; display:block; }
.nav-user { position:relative; display:inline-block; }
.nav-user .avatar { width:36px; height:36px; border-radius:50%; background:#6f4e37; display:inline-flex; align-items:center; justify-content:center; color:#fff; font-weight:700; }
.nav-user:hover .dropdown { opacity:1; visibility:visible; transform:translateY(0); }
.dropdown {
    position:absolute; right:0; top:46px; min-width:150px; background:#fff; color:#000; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.15);
    opacity:0; visibility:hidden; transform:translateY(-6px); transition:all .18s ease;
}
.dropdown a { display:block; padding:10px 12px; color:#111; text-decoration:none; }
.dropdown a:hover { background:#f5f5f5; }
.nav-admin { display:flex; align-items:center; gap:8px; margin-right:6px; }
</style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="nav-left">
        <?php if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin.php" class="icon-btn nav-admin" title="Admin Dashboard">
                <svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a2 2 0 0 1 2 2v1h4a1 1 0 0 1 1 1v3h-2V6h-3v2h-2V4a2 2 0 0 1 2-2zM4 10h16v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-9zm3 3v5h2v-5H7zm4 0v5h2v-5h-2z"/></svg>
            </a>
        <?php endif; ?>
        <div class="logo">CoffeeHub</div>
    </div>

    <ul class="nav-links">
         <li><a href="admin.php">ADMIN</a></li>
        <li><a href="index.php">Home</a></li>
<li><a href="about.php">About Us</a></li>
<li><a href="products.php">Products</a></li>
<li><a href="review.php">Reviews</a></li>
<li><a href="contact.php">Contact</a></li>
<li><a href="cart.php">Cart</a></li>

        <?php if (isset($_SESSION['username'])): ?>
            <li class="nav-user" style="margin-left:12px;">
                <a href="user.php" class="icon-btn">
                    <span class="avatar"><?= strtoupper(substr(htmlspecialchars($_SESSION['username']),0,1)) ?></span>
                </a>
                <div class="dropdown">
                    <a href="profile.php">Profile</a>
                    <a href="orders.php">Orders</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="auth.php">Login / Sign Up</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Home Section -->
<section id="home" class="home-section flex-center">
    <div class="home-text">
        <h1>Welcome to CoffeeHub</h1>
        <p>Discover the finest coffee blends from around the world.</p>
        <p>Freshly brewed, just for you.</p>
        <p>Experience the aroma and taste of premium coffee.</p>
        <p>Your perfect coffee moment starts here.</p>
        <p>Shop now and enjoy exclusive offers!</p>
    </div>
    <div class="home-image">
        <img src="/Coffeehub/images/coffeehub_home.jpg" alt="Coffee" />
    </div>
</section>

<!-- About, Products, Reviews, Contact Sections (same as before) -->
<section id="about" class="about-section flex-center">
    <div class="about-video">
        <video width="600" controls autoplay muted>
            <source src="/Coffeehub/images/coffee_making.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="about-text">
        <h2>About CoffeeHub</h2>
        <p>CoffeeHub is dedicated to bringing you the best coffee experience possible. We source our beans from sustainable farms around the globe...</p>
    </div>
</section>

<section id="products" class="products-section">
    <h2>Our Coffee Products</h2>
    <div class="products-container flex-center">
        <?php foreach ($products as $product): ?>
            <?php $img = strtolower(str_replace(' ', '', $product['name'])) . '.jpg'; ?>
            <div class="product-card">
                <img src="images/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p class="price">₹<?= number_format($product['price'], 2) ?></p>
                <form method="POST" action="cart.php">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                    <input type="hidden" name="price" value="<?= $product['price'] ?>">
                    <button type="submit" name="add">Add to Cart</button>
                </form>
                <a href="products.php?id=<?= $product['id'] ?>" class="btn-details">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="reviews" class="reviews-section">
    <h2>Customer Reviews</h2>
    <div class="reviews-container flex-center">
        <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <h4><?= htmlspecialchars($review['name']) ?></h4>
                <p>"<?= htmlspecialchars($review['comment']) ?>"</p>
                <p>Rating: <?= str_repeat('⭐', (int)$review['rating']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact" class="contact-section flex-center">
    <h2>Contact Us</h2>
    <form action="contact_submit.php" method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required />
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<footer class="footer flex-center">
    <p>&copy; <?= date('Y') ?> CoffeeHub. All rights reserved.</p>
</footer>

</body>
</html>
