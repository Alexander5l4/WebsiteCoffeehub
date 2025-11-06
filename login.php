<?php
session_start(); // MUST be first line
$host = "localhost";
$user = "root";
$pass = "";
$db   = "coffeehub"; // your database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// =====================
// SIGNUP
// =====================
if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username && $email && $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username; // log in immediately
            header("Location: index.php");     // redirect to main website
            exit;
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "All fields are required for signup!";
    }
}

// =====================
// LOGIN
// =====================
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // redirect to main website
            exit;
        } else {
            $message = "Invalid email or password!";
        }
    } else {
        $message = "Email and password are required!";
    }
}
?>

<!-- Minimal HTML forms -->
<h2>Signup</h2>
<form method="post" action="">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="signup">Sign Up</button>
</form>

<h2>Login</h2>
<form method="post" action="">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
</form>

<?php if ($message) echo "<p>$message</p>"; ?>
