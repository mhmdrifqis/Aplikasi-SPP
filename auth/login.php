<?php
/**
 * Halaman Login Autentikasi
 * Aplikasi SPP
 * 
 * Melakukan verifikasi kredensial login pengguna terhadap tabel tb_petugas_muhammadrifqisaifulloh
 * dan menyetel session global.
 */
session_start();
require_once '../config/Database.php';
require_once '../classes/Petugas.php';

$database = new Database();
$db = $database->getConnection();
$petugas = new Petugas($db);

// Pengalihan otomatis jika pengguna sudah login sebelumnya
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        // Melakukan verifikasi kredensial masuk
        $user = $petugas->login($username, $password);
        if ($user) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_petugas'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama_petugas'];
            $_SESSION['level'] = $user['level']; // 'admin', 'petugas', 'siswa'

            // Jika masuk sebagai siswa, cari NISN dari tb_siswa berdasarkan nama
            if ($user['level'] === 'siswa') {
                $query_siswa = "SELECT nisn FROM tb_siswa_muhammadrifqisaifulloh WHERE nama = :nama LIMIT 1";
                $stmt_siswa = $db->prepare($query_siswa);
                $stmt_siswa->bindParam(":nama", $user['nama_petugas']);
                $stmt_siswa->execute();
                $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['nisn'] = $siswa ? $siswa['nisn'] : '';
            }

            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Login berhasil! Selamat datang ' . $user['nama_petugas'] . '.'
            ];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi SPP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Premium stylesheet kustom -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .login-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .login-input-group {
            border: 1px solid #dbe2ec;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease-in-out;
            background-color: #fff;
            flex-wrap: nowrap !important;
        }
        .login-input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .login-input-group .input-group-text {
            background-color: #f8fafc;
            border: none;
            border-right: 1px solid #e2e8f0;
            color: #64748b;
            padding-left: 20px;
            padding-right: 20px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-input-group .form-control {
            border: none;
            padding: 14px 20px;
            font-size: 0.95rem;
            background-color: #fff;
            color: #1e293b;
            flex: 1 1 auto !important;
            width: 1% !important;
        }
        .login-input-group .form-control:focus {
            box-shadow: none !important;
            border: none !important;
            background-color: #fff !important;
        }
        .login-input-group .form-control::placeholder {
            color: #94a3b8;
            opacity: 0.8;
        }
    </style>
</head>
<body class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="sidebar-logo mx-auto d-inline-flex justify-content-center align-items-center">
                <i class="fas fa-wallet" style="font-size: 1.5rem;"></i>
            </div>
            <h4 class="fw-800 mt-3 mb-1 text-dark">Aplikasi SPP</h4>
            <p class="text-muted small">Silakan masuk ke dalam akun Anda</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group mb-3">
                <label for="username" class="login-label">Username</label>
                <div class="input-group login-input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username admin" required autofocus>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="login-label">Password</label>
                <div class="input-group login-input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-700">
                Masuk <i class="fas fa-sign-in-alt ms-1"></i>
            </button>
        </form>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
