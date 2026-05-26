<?php
/**
 * Delete / Cancel Payment Action
 * SPP Application (Aplikasi SPP)
 * 
 * Cancels a student SPP payment transaction and updates the checking status.
 */
session_start();
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

// Verify admin authorization
if ($_SESSION['level'] !== 'admin') {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Anda tidak memiliki hak akses untuk membatalkan transaksi!'
    ];
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);

if ($pembayaran->delete($id)) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Transaksi pembayaran berhasil dibatalkan dan dihapus.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal membatalkan transaksi pembayaran.'
    ];
}

header("Location: index.php");
exit;
?>
