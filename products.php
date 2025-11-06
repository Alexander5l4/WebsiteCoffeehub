<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products | CoffeeHub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Horizontal product layout */
        .products-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product, .product-details {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .product {
            flex: 1 1 250px;
        }

        .product img, .product-details img {
            width: 100%;
            max-width: 300px;
            height: auto;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .product .price, .product-details .price {
            font-weight: bold;
            margin: 10px 0;
        }

        .product button, .product-details button {
            background-color: #523e30ff;
            color: white;
            margin-top: 7px;
            border: none;
            padding: 20px 15px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        .product button:hover, .product-details button:hover {
            background-color: #eb7754ff;
        }

        .btn-details {
            display: inline-block;
            margin-top: 7px;
            text-decoration: none;
            color: #ffffffff;
            font-weight: bold;
        }

        .btn-details:hover {
            text-decoration: underline;
        }

        /* Single product detail layout */
        .product-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .product-details img {
            flex: 1 1 300px;
        }

        .product-details .info {
            flex: 1 1 300px;
            text-align: left;
        }

        .product-details .info h2 {
            margin-top: 0;
        }

        .product-details .info p {
            line-height: 1.5;
        }

        .product-details form {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

<?php
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Show single product details
    $product_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $img = strtolower(str_replace(' ', '', $product['name'])) . '.jpg';

        echo "<div class='product-details'>";
        echo "<img src='/Coffeehub/images/" . $img . "' alt='" . htmlspecialchars($product['name']) . "'>";
        echo "<div class='info'>";
        echo "<h2>" . htmlspecialchars($product['name']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($product['description'])) . "</p>";
        echo "<p class='price'>Price: ₹" . number_format($product['price'], 2) . "</p>";

        // Add to Cart form
        echo "<form method='POST' action='cart.php'>";
        echo "<input type='hidden' name='id' value='" . $product['id'] . "'>";
        echo "<input type='hidden' name='name' value='" . htmlspecialchars($product['name']) . "'>";
        echo "<input type='hidden' name='price' value='" . $product['price'] . "'>";
        echo "<button type='submit' name='add'>Add to Cart</button>";
        echo "</form>";

        echo "<p><a href='products.php' class='btn-details'>← Back to Products</a></p>";
        echo "</div></div>";
    } else {
        echo "<p>Product not found.</p>";
        echo "<p><a href='products.php' class='btn-details'>← Back to Products</a></p>";
    }
} else {
    // Show all products
    echo "<h2>Our Products</h2>";
    echo "<div class='products-container'>";
    $stmt = $pdo->query("SELECT * FROM products");
    while ($row = $stmt->fetch()) {
        $img = strtolower(str_replace(' ', '', $row['name'])) . '.jpg';
        echo "
        <div class='product'>
            <img src='/Coffeehub/images/" . $img . "' alt='" . htmlspecialchars($row['name']) . "'>
            <h3>" . htmlspecialchars($row['name']) . "</h3>
            <p>" . htmlspecialchars($row['description']) . "</p>
            <p class='price'>₹ " . number_format($row['price'], 2) . "</p>

            <form method='POST' action='cart.php'>
                <input type='hidden' name='id' value='" . $row['id'] . "'>
                <input type='hidden' name='name' value='" . htmlspecialchars($row['name']) . "'>
                <input type='hidden' name='price' value='" . $row['price'] . "'>
                <button type='submit' name='add'>Add to Cart</button>
            </form>

            <a href='products.php?id=" . $row['id'] . "' class='btn-details'>View Details</a>
        </div>";
    }
    echo "</div>";
}
?>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
