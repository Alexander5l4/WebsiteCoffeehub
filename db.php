<?php
$host = '127.0.0.1';   // safer than 'localhost'
$db   = 'coffeehub';   // your actual DB name
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "";
} catch (\PDOException $e) {
     die("❌ DB Connection failed: " . $e->getMessage());
}
?>
