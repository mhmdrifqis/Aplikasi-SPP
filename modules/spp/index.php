<?php
/**
 * View SPP Module
 * SPP Application (Aplikasi SPP)
 * 
 * Renders a list of all SPP rate policies in a data table.
 */
$title = "Kelola SPP";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Spp.php';

// Verify admin authorization
if ($_SESSION['level'] !== 'admin') {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Anda tidak memiliki hak akses untuk halaman ini!'
    ];
    header("Location: ../../index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$spp = new Spp($db);
$stmt = $spp->read();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Kelola Data SPP</h4>
        <p class="text-muted small m-0">Manajemen ketetapan nilai nominal SPP tahunan sekolah</p>
    </div>
    <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah SPP
    </a>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>ID SPP</th>
                <th>Tahun</th>
                <th>Nominal Bulanan</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="fw-600 text-secondary"><?= htmlspecialchars($row['id_spp']) ?></td>
                <td class="fw-700 text-dark"><?= htmlspecialchars($row['tahun']) ?></td>
                <td class="fw-600 text-primary">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="edit.php?id=<?= urlencode($row['id_spp']) ?>" class="btn btn-action btn-outline-primary" title="Edit SPP">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="hapus.php?id=<?= urlencode($row['id_spp']) ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tarif SPP ini? Semua data pembayaran dan data siswa terkait tarif ini juga akan ikut terpengaruh.')" title="Hapus SPP">
                            <i class="bi bi-trash"></i>
                        </a>
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
