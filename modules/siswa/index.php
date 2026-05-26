<?php
/**
 * View Students Module
 * SPP Application (Aplikasi SPP)
 * 
 * Renders a list of all student entities, displaying their class, SPP, and contact details.
 */
$title = "Kelola Siswa";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Siswa.php';

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
$siswa = new Siswa($db);
$stmt = $siswa->read();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Kelola Data Siswa</h4>
        <p class="text-muted small m-0">Manajemen data profil siswa, penetapan kelas dan SPP tahunan</p>
    </div>
    <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </a>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>NISN / NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas (Jurusan)</th>
                <th>Tarif SPP</th>
                <th>No Telp</th>
                <th>Alamat</th>
                <th width="12%" class="text-center">Aksi</th>
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
                    <div class="fw-700 text-dark small"><?= htmlspecialchars($row['nisn']) ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;"><?= htmlspecialchars($row['nis']) ?></div>
                </td>
                <td class="fw-600"><?= htmlspecialchars($row['nama']) ?></td>
                <td>
                    <span class="badge bg-primary"><?= htmlspecialchars($row['kelas_display']) ?></span>
                    <div class="text-muted small mt-1" style="font-size: 0.7rem;"><?= htmlspecialchars($row['komp_keahlian']) ?></div>
                </td>
                <td>
                    <div class="fw-600 text-dark small">Tahun <?= htmlspecialchars($row['tahun']) ?></div>
                    <div class="text-primary fw-600 small">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></div>
                </td>
                <td class="small"><?= htmlspecialchars($row['no_telp']) ?></td>
                <td class="small text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['alamat']) ?>">
                    <?= htmlspecialchars($row['alamat']) ?>
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="edit.php?nisn=<?= urlencode($row['nisn']) ?>" class="btn btn-action btn-outline-primary" title="Edit Siswa">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="hapus.php?nisn=<?= urlencode($row['nisn']) ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Semua riwayat pembayaran siswa bersangkutan akan ikut terhapus secara permanen.')" title="Hapus Siswa">
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
