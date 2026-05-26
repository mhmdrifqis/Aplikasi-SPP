<?php
/**
 * Create Student Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to insert a new student record into tb_siswa_muhammadrifqisaifulloh.
 */
$title = "Tambah Siswa";
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

$database = new Database();
$db = $database->getConnection();
$siswa = new Siswa($db);

// Load options
$kelas = new Kelas($db);
$list_kelas = $kelas->read();

$spp = new Spp($db);
$list_spp = $spp->read();

$error = '';

if (isset($_POST['submit'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];

    if (empty($nisn) || empty($nis) || empty($nama) || empty($id_kelas) || empty($id_spp)) {
        $error = "NISN, NIS, Nama, Kelas, dan Ketentuan SPP wajib diisi!";
    } else {
        // Verify uniqueness
        $exist = $siswa->readOne($nisn);
        if ($exist) {
            $error = "Siswa dengan NISN $nisn sudah terdaftar!";
        } else {
            if ($siswa->create($nisn, $nis, $nama, $id_kelas, $alamat, $no_telp, $id_spp)) {
                
                // Automatically create a default account in tb_petugas for the student so they can login!
                // Username will be their 'nis', password will default to their 'nis' as well.
                // This makes student access extremely smooth.
                require_once '../../classes/Petugas.php';
                $petugas = new Petugas($db);
                
                // Check if user already exists
                $query_p = "SELECT username FROM tb_petugas_muhammadrifqisaifulloh WHERE username = :username LIMIT 1";
                $stmt_p = $db->prepare($query_p);
                $stmt_p->bindParam(":username", $nis);
                $stmt_p->execute();
                
                if ($stmt_p->rowCount() == 0) {
                    // ID for student user: 'US'+nis
                    $id_petugas_siswa = "US" . substr($nis, 0, 9);
                    $petugas->create($id_petugas_siswa, $nis, $nis, $nama, 'siswa');
                }

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data siswa berhasil ditambahkan. Akun login siswa otomatis dibuat dengan username dan password: ' . htmlspecialchars($nis)
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menambahkan data siswa. Silakan periksa format input Anda.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Tambah Data Siswa</h4>
    <p class="text-muted small m-0">Masukkan detail profil siswa baru di bawah ini</p>
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
                        <input type="text" name="nisn" id="nisn" class="form-control" maxlength="10" placeholder="Contoh: 0054321098" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="nis" class="form-label">NIS (8 Digit)</label>
                        <input type="text" name="nis" id="nis" class="form-control" maxlength="8" placeholder="Contoh: 18191001" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap siswa" required>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="id_kelas" class="form-label">Kelas & Jurusan</label>
                        <select name="id_kelas" id="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = $list_kelas->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= htmlspecialchars($k['id_kelas']) ?>">
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
                                <option value="<?= htmlspecialchars($s['id_spp']) ?>">
                                    Tahun <?= htmlspecialchars($s['tahun']) ?> - Rp <?= number_format($s['nominal'], 0, ',', '.') ?>/bulan
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="no_telp" class="form-label">No. Telepon / HP</label>
                    <input type="text" name="no_telp" id="no_telp" class="form-control" maxlength="13" placeholder="Contoh: 081234567890">
                </div>

                <div class="form-group mb-4">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap rumah siswa"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Siswa
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
