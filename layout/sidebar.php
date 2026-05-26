<?php
/**
 * Layout Sidebar Navigasi
 * Aplikasi SPP
 * 
 * Menampilkan menu navigasi vertikal secara dinamis berdasarkan level akses pengguna.
 */
$base_url = "/Aplikasi SPP";
$current_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Mendapatkan level akses pengguna dari session
$user_level = $_SESSION['level'] ?? 'siswa';
?>
<aside class="sidebar no-print">
    <div class="sidebar-header d-flex flex-column align-items-center">
        <div class="sidebar-logo">
            <i class="fas fa-wallet" style="font-size: 1.5rem;"></i>
        </div>
        <h3>Aplikasi SPP</h3>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <ul>
            <!-- 1. Dashboard (Semua Akses) -->
            <li class="<?= ($script_name == $base_url . '/index.php') ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/index.php">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
            </li>

            <?php if ($user_level === 'admin'): ?>
            <!-- 2. Data Kelas (Hanya Admin) -->
            <li class="<?= (strpos($current_uri, '/kelas/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/kelas/index.php">
                    <i class="fas fa-school"></i> <span>Data Kelas</span>
                </a>
            </li>

            <!-- 3. Data Siswa (Hanya Admin) -->
            <li class="<?= (strpos($current_uri, '/siswa/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/siswa/index.php">
                    <i class="fas fa-user-graduate"></i> <span>Data Siswa</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
            <!-- 4. Cek Pembayaran (Admin & Petugas) -->
            <li class="<?= (strpos($current_uri, '/cek_pembayaran/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/cek_pembayaran/index.php">
                    <i class="fas fa-money-check-alt"></i> <span>Cek Pembayaran</span>
                </a>
            </li>

            <!-- 5. Pembayaran (Admin & Petugas) -->
            <li class="<?= (strpos($current_uri, '/pembayaran/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/pembayaran/index.php">
                    <i class="fas fa-receipt"></i> <span>Pembayaran</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- 6. Bukti Pembayaran (Semua Akses) -->
            <li class="<?= (strpos($current_uri, '/bukti_pembayaran/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/bukti_pembayaran/index.php">
                    <i class="fas fa-file-invoice"></i> <span>Bukti Pembayaran</span>
                </a>
            </li>

            <?php if ($user_level === 'admin'): ?>
            <!-- 7. Data Petugas (Hanya Admin) -->
            <li class="<?= (strpos($current_uri, '/petugas/') !== false) ? 'active' : '' ?>">
                <a href="<?= $base_url ?>/modules/petugas/index.php">
                    <i class="fas fa-user-tie"></i> <span>Data Petugas</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="menu-label">Akun</div>
        <ul>
            <li>
                <a href="<?= $base_url ?>/auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
