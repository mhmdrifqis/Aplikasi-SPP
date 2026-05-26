<?php
/**
 * Modul Cek Pembayaran
 * Aplikasi SPP
 * 
 * Menampilkan status kelayakan pembayaran terakhir setiap siswa (Lunas / Belum Lunas).
 */
$title = "Cek Pembayaran";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

// Validasi otorisasi (Hanya Admin dan Petugas yang dapat melihat halaman ini)
if ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'petugas') {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Anda tidak memiliki hak akses untuk halaman ini!'
    ];
    header("Location: ../../index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);
$stmt = $pembayaran->readCheckStatus();
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Cek Status Pembayaran SPP</h4>
    <p class="text-muted small m-0">Informasi status pelunasan SPP terakhir untuk masing-masing siswa</p>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Siswa (NISN)</th>
                <th>Kelas</th>
                <th>No Telepon</th>
                <th>Bulan Terbayar</th>
                <th>Tanggal Bayar Terakhir</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <div class="fw-700 text-dark small"><?= htmlspecialchars($row['nama']) ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;">NISN: <?= htmlspecialchars($row['nisn']) ?></div>
                </td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kelas']) ?></span></td>
                <td class="small"><?= htmlspecialchars($row['no_telp'] ?: '-') ?></td>
                <td class="fw-600 text-dark"><?= htmlspecialchars($row['jumlah_bulan'] ?: '0') ?> Bulan</td>
                <td class="small"><?= $row['tgl_terakhir_bayar'] ? date('d-m-Y', strtotime($row['tgl_terakhir_bayar'])) : 'Belum pernah membayar' ?></td>
                <td>
                    <?php if ($row['status_pembayaran'] === 'Sudah Lunas'): ?>
                        <span class="badge bg-success">Sudah Lunas</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Belum Lunas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../../layout/footer.php';
?>
