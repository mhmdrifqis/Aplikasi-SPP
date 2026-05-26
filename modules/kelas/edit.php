<?php
/**
 * Edit Class Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to modify an existing class record in tb_kelas_muhammadrifqisaifulloh.
 */
$title = "Edit Kelas";
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

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$kelas = new Kelas($db);
$row = $kelas->readOne($id);

if (!$row) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Data kelas tidak ditemukan.'
    ];
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $new_id = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $komp_keahlian = $_POST['komp_keahlian'];

    if (empty($new_id) || empty($nama_kelas) || empty($komp_keahlian)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // If ID is changing, check uniqueness of the new ID
        if ($new_id !== $id) {
            $exist = $kelas->readOne($new_id);
            if ($exist) {
                $error = "ID Kelas baru sudah terdaftar! Gunakan ID yang lain.";
            }
        }

        if (empty($error)) {
            if ($kelas->update($id, $new_id, $nama_kelas, $komp_keahlian)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data kelas berhasil diperbarui.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal memperbarui data kelas.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Edit Data Kelas</h4>
    <p class="text-muted small m-0">Ubah detail kelas di bawah ini</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="form-card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group mb-3">
                    <label for="id_kelas" class="form-label">ID Kelas</label>
                    <input type="text" name="id_kelas" id="id_kelas" class="form-control" value="<?= htmlspecialchars($row['id_kelas']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="nama_kelas" class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" value="<?= htmlspecialchars($row['nama_kelas']) ?>" required>
                </div>

                <div class="form-group mb-4">
                    <label for="komp_keahlian" class="form-label">Kompetensi Keahlian (Jurusan)</label>
                    <input type="text" name="komp_keahlian" id="komp_keahlian" class="form-control" value="<?= htmlspecialchars($row['komp_keahlian']) ?>" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../layout/footer.php';
?>
