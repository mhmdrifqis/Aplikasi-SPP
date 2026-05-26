<?php
/**
 * Handler Logout Akun
 * Aplikasi SPP
 * 
 * Menghapus data session login dan mengalihkan pengguna kembali ke halaman utama login.
 */
session_start();

// Mengosongkan data session dan menghancurkannya
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Memulai session baru untuk menampung notifikasi sukses logout
session_start();
$_SESSION['alert'] = [
    'type' => 'info',
    'message' => 'Anda telah berhasil logout.'
];

header("Location: login.php");
exit;
?>
