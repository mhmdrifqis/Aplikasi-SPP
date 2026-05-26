<?php
/**
 * View Classes Module
 * SPP Application (Aplikasi SPP)
 * 
 * Renders a list of all class entities in a premium data table.
 */
$title = "Kelola Kelas";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Kelas.php';

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
$kelas = new Kelas($db);
$stmt = $kelas->read();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Kelola Data Kelas</h4>
        <p class="text-muted small m-0">Manajemen data kelas dan kompetensi keahlian jurusan</p>
    </div>
    <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
    </a>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>ID Kelas</th>
                <th>Nama Kelas</th>
                <th>Kompetensi Keahlian</th>
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
                <td class="fw-600 text-secondary"><?= htmlspecialchars($row['id_kelas']) ?></td>
                <td class="fw-700 text-dark"><?= htmlspecialchars($row['nama_kelas']) ?></td>
                <td><?= htmlspecialchars($row['komp_keahlian']) ?></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="edit.php?id=<?= urlencode($row['id_kelas']) ?>" class="btn btn-action btn-outline-primary" title="Edit Kelas">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="hapus.php?id=<?= urlencode($row['id_kelas']) ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Tindakan ini juga akan menghapus data siswa di dalam kelas tersebut.')" title="Hapus Kelas">
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
