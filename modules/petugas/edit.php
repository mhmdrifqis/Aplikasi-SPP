<?php
/**
 * Edit Officer Module
 * SPP Application (Aplikasi SPP)
 * 
 * Provides a form to modify an existing officer account.
 */
$title = "Edit Petugas";
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

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$petugas = new Petugas($db);
$row = $petugas->readOne($id);

if (!$row) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Akun petugas tidak ditemukan.'
    ];
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $new_id = $_POST['id_petugas'];
    $nama_petugas = $_POST['nama_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password']; // can be empty
    $level = $_POST['level'];

    if (empty($new_id) || empty($nama_petugas) || empty($username) || empty($level)) {
        $error = "Semua bidang input wajib diisi!";
    } else {
        // If ID is changing, check unique ID
        if ($new_id !== $id) {
            $exist_id = $petugas->readOne($new_id);
            if ($exist_id) {
                $error = "ID Petugas baru sudah terdaftar!";
            }
        }
        
        // If Username is changing, check uniqueness
        if ($username !== $row['username']) {
            $query_u = "SELECT id_petugas FROM tb_petugas_muhammadrifqisaifulloh WHERE username = :username LIMIT 1";
            $stmt_u = $db->prepare($query_u);
            $stmt_u->bindParam(":username", $username);
            $stmt_u->execute();
            if ($stmt_u->rowCount() > 0) {
                $error = "Username baru sudah digunakan! Pilih username lain.";
            }
        }

        if (empty($error)) {
            if ($petugas->update($id, $new_id, $username, $password, $nama_petugas, $level)) {
                // If updating logged-in account, refresh active session name/level
                if ($id === $_SESSION['id_user']) {
                    $_SESSION['nama'] = $nama_petugas;
                    $_SESSION['level'] = $level;
                }
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Akun petugas berhasil diperbarui.'
                ];
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal memperbarui akun petugas.";
            }
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Edit Akun Petugas</h4>
    <p class="text-muted small m-0">Ubah detail profil dan kredensial staf di bawah ini</p>
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
                    <input type="text" name="id_petugas" id="id_petugas" class="form-control" value="<?= htmlspecialchars($row['id_petugas']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="nama_petugas" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_petugas" id="nama_petugas" class="form-control" value="<?= htmlspecialchars($row['nama_petugas']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password Baru <span class="text-muted small fw-normal">(Kosongkan jika tidak ingin diubah)</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru">
                </div>

                <div class="form-group mb-4">
                    <label for="level" class="form-label">Level Hak Akses</label>
                    <select name="level" id="level" class="form-select" required>
                        <option value="admin" <?= ($row['level'] === 'admin') ? 'selected' : '' ?>>Administrator (Akses Penuh)</option>
                        <option value="petugas" <?= ($row['level'] === 'petugas') ? 'selected' : '' ?>>Petugas SPP (Piket Penerimaan)</option>
                    </select>
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
