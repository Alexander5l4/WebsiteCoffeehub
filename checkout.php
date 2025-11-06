<?php
session_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to products page if cart is empty or not set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | CoffeeHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container checkout-container">
    <h2 class="checkout-title">Checkout</h2>
    <form class="checkout-form" method="POST" action="thankyou.php">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <button type="submit" class="checkout-btn">Place Order</button>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
