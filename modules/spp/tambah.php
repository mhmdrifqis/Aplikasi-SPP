<?php
/**
 * Create SPP Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to insert a new SPP record into tb_spp_muhammadrifqisaifulloh.
 */
$title = "Tambah SPP";
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

$error = '';

if (isset($_POST['submit'])) {
    $id_spp = $_POST['id_spp'];
    $tahun = $_POST['tahun'];
    $nominal = $_POST['nominal'];

    if (empty($id_spp) || empty($tahun) || empty($nominal)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // Verify unique ID
        $exist = $spp->readOne($id_spp);
        if ($exist) {
            $error = "ID SPP sudah terdaftar! Gunakan ID yang lain.";
        } else {
            if ($spp->create($id_spp, $tahun, $nominal)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Tarif SPP baru berhasil ditambahkan.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menambahkan data SPP.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Tambah Ketetapan SPP</h4>
    <p class="text-muted small m-0">Masukkan ketentuan tarif SPP baru di bawah ini</p>
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
                    <label for="id_spp" class="form-label">ID SPP</label>
                    <input type="text" name="id_spp" id="id_spp" class="form-control" placeholder="Contoh: SPP6" required>
                </div>

                <div class="form-group mb-3">
                    <label for="tahun" class="form-label">Tahun Ajaran</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Contoh: 2029" min="2000" max="2100" required>
                </div>

                <div class="form-group mb-4">
                    <label for="nominal" class="form-label">Nominal Bulanan (Rupiah)</label>
                    <input type="number" name="nominal" id="nominal" class="form-control" placeholder="Contoh: 500000" min="0" required>
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
