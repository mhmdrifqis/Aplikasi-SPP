<?php
/**
 * View Officers Module
 * SPP Application (Aplikasi SPP)
 * 
 * Displays all system accounts (Administrators and Officers).
 */
$title = "Kelola Petugas";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Petugas.php';

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
$petugas = new Petugas($db);
$stmt = $petugas->read();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 m-0">Kelola Data Petugas / Staf</h4>
        <p class="text-muted small m-0">Manajemen akun pengguna sistem untuk administrator dan petugas piket</p>
    </div>
    <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Petugas
    </a>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>ID Pengguna</th>
                <th>Nama Petugas</th>
                <th>Username</th>
                <th>Level Akses</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                // Student logins are managed automatically through the Siswa module.
                // We display them here as read-only or omit them to prevent duplication.
                // Let's display all but only allow actions on admin/petugas.
                $is_siswa = ($row['level'] === 'siswa');
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="fw-600 text-secondary"><?= htmlspecialchars($row['id_petugas']) ?></td>
                <td class="fw-700 text-dark"><?= htmlspecialchars($row['nama_petugas']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td>
                    <?php if ($row['level'] === 'admin'): ?>
                        <span class="badge bg-danger">Administrator</span>
                    <?php elseif ($row['level'] === 'petugas'): ?>
                        <span class="badge bg-primary">Petugas SPP</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Siswa</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if (!$is_siswa): ?>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="edit.php?id=<?= urlencode($row['id_petugas']) ?>" class="btn btn-action btn-outline-primary" title="Edit Petugas">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <?php if ($row['id_petugas'] !== $_SESSION['id_user']): ?>
                                <a href="hapus.php?id=<?= urlencode($row['id_petugas']) ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun petugas ini?')" title="Hapus Petugas">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-action btn-outline-secondary" disabled title="Anda tidak dapat menghapus akun sendiri."><i class="bi bi-trash"></i></button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <span class="text-muted small italic">Dikelola di modul Siswa</span>
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
