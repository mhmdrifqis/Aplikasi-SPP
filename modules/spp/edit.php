<?php
/**
 * Edit SPP Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to modify an existing SPP record in tb_spp_muhammadrifqisaifulloh.
 */
$title = "Edit SPP";
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

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$spp = new Spp($db);
$row = $spp->readOne($id);

if (!$row) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Data SPP tidak ditemukan.'
    ];
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $new_id = $_POST['id_spp'];
    $tahun = $_POST['tahun'];
    $nominal = $_POST['nominal'];

    if (empty($new_id) || empty($tahun) || empty($nominal)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // If ID is changing, check uniqueness of new ID
        if ($new_id !== $id) {
            $exist = $spp->readOne($new_id);
            if ($exist) {
                $error = "ID SPP baru sudah terdaftar! Gunakan ID yang lain.";
            }
        }

        if (empty($error)) {
            if ($spp->update($id, $new_id, $tahun, $nominal)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data SPP berhasil diperbarui.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal memperbarui data SPP.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Edit Ketetapan SPP</h4>
    <p class="text-muted small m-0">Ubah detail ketentuan tarif SPP di bawah ini</p>
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
                    <input type="text" name="id_spp" id="id_spp" class="form-control" value="<?= htmlspecialchars($row['id_spp']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="tahun" class="form-label">Tahun Ajaran</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" value="<?= htmlspecialchars($row['tahun']) ?>" min="2000" max="2100" required>
                </div>

                <div class="form-group mb-4">
                    <label for="nominal" class="form-label">Nominal Bulanan (Rupiah)</label>
                    <input type="number" name="nominal" id="nominal" class="form-control" value="<?= htmlspecialchars($row['nominal']) ?>" min="0" required>
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
