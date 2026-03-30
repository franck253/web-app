<?php
require_once 'nav.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$stmt = $pdo->query("SELECT * FROM certificates ORDER BY id DESC");
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($certificates);

// Get this month's count
$month_stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
$this_month = $month_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | CertVerify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php nav('report'); ?>
    <div class="wide-container">
        <h2>📊 Reports Dashboard</h2>
        <p class="subtitle">Overview of all issued certificates</p>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Certificates</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $this_month; ?></div>
                <div class="stat-label">Issued This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">✓</div>
                <div class="stat-label">All Verified</div>
            </div>
        </div>

        <?php if ($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cert Number</th>
                        <th>Student Name</th>
                        <th>Course Name</th>
                        <th>Issue Date</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certificates as $i => $cert): ?>
                        <tr>
                            <td style="opacity:0.5;"><?php echo $total - $i; ?></td>
                            <td><strong style="color:#00d2ff;"><?php echo htmlspecialchars($cert['cert_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cert['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($cert['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($cert['issue_date']); ?></td>
                            <td style="opacity:0.6;"><?php echo date('d M Y', strtotime($cert['created_at'])); ?></td>
                            <td>
                                <a class="link-blue" href="verify.php?cert=<?php echo urlencode($cert['cert_number']); ?>" target="_blank">Verify</a>
                                &nbsp;|&nbsp;
                                <a class="link-green" href="qr.php">QR Code</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; padding:40px; opacity:0.6;">
                No certificates yet. <a href="upload.php" class="link-blue">Upload one now →</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
