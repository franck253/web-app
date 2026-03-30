<?php
require_once 'nav.php'; // Starts session
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Quick stats
$total_certs = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
$this_month = $pdo->query("SELECT COUNT(*) FROM certificates WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$recent_scans = $pdo->query("SELECT COUNT(*) FROM verification_logs")->fetchColumn();

// Recent certs
$stmt = $pdo->query("SELECT * FROM certificates ORDER BY id DESC LIMIT 5");
$recent_certs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CertVerify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php nav('dashboard'); ?>
    
    <div class="wide-container">
        <h2>🛠️ Admin Dashboard</h2>
        <p class="subtitle">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>. Here's what's happening today.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $total_certs ?></div>
                <div class="stat-label">Total Issued</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $this_month ?></div>
                <div class="stat-label">New This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $recent_scans ?></div>
                <div class="stat-label">Total Verifications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">✓</div>
                <div class="stat-label">System Status</div>
            </div>
        </div>
        
        <h3 style="margin-top: 30px; margin-bottom: 15px; text-align: left; padding-left: 10px;">Recent Certificates</h3>
        <?php if (count($recent_certs) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cert Number</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Date Issued</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_certs as $cert): ?>
                        <tr>
                            <td><strong style="color:#00d2ff;"><?= htmlspecialchars($cert['cert_number']) ?></strong></td>
                            <td><?= htmlspecialchars($cert['student_name']) ?></td>
                            <td><?= htmlspecialchars($cert['course_name']) ?></td>
                            <td><?= htmlspecialchars($cert['issue_date']) ?></td>
                            <td>
                                <a href="manage.php" class="link-blue">Manage</a> | 
                                <a href="verify.php?cert=<?= urlencode($cert['cert_number']) ?>" target="_blank" class="link-green">Verify</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="text-align:center; margin-top: 20px;">
                <a href="report.php" class="btn">View All Certificates →</a>
            </div>
        <?php else: ?>
            <p style="text-align:center; padding: 40px; opacity: 0.6;">No certificates issued yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
