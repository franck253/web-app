<?php
require 'nav.php';
require 'db.php';

$certificate = null;
$error = '';
$search_query = $_GET['cert'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_query = trim($_POST['cert_number'] ?? '');
}

if ($search_query) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE cert_number = ?");
    $stmt->execute([$search_query]);
    $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$certificate) {
        $error = "❌ No certificate found with that number. It may be invalid or not yet issued.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate | CertVerify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php nav('verify'); ?>
    <div class="container">
        <h2>🔍 Verify Certificate</h2>
        <p class="subtitle">Enter a certificate number to verify its authenticity</p>

        <form method="POST" action="verify.php">
            <label>Certificate Number <span style="font-size:11px; color:#00d2ff; font-weight:normal;">(Format: CERT-YYYY-NNN)</span></label>
            <input type="text" name="cert_number" placeholder="e.g. CERT-2026-001"
                   value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit">Verify Now</button>
        </form>

        <?php if ($error): ?>
            <div class="alert error" style="margin-top:20px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($certificate): ?>
            <div class="alert success" style="margin-top:20px;">✅ Certificate is VALID and officially issued.</div>
            <div class="cert-details">
                <div><strong>Certificate No.</strong> <span><?php echo htmlspecialchars($certificate['cert_number']); ?></span></div>
                <div><strong>Student Name</strong> <span><?php echo htmlspecialchars($certificate['student_name']); ?></span></div>
                <div><strong>Course Completed</strong> <span><?php echo htmlspecialchars($certificate['course_name']); ?></span></div>
                <div><strong>Issue Date</strong> <span><?php echo htmlspecialchars($certificate['issue_date']); ?></span></div>
                <div><strong>Status</strong> <span class="badge">✓ Verified</span></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
