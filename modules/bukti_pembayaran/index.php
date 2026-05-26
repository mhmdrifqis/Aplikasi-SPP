<?php
/**
 * Modul Bukti Pembayaran
 * Aplikasi SPP
 * 
 * Menampilkan daftar transaksi pembayaran yang telah lunas agar kuitansi/bukti bayar dapat dicetak.
 */
$title = "Bukti Pembayaran";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);

$user_level = $_SESSION['level'] ?? 'siswa';
$siswa_nisn = $_SESSION['nisn'] ?? '';

// Membaca histori pembayaran berdasarkan level akses
if ($user_level === 'admin' || $user_level === 'petugas') {
    // Admin dan Petugas dapat melihat seluruh pembayaran yang berstatus lunas
    $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
              FROM tb_pembayaran_muhammadrifqisaifulloh p
              JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
              LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
              JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
              WHERE p.status = 'Sudah Lunas'
              ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
    $stmt = $db->query($query);
} else {
    // Siswa hanya dapat melihat pembayaran lunas miliknya sendiri
    $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
              FROM tb_pembayaran_muhammadrifqisaifulloh p
              JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
              LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
              JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
              WHERE p.nisn = :nisn AND p.status = 'Sudah Lunas'
              ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":nisn", $siswa_nisn);
    $stmt->execute();
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Cetak Bukti Pembayaran SPP</h4>
    <p class="text-muted small m-0">
        <?= ($user_level === 'siswa') ? 'Daftar bukti pembayaran SPP resmi Anda' : 'Daftar bukti kuitansi pembayaran SPP siswa' ?>
    </p>
</div>

<div class="table-responsive-custom">
    <table class="table table-hover table-custom datatable w-100">
        <thead>
            <tr>
                <th width="10%">ID Transaksi</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Tahun SPP</th>
                <th>Tanggal Bayar</th>
                <th>Jumlah Bayar</th>
                <th width="15%" class="text-center">Cetak Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
            ?>
            <tr>
                <td class="fw-600 text-secondary small"><?= htmlspecialchars($row['id_pembayaran']) ?></td>
                <td>
                    <div class="fw-700 text-dark small"><?= htmlspecialchars($row['nama']) ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;">NISN: <?= htmlspecialchars($row['nisn']) ?></div>
                </td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kelas']) ?></span></td>
                <td>Tahun <?= htmlspecialchars($row['tahun']) ?></td>
                <td class="small"><?= date('d-m-Y', strtotime($row['tgl_bayar'])) ?></td>
                <td class="fw-600 text-success">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                <td class="text-center">
                    <a href="../pembayaran/cetak.php?id=<?= urlencode($row['id_pembayaran']) ?>" class="btn btn-action btn-outline-info" title="Cetak Kuitansi" target="_blank">
                        <i class="bi bi-printer"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../../layout/footer.php';
?>
