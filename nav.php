<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Shared navigation helper
function nav($active = '') {
    $is_admin = isset($_SESSION['admin_logged_in']);
    ?>
    <nav>
        <div class="nav-brand">
            <span class="brand-icon">🎓</span>
            <span>CertVerify</span>
        </div>
        <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
        <div class="nav-links">
            <a href="index.php" class="<?= $active === 'home' ? 'active' : '' ?>">
                <span class="nav-icon">🏠</span> Home
            </a>
            <a href="verify.php" class="<?= $active === 'verify' ? 'active' : '' ?>">
                <span class="nav-icon">🔍</span> Verify
            </a>
            <?php if ($is_admin): ?>
                <a href="dashboard.php" class="<?= $active === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
                <a href="upload.php" class="<?= $active === 'upload' ? 'active' : '' ?>">
                    <span class="nav-icon">📤</span> Upload
                </a>
                <a href="manage.php" class="<?= $active === 'manage' ? 'active' : '' ?>">
                    <span class="nav-icon">⚙️</span> Manage
                </a>
                <a href="qr.php" class="<?= $active === 'qr' ? 'active' : '' ?>">
                    <span class="nav-icon">📱</span> QR Codes
                </a>
                <a href="report.php" class="<?= $active === 'report' ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span> Reports
                </a>
                <div class="nav-user">
                    <span class="user-badge">👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                    <a href="logout.php" class="nav-logout">Logout →</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="<?= $active === 'login' ? 'active' : '' ?>">
                    <span class="nav-icon">🔐</span> Login
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <?php
}
?>
