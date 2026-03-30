<?php
require 'nav.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$message = '';
$new_cert = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cert_number = trim($_POST['cert_number'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $issue_date = $_POST['issue_date'] ?? '';

    // Validate: cert_number must contain at least one letter AND one number
    if (!preg_match('/[a-zA-Z]/', $cert_number) || !preg_match('/[0-9]/', $cert_number)) {
        $message = "<div class='alert error'>❌ Certificate Number must contain both letters and numbers (e.g. CERT-2026-001).</div>";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM certificates WHERE cert_number = ?");
        $stmt->execute([$cert_number]);

        if ($stmt->fetch()) {
            $message = "<div class='alert error'>Certificate Number already exists!</div>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO certificates (cert_number, student_name, course_name, issue_date) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$cert_number, $student_name, $course_name, $issue_date])) {
                $message = "<div class='alert success'>✅ Certificate uploaded successfully!</div>";
                $new_cert = ['cert_number' => $cert_number, 'student_name' => $student_name];
            } else {
                $message = "<div class='alert error'>Failed to upload certificate.</div>";
            }
        }
    }
}

function getQrUrl($cert_number) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['REQUEST_URI']);
    if ($dir == '/' || $dir == '\\') $dir = '';
    $verify_link = "$protocol://$domain$dir/verify.php?cert=" . urlencode($cert_number);
    return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($verify_link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Certificate | CertVerify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php nav('upload'); ?>
    <div class="container">
        <h2>📤 Upload Certificate</h2>
        <p class="subtitle">Add a new certificate to the system</p>
        <?php echo $message; ?>
        <form method="POST">
            <label>Certificate Number <span style="font-size:11px; color:#00d2ff; font-weight:normal;">(Must contain letters &amp; numbers, e.g. CERT-2026-001)</span></label>
            <input type="text" name="cert_number" placeholder="e.g. CERT-2026-001" pattern="(?=.*[a-zA-Z])(?=.*[0-9]).+" title="Must contain both letters and numbers" required>

            <label>Student Name</label>
            <input type="text" name="student_name" placeholder="Full Name" required>

            <label>Course Name</label>
            <input type="text" name="course_name" placeholder="e.g. B.Sc Computer Science" required>

            <label>Issue Date</label>
            <input type="date" name="issue_date" required>

            <button type="submit">Upload & Generate QR Code</button>
        </form>

        <?php if ($new_cert): ?>
            <div style="margin-top: 30px; text-align: center;">
                <p style="margin-bottom: 15px; opacity: 0.8;">QR Code for <strong><?php echo htmlspecialchars($new_cert['cert_number']); ?></strong></p>
                <img src="<?php echo getQrUrl($new_cert['cert_number']); ?>"
                     alt="QR Code"
                     style="border: 5px solid white; border-radius: 10px; background: white; width: 200px;">
                <br><br>
                <a href="qr.php" class="btn" style="font-size: 13px; padding: 10px 20px;">View All QR Codes →</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
