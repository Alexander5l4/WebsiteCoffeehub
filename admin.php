<?php
session_start();
include 'db.php';

// --- LOGIN LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch admin from database
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && $password === $admin['password']) { // you can later use password_verify if hashed
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $admin['username'];
        header("Location: admin.php"); // redirect to dashboard
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}

// --- LOGOUT LOGIC ---
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// --- SELECTED PAGE ---
$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CoffeeHub Admin Panel</title>
<style>
body { font-family: 'Poppins', sans-serif; margin: 0; background-color: #f7f6f3; }
.login-page {
    background: url('https://i.pinimg.com/1200x/9e/56/0c/9e560cdb73ae2abfd80577e093f8335b.jpg') no-repeat center center/cover;
    height: 100vh; display: flex; justify-content: center; align-items: center;
}
.login-container {
    background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
    border-radius: 20px; padding: 40px 50px; text-align: center; color: #fff; width: 350px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}
.login-container input { width: 100%; padding: 12px; border-radius: 8px; border: none; margin-bottom: 15px; background: rgba(255,255,255,0.9); color: #333; }
.login-container button { width: 100%; padding: 12px; background-color: #6b4f4f; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
.login-container button:hover { background-color: #5a3f3f; }
.error { color: #ffb3b3; margin-top: 10px; }

.dashboard { display: flex; height: 100vh; }
.sidebar { width: 240px; background-color: #3e2723; color: #fff; padding: 20px; display: flex; flex-direction: column; }
.sidebar h2 { text-align: center; margin-bottom: 30px; }
.sidebar a { color: #fff; text-decoration: none; margin: 10px 0; padding: 10px; border-radius: 8px; transition: background 0.3s; }
.sidebar a:hover, .active { background-color: #6d4c41; }
.main { flex-grow: 1; padding: 30px; background-color: #f7f6f3; overflow-y: auto; }
.topbar { display: flex; justify-content: space-between; align-items: center; background: #6b4f4f; color: #fff; padding: 15px 25px; border-radius: 10px; }
.cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
.card { background: #fff; border-radius: 12px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); padding: 25px; text-align: center; }
.card h3 { margin: 0; color: #3e2723; }
.card p { color: #777; margin-top: 8px; }
.logout { text-align: center; margin-top: auto; }
.logout a { color: #ffccbc; text-decoration: none; }
table { width: 100%; border-collapse: collapse; margin-top: 25px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
th, td { padding: 12px 15px; text-align: left; }
th { background: #6d4c41; color: white; }
tr:nth-child(even) { background: #f4f4f4; }
img { border-radius: 6px; }
</style>
</head>
<body>

<?php if (!isset($_SESSION["admin_logged_in"])): ?>
    <!-- LOGIN PAGE -->
    <div class="login-page">
        <div class="login-container">
            <h2>☕ CoffeeHub Admin</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Admin Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        </div>
    </div>

<?php else: ?>
    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="sidebar">
            <h2>☕ CoffeeHub</h2>
            <a href="?page=dashboard" class="<?= ($page=='dashboard'?'active':'') ?>">Dashboard</a>
            <a href="?page=users" class="<?= ($page=='users'?'active':'') ?>">Users</a>
            <a href="?page=orders" class="<?= ($page=='orders'?'active':'') ?>">Orders</a>
            <a href="?page=products" class="<?= ($page=='products'?'active':'') ?>">Products</a>
            <a href="?page=bills" class="<?= ($page=='bills'?'active':'') ?>">Bills</a>
            <a href="?page=messages" class="<?= ($page=='messages'?'active':'') ?>">Messages</a>
            <div class="logout"><a href="?logout=true">Logout</a></div>
        </div>

        <div class="main">
            <div class="topbar">
                <h2><?= ucfirst($page) ?> Section</h2>
                <p>Welcome, <?= htmlspecialchars($_SESSION["admin_username"]) ?> 👋</p>
            </div>

            <?php
            // --- DASHBOARD CARDS ---
            if ($page == 'dashboard') {
                echo '<div class="cards">
                    <div class="card"><h3>100</h3><p>Total Users</p></div>
                    <div class="card"><h3>30</h3><p>Total Orders</p></div>
                    <div class="card"><h3>₹25,500</h3><p>Total Revenue</p></div>
                    <div class="card"><h3>55</h3><p>Messages Received</p></div>
                </div>';
            }

            // --- USERS ---
            if ($page == 'users') {
                $users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>All Users</h3><table><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
                foreach ($users as $u) {
                    echo "<tr><td>{$u['id']}</td><td>{$u['username']}</td><td>{$u['email']}</td><td>{$u['role']}</td><td>{$u['created_at']}</td></tr>";
                }
                echo "</table>";
            }

            // --- ORDERS ---
            if ($page == 'orders') {
                $orders = $pdo->query("SELECT * FROM orders ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>All Orders</h3><table><tr><th>ID</th><th>User ID</th><th>Total</th><th>Status</th><th>Created At</th></tr>";
                foreach ($orders as $o) {
                    echo "<tr><td>{$o['id']}</td><td>{$o['user_id']}</td><td>₹{$o['total_amount']}</td><td>{$o['status']}</td><td>{$o['created_at']}</td></tr>";
                }
                echo "</table>";
            }

            // --- PRODUCTS (Add/Edit) ---
            if ($page == 'products') {
                // Handle Add Product
                if (isset($_POST['add_product'])) {
                    $name = trim($_POST['name']);
                    $price = trim($_POST['price']);
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $upload_dir = 'uploads/';
                        $image_name = basename($_FILES['image']['name']);
                        $target_file = $upload_dir . $image_name;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                            $stmt = $pdo->prepare("INSERT INTO products (name, price, image, created_at) VALUES (?, ?, ?, NOW())");
                            $stmt->execute([$name, $price, $target_file]);
                            echo "<p style='color:green;'>Product added successfully!</p>";
                        } else {
                            echo "<p style='color:red;'>Failed to upload image.</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Please select an image.</p>";
                    }
                }

                $products = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

                // Add Product Form
                echo '<h3>Add New Product</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-bottom:30px; display:flex; flex-direction:column; max-width:400px;">
                    <input type="text" name="name" placeholder="Product Name" required style="margin-bottom:10px; padding:8px;">
                    <input type="number" name="price" placeholder="Price" required style="margin-bottom:10px; padding:8px;">
                    <input type="file" name="image" required style="margin-bottom:10px; padding:5px;">
                    <button type="submit" name="add_product" style="padding:10px; background:#6b4f4f; color:white; border:none; border-radius:6px; cursor:pointer;">Add Product</button>
                </form>';

                // Show all products
                echo '<h3>All Products</h3>
                <table>
                    <tr>
                        <th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Created</th><th>Action</th>
                    </tr>';
                foreach ($products as $p) {
                    echo '<tr>
                        <td>' . $p['id'] . '</td>
                        <td><img src="' . $p['image'] . '" width="80"></td>
                        <td>' . htmlspecialchars($p['name']) . '</td>
                        <td>₹' . $p['price'] . '</td>
                        <td>' . $p['created_at'] . '</td>
                        <td><a href="?page=edit_product&id=' . $p['id'] . '" style="color:#6b4f4f;">Edit</a></td>
                    </tr>';
                }
                echo '</table>';
            }

            // --- EDIT PRODUCT ---
            if ($page == 'edit_product' && isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    echo "<p style='color:red;'>Product not found!</p>";
                } else {
                    if (isset($_POST['update_product'])) {
                        $name = trim($_POST['name']);
                        $price = trim($_POST['price']);
                        $image_path = $product['image'];

                        if (!empty($_FILES['image']['name'])) {
                            $upload_dir = 'uploads/';
                            $image_name = basename($_FILES['image']['name']);
                            $target_file = $upload_dir . $image_name;
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                                $image_path = $target_file;
                            }
                        }

                        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, image=? WHERE id=?");
                        $stmt->execute([$name, $price, $image_path, $id]);
                        echo "<p style='color:green;'>Product updated successfully!</p>";

                        // Refresh product info
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
                        $stmt->execute([$id]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                    // Edit Product Form
                    echo '<h3>Edit Product</h3>
                    <form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; max-width:400px;">
                        <input type="text" name="name" value="' . htmlspecialchars($product['name']) . '" required style="margin-bottom:10px; padding:8px;">
                        <input type="number" name="price" value="' . $product['price'] . '" required style="margin-bottom:10px; padding:8px;">
                        <input type="file" name="image" style="margin-bottom:10px; padding:5px;">
                        <p>Current Image:</p>
                        <img src="' . $product['image'] . '" width="100" style="margin-bottom:10px;">
                        <button type="submit" name="update_product" style="padding:10px; background:#6b4f4f; color:white; border:none; border-radius:6px; cursor:pointer;">Update Product</button>
                    </form>';
                }
            }

            // --- BILLS ---
            if ($page == 'bills') {
                $bills = $pdo->query("SELECT * FROM bill ORDER BY bill_id ASC")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>Billing Records</h3><table><tr><th>Bill ID</th><th>Order ID</th><th>User ID</th><th>Total</th><th>Payment Status</th><th>Method</th><th>Date</th></tr>";
                foreach ($bills as $b) {
                    echo "<tr><td>{$b['bill_id']}</td><td>{$b['order_id']}</td><td>{$b['user_id']}</td><td>₹{$b['total_amount']}</td><td>{$b['payment_status']}</td><td>{$b['payment_method']}</td><td>{$b['billing_date']}</td></tr>";
                }
                echo "</table>";
            }

            // --- MESSAGES ---
            if ($page == 'messages') {
                $msgs = $pdo->query("SELECT * FROM contact_messages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>Contact Messages</h3><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>";
                foreach ($msgs as $m) {
                    echo "<tr><td>{$m['id']}</td><td>{$m['name']}</td><td>{$m['email']}</td><td>{$m['message']}</td><td>{$m['created_at']}</td></tr>";
                }
                echo "</table>";
            }
            ?>
        </div>
    </div>
<?php endif; ?>
</body>
</html>
