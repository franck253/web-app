<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Change if your MySQL has a password

try {
    // Step 1: Connect WITHOUT specifying a database first
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Step 2: Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cert_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE cert_system");

    // Step 3: Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) DEFAULT '',
            role ENUM('admin','staff') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(100) DEFAULT '' AFTER password");
    } catch(PDOException $e) {}

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin','staff') DEFAULT 'admin' AFTER full_name");
    } catch(PDOException $e) {}

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cert_number VARCHAR(100) UNIQUE NOT NULL,
            student_name VARCHAR(255) NOT NULL,
            course_name VARCHAR(255) NOT NULL,
            institution VARCHAR(255) DEFAULT 'University',
            grade VARCHAR(50) DEFAULT '',
            issue_date DATE NOT NULL,
            expiry_date DATE DEFAULT NULL,
            status ENUM('active','revoked','expired') DEFAULT 'active',
            issued_by VARCHAR(100) DEFAULT 'Admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS verification_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cert_number VARCHAR(100) NOT NULL,
            ip_address VARCHAR(45) DEFAULT '',
            verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            result ENUM('valid','invalid') DEFAULT 'valid'
        )
    ");

    // Step 4: Create default admin account if not already there
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)")
            ->execute(['admin', $hash, 'System Administrator', 'admin']);
    }

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
