<?php
$host = "localhost";  // or 127.0.0.1
$user = "root";       // default XAMPP user
$pass = "";           // default XAMPP password is empty
$db   = "coffeehub";  // your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Connected to database!";
// After successful signup
$_SESSION['username'] = $username;  // optional: log in immediately after signup
header("Location: index.php");
exit;

// After successful login
$_SESSION['username'] = $user['username'];
header("Location: index.php");
exit;
?>
