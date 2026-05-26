<?php
/**
 * Delete Class Action
 * SPP Application (Aplikasi SPP)
 * 
 * Removes a class record from the database.
 */
session_start();
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

if ($kelas->delete($id)) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Data kelas berhasil dihapus.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal menghapus data kelas. Data mungkin masih digunakan di tabel lain.'
    ];
}

header("Location: index.php");
exit;
?>
