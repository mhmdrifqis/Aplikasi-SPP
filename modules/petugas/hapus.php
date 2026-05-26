<?php
/**
 * Delete Officer Action
 * SPP Application (Aplikasi SPP)
 * 
 * Removes an administrative/officer account.
 */
session_start();
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

// Cannot delete self
if ($id === $_SESSION['id_user']) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Anda tidak dapat menghapus akun Anda sendiri!'
    ];
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$petugas = new Petugas($db);

if ($petugas->delete($id)) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Akun petugas berhasil dihapus.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal menghapus akun petugas.'
    ];
}

header("Location: index.php");
exit;
?>
