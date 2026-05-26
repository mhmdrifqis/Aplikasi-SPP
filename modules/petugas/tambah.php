<?php
/**
 * Create Officer Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to insert a new administrative or officer account.
 */
$title = "Tambah Petugas";
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

$error = '';

if (isset($_POST['submit'])) {
    $id_petugas = $_POST['id_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_petugas = $_POST['nama_petugas'];
    $level = $_POST['level'];

    if (empty($id_petugas) || empty($username) || empty($password) || empty($nama_petugas) || empty($level)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // Verify unique ID & Username
        $exist_id = $petugas->readOne($id_petugas);
        
        $query_u = "SELECT id_petugas FROM tb_petugas_muhammadrifqisaifulloh WHERE username = :username LIMIT 1";
        $stmt_u = $db->prepare($query_u);
        $stmt_u->bindParam(":username", $username);
        $stmt_u->execute();
        $exist_username = $stmt_u->fetch(PDO::FETCH_ASSOC);

        if ($exist_id) {
            $error = "ID Petugas sudah terdaftar!";
        } elseif ($exist_username) {
            $error = "Username sudah digunakan! Pilih username lain.";
        } else {
            if ($petugas->create($id_petugas, $username, $password, $nama_petugas, $level)) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Akun petugas baru berhasil didaftarkan.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mendaftarkan akun petugas.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Tambah Akun Petugas</h4>
    <p class="text-muted small m-0">Buat akun login staf administrasi baru di bawah ini</p>
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
                    <label for="id_petugas" class="form-label">ID Petugas</label>
                    <input type="text" name="id_petugas" id="id_petugas" class="form-control" placeholder="Contoh: P6" required>
                </div>

                <div class="form-group mb-3">
                    <label for="nama_petugas" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_petugas" id="nama_petugas" class="form-control" placeholder="Masukkan nama lengkap petugas" required>
                </div>

                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username untuk login" required>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password akun" required>
                </div>

                <div class="form-group mb-4">
                    <label for="level" class="form-label">Level Hak Akses</label>
                    <select name="level" id="level" class="form-select" required>
                        <option value="">-- Pilih Level Akses --</option>
                        <option value="admin">Administrator (Akses Penuh)</option>
                        <option value="petugas">Petugas SPP (Piket Penerimaan)</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Daftarkan Petugas
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
