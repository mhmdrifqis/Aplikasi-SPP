<?php
/**
 * Delete Student Action
 * SPP Application (Aplikasi SPP)
 * 
 * Removes a student record and deletes the corresponding login account.
 */
session_start();
require_once '../../config/Database.php';
require_once '../../classes/Siswa.php';

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

// Fetch details first to get the NIS (username of their login account)
$siswa_data = $siswa->readOne($nisn);

if ($siswa_data) {
    $nis = $siswa_data['nis'];

    try {
        $db->beginTransaction();

        // 1. Delete student profile
        $siswa->delete($nisn);

        // 2. Delete login account in tb_petugas
        $query_p = "DELETE FROM tb_petugas_muhammadrifqisaifulloh WHERE username = :username AND level = 'siswa'";
        $stmt_p = $db->prepare($query_p);
        $stmt_p->bindParam(":username", $nis);
        $stmt_p->execute();

        $db->commit();
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Data siswa beserta akun login berhasil dihapus.'
        ];
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Gagal menghapus data siswa.'
        ];
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Data siswa tidak ditemukan.'
    ];
}

header("Location: index.php");
exit;
?>
