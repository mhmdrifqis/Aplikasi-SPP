<?php
/**
 * Delete SPP Action
 * SPP Application (Aplikasi SPP)
 * 
 * Removes an SPP record from the database.
 */
session_start();
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

if ($spp->delete($id)) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Tarif SPP berhasil dihapus.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal menghapus tarif SPP. Data masih berelasi di tabel lain.'
    ];
}

header("Location: index.php");
exit;
?>
