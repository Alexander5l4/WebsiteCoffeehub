<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = floatval($_POST['price']);

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $name,
                'price' => $price,
                'qty' => 1,
                'image' => $_POST['image'] ?? 'placeholder.png' // optional image
            ];
        }
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $qty = intval($_POST['qty']);
        if ($qty > 0 && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        } elseif ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }

    if (isset($_POST['remove'])) {
        $id = $_POST['id'];
        unset($_SESSION['cart'][$id]);
    }

    if (isset($_POST['clear'])) {
        $_SESSION['cart'] = [];
    }

    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title> CoffeeHub</title>
<style>
    /* Reset & base */
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #fef6f0;
        color: #4b3b2b;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    a {
        text-decoration: none;
        color: #6b4f4f;
        transition: color 0.3s ease;
    }
    a:hover {
        color: #a67c7c;
    }

    /* Header */
    header {
        background: #6b4f4f;
        color: #fff;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 3px 8px rgba(107,79,79,0.4);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    header .logo {
        font-size: 1.8rem;
        font-weight: 700;
        letter-spacing: 2px;
        user-select: none;
    }
    nav ul {
        list-style: none;
        display: flex;
        gap: 25px;
        margin: 0;
        padding: 0;
    }
    nav ul li {
        font-weight: 600;
        font-size: 1rem;
    }
    nav ul li a {
        color: #f0e6e6;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-block;
    }
    nav ul li a:hover,
    nav ul li a.active {
        background: #a67c7c;
        color: #fff;
    }

    /* Container */
    .container {
        max-width: 960px;
        margin: 40px auto 60px;
        padding: 0 20px;
        flex-grow: 1;
    }

    /* Cart Title */
    h1 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
        font-family: 'Georgia', serif;
        color: #381010ff;
        text-shadow: 1px 1px 2px #3d1818ff;
    }

    /* Cart Table */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    thead th {
        text-align: left;
        padding: 12px 15px;
        font-weight: 700;
        font-size: 1.1rem;
        color: #391313ff;
        border-bottom: 2px solid #d9cfcf;
    }
    tbody tr {
        background: #fff;
        box-shadow: 0 2px 6px rgba(107,79,79,0.15);
        border-radius: 12px;
        transition: box-shadow 0.3s ease;
    }
    tbody tr:hover {
        box-shadow: 0 6px 12px rgba(107,79,79,0.3);
    }
    tbody td {
        padding: 15px;
        vertical-align: middle;
        font-size: 1rem;
        color: #4b3b2b;
    }

    /* Product cell with image */
    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .product-info img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(107,79,79,0.2);
    }
    .product-info span {
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Price and total */
    .price, .total {
        font-weight: 600;
        color: #6b4f4f;
    }

    /* Quantity input */
    form.inline {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    input.qty-input {
        width: 60px;
        padding: 6px 10px;
        font-size: 1rem;
        border-radius: 8px;
        border: 1.5px solid #d9cfcf;
        text-align: center;
        transition: border-color 0.3s ease;
    }
    input.qty-input:focus {
        border-color: #6b4f4f;
        outline: none;
    }
    button.btn {
        background: #6b4f4f;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 0.9rem;
        box-shadow: 0 3px 6px rgba(107,79,79,0.3);
    }
    button.btn:hover {
        background: #a67c7c;
        box-shadow: 0 5px 10px rgba(166,124,124,0.5);
    }
    button.remove-btn {
        background: #d9534f;
        box-shadow: 0 3px 6px rgba(217,83,79,0.3);
    }
    button.remove-btn:hover {
        background: #b52b2b;
        box-shadow: 0 5px 10px rgba(181,43,43,0.5);
    }

    /* Grand total row */
    tfoot td {
        font-weight: 700;
        font-size: 1.3rem;
        color: #6b4f4f;
        padding: 20px 15px 15px;
        text-align: right;
    }
    tfoot td.label {
        text-align: left;
    }

    /* Cart buttons below table */
    .cart-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .cart-actions a, .cart-actions form button {
        background: #6b4f4f;
        color: #fff;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(107,79,79,0.4);
        transition: background-color 0.3s ease;
        text-align: center;
        display: inline-block;
        min-width: 180px;
        user-select: none;
    }
    .cart-actions a:hover, .cart-actions form button:hover {
        background: #a67c7c;
        box-shadow: 0 6px 14px rgba(166,124,124,0.6);
    }
    .cart-actions form button.clear-btn {
        background: #d9534f;
        box-shadow: 0 4px 10px rgba(217,83,79,0.4);
    }
    .cart-actions form button.clear-btn:hover {
        background: #b52b2b;
        box-shadow: 0 6px 14px rgba(181,43,43,0.6);
    }

    /* Empty cart */
    .empty-cart {
        text-align: center;
        margin-top: 80px;
        font-size: 1.4rem;
        color: #6b4f4f;
        font-weight: 600;
    }
    .empty-cart a {
        margin-top: 20px;
        display: inline-block;
        background: #6b4f4f;
        color: #fff;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(107,79,79,0.4);
        transition: background-color 0.3s ease;
    }
    .empty-cart a:hover {
        background: #a67c7c;
        box-shadow: 0 6px 14px rgba(166,124,124,0.6);
    }

    /* Responsive */
    @media (max-width: 720px) {
        .product-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        input.qty-input {
            width: 50px;
        }
        .cart-actions {
            flex-direction: column;
            align-items: center;
        }
        .cart-actions a, .cart-actions form button {
            min-width: 100%;
        }
    }
</style>
</head>
<body>

<header>
    <div class="logo">CoffeeHub</div>
    <nav>
        <ul>
            <li><a href="/Coffeehub/index.php">Home</a></li>
            <li><a href="/Coffeehub/about.php">About Us</a></li>
            <li><a href="/Coffeehub/products.php" class="active">Products</a></li>
            <li><a href="/Coffeehub/cart.php">Cart (<?= array_sum(array_column($_SESSION['cart'], 'qty')) ?>)</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1>Your Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="/Coffeehub/products.php">Continue Shopping</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th style="width: 140px;">Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $id => $item):
                $total = $item['price'] * $item['qty'];
                $grand_total += $total;
            ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="/Coffeehub/images/<?= htmlspecialchars($item['image'] ?? 'placeholder.png') ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
                            <span><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td class="price">₹<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <form method="post" action="cart.php" class="inline" aria-label="Update quantity for <?= htmlspecialchars($item['name']) ?>">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                            <input type="number" name="qty" value="<?= $item['qty'] ?>" min="0" class="qty-input" />
                            <button type="submit" name="update" class="btn">Update</button>
                        </form>
                    </td>
                    <td class="total">₹<?= number_format($total, 2) ?></td>
                    <td>
                        <form method="post" action="cart.php" class="inline" onsubmit="return confirm('Remove <?= htmlspecialchars($item['name']) ?> from cart?');" aria-label="Remove <?= htmlspecialchars($item['name']) ?> from cart">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                            <button type="submit" name="remove" class="btn remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="label">Grand Total</td>
                    <td colspan="2">₹<?= number_format($grand_total, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="cart-actions">
            <a href="/Coffeehub/index.php" class="btn">Continue Shopping</a>
            <a href="/Coffeehub/checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            <form method="post" action="cart.php" onsubmit="return confirm('Clear your entire cart?');" style="margin:0;">
                <button type="submit" name="clear" class="btn clear-btn">Clear Cart</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer style="background:#6b4f4f; color:#fff; text-align:center; padding:20px 0; font-weight:600;">
    &copy; <?= date('Y') ?> CoffeeHub. All rights reserved.
</footer>

</body>
</html>