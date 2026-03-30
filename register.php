<?php
session_start();
require 'db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: report.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    // Validate password: strictly at least 5 letters and 2 numbers
    $letters_count = preg_match_all('/[a-zA-Z]/', $password, $matches_letters);
    $numbers_count = preg_match_all('/[0-9]/', $password, $matches_numbers);

    if (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif ($letters_count < 5 || $numbers_count < 2) {
        $error = 'Password must strictly contain at least 5 letters and 2 numeric characters.';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'This username is already taken. Please choose another.';
        } else {
            // Register the user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, 'staff')");
            if ($stmt->execute([$username, $hash, $full_name])) {
                $success = 'Account created successfully! You can now <a href="login.php" style="color:#00d2ff;">Login here</a>.';
            } else {
                $error = 'Something went wrong creating your account.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CertVerify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { justify-content: center; } /* Perfect vertical centering */
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 Create Account</h2>
        <p class="subtitle">Join as a Staff member to manage certificates</p>
        
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>

                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>

                <label>Password <span style="font-size:11px; color:#00d2ff; font-weight:normal;">(Must have at least 5 letters and 2 numbers)</span></label>
                <input type="password" name="password" placeholder="Create a secure password" required>

                <button type="submit">Register Account →</button>
            </form>
            <div style="text-align: center; margin-top: 20px;">
                <p style="font-size: 14px; opacity: 0.8;">Already have an account? <a href="login.php" style="color: #00d2ff; text-decoration: none; font-weight: bold;">Login here</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
