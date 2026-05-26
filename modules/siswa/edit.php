<?php
/**
 * Edit Student Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to modify an existing student record in tb_siswa_muhammadrifqisaifulloh.
 */
$title = "Edit Siswa";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Siswa.php';
require_once '../../classes/Kelas.php';
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

$nisn = $_GET['nisn'] ?? '';
if (empty($nisn)) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$siswa = new Siswa($db);
$row = $siswa->readOne($nisn);

if (!$row) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Data siswa tidak ditemukan.'
    ];
    header("Location: index.php");
    exit;
}

// Load options
$kelas = new Kelas($db);
$list_kelas = $kelas->read();

$spp = new Spp($db);
$list_spp = $spp->read();

$error = '';

if (isset($_POST['submit'])) {
    $new_nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];

    if (empty($new_nisn) || empty($nis) || empty($nama) || empty($id_kelas) || empty($id_spp)) {
        $error = "NISN, NIS, Nama, Kelas, dan Ketentuan SPP wajib diisi!";
    } else {
        // If NISN is changing, verify new NISN is unique
        if ($new_nisn !== $nisn) {
            $exist = $siswa->readOne($new_nisn);
            if ($exist) {
                $error = "NISN baru $new_nisn sudah digunakan oleh siswa lain!";
            }
        }

        if (empty($error)) {
            if ($siswa->update($nisn, $new_nisn, $nis, $nama, $id_kelas, $alamat, $no_telp, $id_spp)) {
                
                // Keep the student user account username updated as well
                $query_u = "UPDATE tb_petugas_muhammadrifqisaifulloh 
                            SET username = :new_username, nama_petugas = :nama 
                            WHERE username = :old_username AND level = 'siswa'";
                $stmt_u = $db->prepare($query_u);
                $stmt_u->bindParam(":new_username", $nis);
                $stmt_u->bindParam(":nama", $nama);
                $stmt_u->bindParam(":old_username", $row['nis']);
                $stmt_u->execute();

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data siswa berhasil diperbarui.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal memperbarui data siswa.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Edit Data Siswa</h4>
    <p class="text-muted small m-0">Ubah detail profil siswa di bawah ini</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="form-card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="nisn" class="form-label">NISN (10 Digit)</label>
                        <input type="text" name="nisn" id="nisn" class="form-control" maxlength="10" value="<?= htmlspecialchars($row['nisn']) ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="nis" class="form-label">NIS (8 Digit)</label>
                        <input type="text" name="nis" id="nis" class="form-control" maxlength="8" value="<?= htmlspecialchars($row['nis']) ?>" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="id_kelas" class="form-label">Kelas & Jurusan</label>
                        <select name="id_kelas" id="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = $list_kelas->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= htmlspecialchars($k['id_kelas']) ?>" <?= ($k['id_kelas'] == $row['id_kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?> (<?= htmlspecialchars($k['komp_keahlian']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="id_spp" class="form-label">Ketentuan SPP (Tahun Ajaran)</label>
                        <select name="id_spp" id="id_spp" class="form-select" required>
                            <option value="">-- Pilih Ketentuan SPP --</option>
                            <?php while ($s = $list_spp->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= htmlspecialchars($s['id_spp']) ?>" <?= ($s['id_spp'] == $row['id_spp']) ? 'selected' : '' ?>>
                                    Tahun <?= htmlspecialchars($s['tahun']) ?> - Rp <?= number_format($s['nominal'], 0, ',', '.') ?>/bulan
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="no_telp" class="form-label">No. Telepon / HP</label>
                    <input type="text" name="no_telp" id="no_telp" class="form-control" maxlength="13" value="<?= htmlspecialchars($row['no_telp']) ?>">
                </div>

                <div class="form-group mb-4">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3"><?= htmlspecialchars($row['alamat']) ?></textarea>
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
