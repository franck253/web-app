<?php
require 'nav.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$stmt = $pdo->query("SELECT * FROM certificates ORDER BY id DESC");
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getQrUrl($cert_number, $size = 160) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['REQUEST_URI']);
    if ($dir == '/' || $dir == '\\') $dir = '';
    $verify_link = "$protocol://$domain$dir/verify.php?cert=" . urlencode($cert_number);
    return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($verify_link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes | CertVerify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php nav('qr'); ?>
    <div class="wide-container">
        <h2>📱 QR Codes</h2>
        <p class="subtitle">Scan any QR code to instantly verify a certificate</p>

        <?php if (count($certificates) > 0): ?>
            <div class="qr-grid">
                <?php foreach ($certificates as $cert): ?>
                    <div class="qr-card">
                        <img src="<?php echo getQrUrl($cert['cert_number']); ?>"
                             alt="QR for <?php echo htmlspecialchars($cert['cert_number']); ?>">
                        <div class="cert-id"><?php echo htmlspecialchars($cert['cert_number']); ?></div>
                        <div class="cert-name"><?php echo htmlspecialchars($cert['student_name']); ?></div>
                        <a href="verify.php?cert=<?php echo urlencode($cert['cert_number']); ?>" target="_blank">View Certificate</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 40px; opacity: 0.6;">
                No certificates uploaded yet. <a href="upload.php" class="link-blue">Upload one now →</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
