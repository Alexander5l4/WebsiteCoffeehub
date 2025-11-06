<?php
session_start();
include 'db.php'; // contains $pdo

// default tab
$activeTab = isset($_GET['tab']) && in_array($_GET['tab'], ['login','signup']) ? $_GET['tab'] : 'login';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== LOGIN =====
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if ($email && $password) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit;
            } else {
                $message = "Invalid email or password!";
                $activeTab = 'login';
            }
        } else {
            $message = "Please enter both email and password!";
            $activeTab = 'login';
        }
    }

    // ===== SIGNUP =====
    if (isset($_POST['signup'])) {
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm  = $_POST['confirm_password'];

        if ($username && $email && $password && $confirm) {
            if ($password !== $confirm) {
                $message = "Passwords do not match!";
                $activeTab = 'signup';
            } else {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
                $stmt->execute([$email]);
                if ($stmt->rowCount() > 0) {
                    $message = "Email already registered! Please login.";
                    $activeTab = 'login';
                } else {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                    if ($stmt->execute([$username, $email, $hashed])) {
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = 'user';
                        header("Location: index.php");
                        exit;
                    } else {
                        $message = "Signup failed! Try again.";
                        $activeTab = 'signup';
                    }
                }
            }
        } else {
            $message = "All fields are required!";
            $activeTab = 'signup';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CoffeeHub | Login / Sign Up</title>
<style>
/* ===== GLOBAL ===== */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f9f7f5, #e6dacd);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    margin:0;
}

/* ===== CARD ===== */
.tab-container {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.15);
    width: 380px;
    padding: 35px;
    animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}

/* ===== TABS ===== */
.tabs {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
}
.tab {
    flex:1;
    text-align:center;
    background: #f2ede8;
    border-radius: 30px;
    padding: 10px;
    margin:0 5px;
    cursor: pointer;
    font-weight: 600;
    color: #5a3e2b;
    text-decoration:none;
    transition: all 0.3s;
}
.tab.active {
    background: #8b4513;
    color:#fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* ===== FORMS ===== */
form {display: none; flex-direction: column;}
form.active {display:flex; animation: fadeIn 0.4s ease;}

input {
    padding: 12px; 
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius:8px;
    font-size: 14px;
    transition: border 0.3s;
}
input:focus {
    outline:none;
    border:1px solid #8b4513;
    box-shadow:0 0 4px rgba(139,69,19,0.4);
}

/* ===== BUTTON ===== */
button {
    background: #8b4513;
    color:#fff;
    border:none;
    padding:12px;
    border-radius:8px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition: all 0.3s;
}
button:hover {
    background:#5a2e0d;
    transform: translateY(-1px);
}

/* ===== MESSAGE ===== */
.message {
    background:#ffe0b2;
    color:#663300;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}
</style>
</head>
<body>

<div class="tab-container">
  <div class="tabs">
    <a href="?tab=login" class="tab <?= ($activeTab==='login')?'active':'' ?>">Login</a>
    <a href="?tab=signup" class="tab <?= ($activeTab==='signup')?'active':'' ?>">Sign Up</a>
  </div>

  <?php if($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- LOGIN FORM -->
  <form method="POST" class="<?= ($activeTab==='login')?'active':'' ?>">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>

  <!-- SIGNUP FORM -->
  <form method="POST" class="<?= ($activeTab==='signup')?'active':'' ?>">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit" name="signup">Sign Up</button>
  </form>
</div>

</body>
</html>
