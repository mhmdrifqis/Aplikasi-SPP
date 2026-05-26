<?php
/**
 * View Payments Module
 * SPP Application (Aplikasi SPP)
 * 
 * Renders the payment transaction history. Admins/officers see all logs,
 * while students see only their own payment records.
 */
$title = "Histori Pembayaran";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);

$user_level = $_SESSION['level'] ?? 'siswa';
$siswa_nisn = $_SESSION['nisn'] ?? '';

// Ambil tanggal filter jika ada
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
$filter_status = $_GET['filter_status'] ?? ''; // '' = semua, 'Sudah Lunas', 'Belum Lunas'

// Fetch data berdasarkan level akses dan filter
if ($user_level === 'admin' || $user_level === 'petugas') {
    $hasTanggal = !empty($tgl_mulai) && !empty($tgl_selesai);
    $hasStatus  = in_array($filter_status, ['Sudah Lunas', 'Belum Lunas']);

    if ($hasTanggal && $hasStatus) {
        $stmt = $pembayaran->readFilteredByStatus($tgl_mulai, $tgl_selesai, $filter_status);
    } elseif ($hasTanggal) {
        $stmt = $pembayaran->readFiltered($tgl_mulai, $tgl_selesai);
    } elseif ($hasStatus) {
        $stmt = $pembayaran->readAllByStatus($filter_status);
    } else {
        $stmt = $pembayaran->readAll();
    }
} else {
    $stmt = $pembayaran->readBySiswa($siswa_nisn);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Histori Pembayaran SPP</h4>
        <p class="text-muted small m-0">
            <?= ($user_level === 'siswa') ? 'Daftar riwayat pembayaran SPP Anda' : 'Manajemen log transaksi pembayaran SPP siswa sekolah' ?>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
            <!-- Dropdown Cetak Laporan dengan filter status -->
            <div class="btn-group shadow-sm" role="group">
                <a href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=" class="btn btn-secondary rounded-start-3" target="_blank" title="Cetak semua transaksi">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header"><i class="bi bi-funnel me-1"></i>Filter Status Laporan</h6></li>
                    <li>
                        <a class="dropdown-item" href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=" target="_blank">
                            <i class="bi bi-list-ul me-2 text-secondary"></i>Semua Transaksi
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=Sudah+Lunas" target="_blank">
                            <i class="bi bi-check-circle me-2 text-success"></i>Sudah Lunas
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=Belum+Lunas" target="_blank">
                            <i class="bi bi-x-circle me-2 text-danger"></i>Belum Lunas
                        </a>
                    </li>
                </ul>
            </div>
            <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
    <!-- Form Filter Rentang Tanggal -->
    <div class="form-card mb-4 py-3 px-4">
        <form action="" method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label for="tgl_mulai" class="form-label mb-1 small text-muted">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control py-1 px-2" value="<?= htmlspecialchars($tgl_mulai) ?>">
            </div>
            <div class="col-md-3">
                <label for="tgl_selesai" class="form-label mb-1 small text-muted">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control py-1 px-2" value="<?= htmlspecialchars($tgl_selesai) ?>">
            </div>
            <div class="col-md-3">
                <label for="filter_status" class="form-label mb-1 small text-muted">Status Pembayaran</label>
                <select name="filter_status" id="filter_status" class="form-select py-1 px-2">
                    <option value="" <?= $filter_status === '' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="Sudah Lunas" <?= $filter_status === 'Sudah Lunas' ? 'selected' : '' ?>>Sudah Lunas</option>
                    <option value="Belum Lunas" <?= $filter_status === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50 py-2">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="index.php" class="btn btn-secondary w-50 py-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Tahun SPP</th>
                <th>Tanggal Bayar</th>
                <th>Jumlah Bayar</th>
                <th>Status</th>
                <th width="15%" class="text-center no-print">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
            ?>
            <tr>
                <td class="fw-600 text-secondary small"><?= htmlspecialchars($row['id_pembayaran']) ?></td>
                <td>
                    <div class="fw-700 text-dark small"><?= htmlspecialchars($row['nama']) ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;">NISN: <?= htmlspecialchars($row['nisn']) ?></div>
                </td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kelas']) ?></span></td>
                <td>Tahun <?= htmlspecialchars($row['tahun']) ?></td>
                <td class="small"><?= $row['tgl_bayar'] ? date('d-m-Y', strtotime($row['tgl_bayar'])) : '-' ?></td>
                <td class="fw-600 text-success">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                <td>
                    <?php if ($row['status'] === 'Sudah Lunas'): ?>
                        <span class="badge bg-success">Sudah Lunas</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Belum Lunas</span>
                    <?php endif; ?>
                </td>
                <td class="text-center no-print">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="cetak.php?id=<?= urlencode($row['id_pembayaran']) ?>" class="btn btn-action btn-outline-info" title="Cetak Kuitansi" target="_blank">
                            <i class="bi bi-printer"></i>
                        </a>
                        <?php if ($user_level === 'admin' || $user_level === 'petugas'): ?>
                            <a href="edit.php?id=<?= urlencode($row['id_pembayaran']) ?>" class="btn btn-action btn-outline-primary" title="Ubah Transaksi">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($user_level === 'admin'): ?>
                            <a href="hapus.php?id=<?= urlencode($row['id_pembayaran']) ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus transaksi pembayaran ini?')" title="Hapus Transaksi">
                                <i class="bi bi-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../../layout/footer.php';
?>
