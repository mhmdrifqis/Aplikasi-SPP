<?php

$title = "Beranda Dashboard";
require_once 'layout/header.php';
require_once 'config/Database.php';

$database = new Database();
$db = $database->getConnection();

$user_level = $_SESSION['level'] ?? 'siswa';
$user_name = $_SESSION['nama'] ?? 'User';
$siswa_nisn = $_SESSION['nisn'] ?? '';

if ($user_level === 'admin' || $user_level === 'petugas') {
    // 1. Mengambil data ringkasan statistik (Admin & Petugas)
    $count_siswa = $db->query("SELECT COUNT(*) FROM tb_siswa_muhammadrifqisaifulloh")->fetchColumn();
    $count_kelas = $db->query("SELECT COUNT(*) FROM tb_kelas_muhammadrifqisaifulloh")->fetchColumn();
    $count_petugas = $db->query("SELECT COUNT(*) FROM tb_petugas_muhammadrifqisaifulloh WHERE level = 'petugas'")->fetchColumn();
    $total_spp_collected = $db->query("SELECT SUM(CAST(jumlah_bayar AS UNSIGNED)) FROM tb_pembayaran_muhammadrifqisaifulloh WHERE status = 'Sudah Lunas'")->fetchColumn() ?: 0;

    // Mengambil data jumlah siswa yang sudah lunas dan belum lunas
    $count_lunas = $db->query("SELECT COUNT(*) FROM tb_cek_pembayaran_muhammadrifqisaifulloh WHERE status_pembayaran = 'Sudah Lunas'")->fetchColumn() ?: 0;
    $count_belum_lunas = $db->query("SELECT COUNT(*) FROM tb_cek_pembayaran_muhammadrifqisaifulloh WHERE status_pembayaran = 'Belum Lunas'")->fetchColumn() ?: 0;
    $total_spp_siswa = $count_lunas + $count_belum_lunas;
    $pct_lunas = ($total_spp_siswa > 0) ? round(($count_lunas / $total_spp_siswa) * 100) : 0;

    // 2. Mengambil 5 log transaksi terupdate terbaru
    $recent_query = "SELECT p.*, s.nama, k.nama_kelas 
                     FROM tb_pembayaran_muhammadrifqisaifulloh p
                     JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                     LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                     ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC LIMIT 5";
    $recent_stmt = $db->query($recent_query);
} else {
    // 1. Mengambil ringkasan data statistik khusus level siswa
    $count_pembayaran_lunas = 0;
    $status_siswa = 'Belum Lunas';
    $nominal_spp = 0;

    if (!empty($siswa_nisn)) {
        $count_pembayaran_lunas = $db->query("SELECT COUNT(*) FROM tb_pembayaran_muhammadrifqisaifulloh WHERE nisn = '$siswa_nisn' AND status = 'Sudah Lunas'")->fetchColumn();
        
        $status_stmt = $db->query("SELECT status_pembayaran FROM tb_cek_pembayaran_muhammadrifqisaifulloh WHERE nisn = '$siswa_nisn' LIMIT 1");
        $status_siswa = $status_stmt->fetchColumn() ?: 'Belum Lunas';

        $spp_stmt = $db->query("SELECT spp.nominal FROM tb_siswa_muhammadrifqisaifulloh s JOIN tb_spp_muhammadrifqisaifulloh spp ON s.id_spp = spp.id_spp WHERE s.nisn = '$siswa_nisn' LIMIT 1");
        $nominal_spp = $spp_stmt->fetchColumn() ?: 0;
    }

    // 2. Mengambil 5 riwayat pembayaran terbaru dari siswa login bersangkutan
    $recent_query = "SELECT p.*, spp.tahun, spp.nominal 
                     FROM tb_pembayaran_muhammadrifqisaifulloh p
                     JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                     WHERE p.nisn = :nisn 
                     ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC LIMIT 5";
    $recent_stmt = $db->prepare($recent_query);
    $recent_stmt->bindParam(":nisn", $siswa_nisn);
    $recent_stmt->execute();
}
?>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Dashboard Beranda</h4>
        <p class="text-muted small m-0">Ringkasan data pembayaran dan statistik sistem SPP</p>
    </div>
    <?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
        <a href="modules/pembayaran/tambah.php" class="btn btn-primary rounded-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Entri Transaksi Baru
        </a>
    <?php endif; ?>
</div>

<?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
    <!-- Ringkasan Kartu Statistik (Admin & Petugas) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-sm-6">
            <div class="card-stats stats-primary">
                <div class="card-stats-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="card-stats-title">Siswa</div>
                <div class="card-stats-value"><?= $count_siswa ?></div>
                <small class="text-muted">Siswa Terdaftar</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card-stats stats-success">
                <div class="card-stats-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="card-stats-title">Kelas</div>
                <div class="card-stats-value"><?= $count_kelas ?></div>
                <small class="text-muted">Total Ruang Kelas</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card-stats stats-info">
                <div class="card-stats-icon">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="card-stats-title">Petugas</div>
                <div class="card-stats-value"><?= $count_petugas ?></div>
                <small class="text-muted">Staf Petugas Aktif</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card-stats stats-warning">
                <div class="card-stats-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="card-stats-title">Dana SPP Masuk</div>
                <div class="card-stats-value" style="font-size: 1.35rem;">Rp <?= number_format($total_spp_collected, 0, ',', '.') ?></div>
                <small class="text-muted">Dari Transaksi Lunas</small>
            </div>
        </div>
    </div>

    <!-- Grafik dan Log Histori Terbaru -->
    <div class="row">
        <!-- Status Kelayakan SPP Siswa (Menggantikan Grafik Penerimaan) -->
        <div class="col-xl-6 mb-4">
            <div class="form-card py-4" style="min-height: 430px;">
                <h5 class="fw-700 border-bottom pb-2 mb-4"><i class="bi bi-pie-chart text-primary me-2"></i> Status Kelayakan SPP Siswa</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3">
                            <div class="bg-success rounded-3 p-2 d-inline-flex" style="--bs-bg-opacity: 0.15;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-600">Siswa Sudah Lunas</div>
                                <h3 class="fw-800 m-0 text-success"><?= $count_lunas ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3">
                            <div class="bg-danger rounded-3 p-2 d-inline-flex" style="--bs-bg-opacity: 0.15;">
                                <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-600">Siswa Belum Lunas</div>
                                <h3 class="fw-800 m-0 text-danger"><?= $count_belum_lunas ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visualisasi persentase kelayakan dengan progress bar -->
                <div class="mt-4 p-3 border rounded-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-700 text-secondary">Rasio Kelulusan SPP</span>
                        <span class="small fw-800 text-dark"><?= $pct_lunas ?>%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pct_lunas ?>%; border-radius: 10px;" aria-valuenow="<?= $pct_lunas ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Sebanyak <strong><?= $count_lunas ?></strong> dari <strong><?= $total_spp_siswa ?></strong> siswa terdaftar telah menyelesaikan seluruh tagihan pembayaran SPP secara tepat waktu.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel log histori transaksi pembayaran terbaru -->
        <div class="col-xl-6 mb-4">
            <div class="form-card py-4" style="min-height: 430px;">
                <h5 class="fw-700 border-bottom pb-2 mb-4"><i class="bi bi-receipt text-primary me-2"></i> Transaksi Pembayaran Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="small text-muted text-uppercase">
                                <th>No Transaksi</th>
                                <th>Siswa</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_stmt->rowCount() == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi pembayaran</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($r = $recent_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-600 text-secondary small"><?= htmlspecialchars($r['id_pembayaran']) ?></td>
                                        <td>
                                            <div class="fw-600 text-dark small"><?= htmlspecialchars($r['nama']) ?></div>
                                            <div class="text-muted small" style="font-size: 0.7rem;"><?= htmlspecialchars($r['nama_kelas']) ?></div>
                                        </td>
                                        <td class="small"><?= $r['tgl_bayar'] ? date('d-m-Y', strtotime($r['tgl_bayar'])) : '-' ?></td>
                                        <td>
                                            <?php if ($r['status'] === 'Sudah Lunas'): ?>
                                                <span class="badge bg-success">Lunas</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Ringkasan Statistik Khusus Level Siswa -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card-stats stats-success">
                <div class="card-stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="card-stats-title">Bulan Terbayar</div>
                <div class="card-stats-value"><?= $count_pembayaran_lunas ?> Bulan</div>
                <small class="text-muted">Total transaksi lunas</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stats stats-primary">
                <div class="card-stats-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="card-stats-title">Tarif SPP</div>
                <div class="card-stats-value">Rp <?= number_format($nominal_spp, 0, ',', '.') ?></div>
                <small class="text-muted">Tarif Bulanan</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stats <?= ($status_siswa === 'Sudah Lunas') ? 'stats-success' : 'stats-warning' ?>">
                <div class="card-stats-icon">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div class="card-stats-title">Status Cek Terakhir</div>
                <div class="card-stats-value"><?= htmlspecialchars($status_siswa) ?></div>
                <small class="text-muted">Status akun terupdate</small>
            </div>
        </div>
    </div>

    <!-- Riwayat Riwayat SPP Siswa Login -->
    <div class="form-card py-4">
        <h5 class="fw-700 border-bottom pb-2 mb-4"><i class="bi bi-clock-history text-primary me-2"></i> Histori Pembayaran Anda</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="small text-muted text-uppercase">
                        <th>No Transaksi</th>
                        <th>Tahun Ajaran</th>
                        <th>Tarif SPP</th>
                        <th>Tgl Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_stmt->rowCount() == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pembayaran SPP</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($r = $recent_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="fw-600 text-secondary small"><?= htmlspecialchars($r['id_pembayaran']) ?></td>
                                <td><?= htmlspecialchars($r['tahun']) ?></td>
                                <td>Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td>
                                <td><?= $r['tgl_bayar'] ? date('d-m-Y', strtotime($r['tgl_bayar'])) : '-' ?></td>
                                <td class="fw-600 text-success">Rp <?= number_format($r['jumlah_bayar'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($r['status'] === 'Sudah Lunas'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
require_once 'layout/footer.php';
?>
