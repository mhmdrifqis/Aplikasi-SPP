<?php
/**
 * Header Halaman Global
 * Aplikasi SPP
 * 
 * Mendefinisikan struktur head HTML, stylesheet, script eksternal,
 * manajemen session, proteksi login, serta alert sistem.
 */
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi path dasar aplikasi
$base_url = "/Aplikasi SPP";
$base_path = dirname(__DIR__);

// Validasi status login pengguna (Kecuali untuk halaman login.php)
$current_page = $_SERVER['PHP_SELF'];
if (!strpos($current_page, 'login.php') && !isset($_SESSION['login'])) {
    header("Location: " . $base_url . "/auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - Aplikasi SPP" : "Aplikasi SPP" ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Custom CSS Theme -->
    <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
    <!-- Chart.js untuk grafik statistika -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>
<?php if (isset($_SESSION['login']) && !strpos($current_page, 'login.php')): ?>
<div class="wrapper">
    <!-- Sidebar Navigasi -->
    <?php include_once $base_path . '/layout/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Top Navigasi Bar -->
        <header class="top-nav no-print">
            <div class="welcome-text d-flex align-items-center">
                <!-- Tombol menu bar untuk tampilan responsive mobile -->
                <button class="btn btn-light border d-lg-none me-3" id="sidebar-toggle" style="padding: 8px 12px; border-radius: 8px;">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 style="font-size: 1.2rem; color: #444; font-weight: 600; margin: 0;">
                    Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
                </h2>
            </div>
            <div class="user-profile">
                <div class="user-info" style="text-align: right;">
                    <p style="font-weight: 600; font-size: 0.9rem; margin: 0;"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></p>
                    <p style="font-size: 0.75rem; color: var(--gray); margin: 0;"><?= ucfirst(htmlspecialchars($_SESSION['level'] ?? 'siswa')) ?></p>
                </div>
                <!-- Avatar profil dinamis menggunakan API ui-avatars -->
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama'] ?? 'User') ?>&background=3b82f6&color=fff" alt="User Avatar">
            </div>
        </header>
        <div class="content-body">
            
            <!-- Alert Notifikasi Session -->
            <?php if (isset($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show no-print mb-4" role="alert">
                    <?= $_SESSION['alert']['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>
            
<?php endif; ?>
