<?php
require 'nav.php'; // Starts session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | CertVerify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex: 1;
            padding: 40px 20px;
        }
        .hero h1 {
            font-size: 3rem;
            color: #00d2ff;
            margin-bottom: 20px;
            font-weight: 800;
        }
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 40px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .hero-btn {
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(to right, #00d2ff, #3a7bd5);
            color: white;
            box-shadow: 0 10px 20px rgba(0, 210, 255, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 210, 255, 0.5);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <?php nav('home'); ?>
    
    <div class="hero">
        <h1>Verify Certificates Instantly</h1>
        <p>A secure, transparent, and user-friendly platform to issue, manage, and verify student credentials and certificates online.</p>
        
        <div class="hero-buttons">
            <a href="verify.php" class="hero-btn btn-primary">🔍 Verify a Certificate</a>
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                <a href="dashboard.php" class="hero-btn btn-secondary">📊 Go to Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="hero-btn btn-secondary">🔐 Login</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
