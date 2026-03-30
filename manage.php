<?php
require 'nav.php'; // Starts session
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$message = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "<div class='alert success'>✅ Certificate successfully deleted.</div>";
    } else {
        $message = "<div class='alert error'>Failed to delete certificate.</div>";
    }
}

$stmt = $pdo->query("SELECT * FROM certificates ORDER BY id DESC");
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($certificates);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Certificates | CertVerify</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this certificate? This action cannot be undone.")) {
                window.location.href = "manage.php?delete=" + id;
            }
        }
    </script>
</head>
<body>
    <?php nav('manage'); ?>
    
    <div class="wide-container">
        <h2>⚙️ Manage Certificates</h2>
        <p class="subtitle">Edit or Remove Issued Certificates</p>

        <?php echo $message; ?>

        <?php if ($total > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cert Number</th>
                        <th>Student Name</th>
                        <th>Issue Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certificates as $i => $cert): ?>
                        <tr>
                            <td style="opacity:0.5;"><?php echo $total - $i; ?></td>
                            <td><strong style="color:#00d2ff;"><?php echo htmlspecialchars($cert['cert_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cert['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($cert['issue_date']); ?></td>
                            <td><span class="badge"><?php echo htmlspecialchars($cert['status']); ?></span></td>
                            <td>
                                <a href="upload.php" class="link-blue">New</a>
                                &nbsp;|&nbsp;
                                <a href="#" onclick="confirmDelete(<?php echo $cert['id']; ?>)" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Delete</a>
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
