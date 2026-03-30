<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: report.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        header('Location: report.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CertVerify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { justify-content: center; } /* ensures perfect vertical centering since there is no nav now */
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Login</h2>
        <p class="subtitle">Sign in to access the system</p>
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login →</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <p style="font-size: 14px; opacity: 0.8;">Don't have an account? <a href="register.php" style="color: #00d2ff; text-decoration: none; font-weight: bold;">Create one</a></p>
        </div>
    </div>
</body>
</html>
