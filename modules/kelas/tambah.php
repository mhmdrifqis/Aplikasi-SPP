<?php
/**
 * Create Class Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to insert a new class record into tb_kelas_muhammadrifqisaifulloh.
 */
$title = "Tambah Kelas";
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

$error = '';

if (isset($_POST['submit'])) {
    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $komp_keahlian = $_POST['komp_keahlian'];

    if (empty($id_kelas) || empty($nama_kelas) || empty($komp_keahlian)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // Verify unique ID
        $exist = $kelas->readOne($id_kelas);
        if ($exist) {
            $error = "ID Kelas sudah terdaftar! Gunakan ID yang lain.";
        } else {
            if ($kelas->create($id_kelas, $nama_kelas, $komp_keahlian)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data kelas berhasil ditambahkan.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menambahkan data kelas.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Tambah Data Kelas</h4>
    <p class="text-muted small m-0">Masukkan detail kelas baru di bawah ini</p>
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
                    <input type="text" name="id_kelas" id="id_kelas" class="form-control" placeholder="Contoh: K6" required>
                </div>

                <div class="form-group mb-3">
                    <label for="nama_kelas" class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" placeholder="Contoh: XII RPL 3" required>
                </div>

                <div class="form-group mb-4">
                    <label for="komp_keahlian" class="form-label">Kompetensi Keahlian (Jurusan)</label>
                    <input type="text" name="komp_keahlian" id="komp_keahlian" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Data
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
