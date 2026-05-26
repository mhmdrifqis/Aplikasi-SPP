<?php
/**
 * Modul Cetak Kuitansi Pembayaran
 * Aplikasi SPP
 * 
 * Merender struk/bukti kuitansi pembayaran SPP yang siap cetak (printer-friendly).
 */
session_start();
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

// Validasi status login
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? '';
if (empty($id)) {
    echo "ID Transaksi tidak ditemukan!";
    exit;
}

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);
$row = $pembayaran->readOne($id);

if (!$row) {
    echo "Transaksi tidak ditemukan!";
    exit;
}

// Validasi agar siswa hanya dapat mencetak kuitansi miliknya sendiri
if ($_SESSION['level'] === 'siswa' && $_SESSION['nisn'] !== $row['nisn']) {
    echo "Anda tidak memiliki hak akses untuk mencetak kuitansi ini!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran SPP - <?= htmlspecialchars($row['id_pembayaran']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Theme CSS kustom -->
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            padding: 30px;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
        }
        @media print {
            @page {
                margin: 0;
            }
            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 1.5cm !important;
            }
            .print-container {
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Tombol Aksi (Disembunyikan saat mencetak kuitansi) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Histori
            </a>
            <button onclick="window.print();" class="btn btn-primary btn-sm">
                <i class="bi bi-printer"></i> Cetak Kuitansi
            </button>
        </div>

        <!-- Box Kuitansi -->
        <div class="invoice-box shadow-sm">
            <div class="invoice-header d-flex justify-content-between align-items-center flex-wrap pb-3">
                <div>
                    <h3 class="fw-800 m-0 text-primary">KUITANSI PEMBAYARAN SPP</h3>
                    <p class="text-muted small m-0">SMK Negeri Indonesia</p>
                </div>
                <div class="text-md-end mt-2 mt-md-0">
                    <h5 class="fw-700 m-0 text-dark"><?= htmlspecialchars($row['id_pembayaran']) ?></h5>
                    <p class="text-muted small m-0">Tanggal Cetak: <?= date('d M Y') ?></p>
                </div>
            </div>

            <div class="row my-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-muted small text-uppercase fw-700 mb-2">Informasi Siswa:</h6>
                    <table class="table table-borderless table-sm small">
                        <tr>
                            <td width="30%" class="fw-600 text-dark">Nama Siswa</td>
                            <td>: <?= htmlspecialchars($row['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-600 text-dark">NISN / NIS</td>
                            <td>: <?= htmlspecialchars($row['nisn']) ?> / <?= htmlspecialchars($row['nis']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-600 text-dark">Kelas</td>
                            <td>: <?= htmlspecialchars($row['nama_kelas']) ?> (<?= htmlspecialchars($row['komp_keahlian']) ?>)</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase fw-700 mb-2">Informasi Transaksi:</h6>
                    <table class="table table-borderless table-sm small">
                        <tr>
                            <td width="40%" class="fw-600 text-dark">Tanggal Pembayaran</td>
                            <td>: <?= $row['tgl_bayar'] ? date('d F Y', strtotime($row['tgl_bayar'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-600 text-dark">Batas Pembayaran</td>
                            <td>: <?= $row['batas_pembayaran'] ? date('d F Y', strtotime($row['batas_pembayaran'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-600 text-dark">Lama Bulan SPP</td>
                            <td>: <?= htmlspecialchars($row['jumlah_bulan']) ?> Bulan</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Detail Biaya -->
            <table class="table table-striped align-middle print-table my-4">
                <thead>
                    <tr class="table-dark">
                        <th width="10%">No</th>
                        <th>Keterangan Pembayaran</th>
                        <th>Tahun SPP</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="fw-700 text-dark">Uang SPP Bulanan</div>
                            <div class="text-muted small">Pembayaran untuk <?= htmlspecialchars($row['jumlah_bulan']) ?> bulan</div>
                        </td>
                        <td>Tahun <?= htmlspecialchars($row['tahun']) ?></td>
                        <td class="text-end fw-700 text-dark">Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-700 text-dark">Total Tagihan</td>
                        <td class="text-end fw-800 text-primary fs-5">Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-700 text-dark">Jumlah Uang Tunai</td>
                        <td class="text-end fw-700 text-dark">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-700 text-dark">Kembalian</td>
                        <td class="text-end fw-700 text-success">Rp <?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="row mt-5 pt-4 border-top">
                <div class="col-8">
                    <p class="small text-muted mb-0">Catatan:</p>
                    <p class="small text-muted italic">Kuitansi ini dicetak secara komputerisasi sebagai bukti pembayaran SPP yang sah.</p>
                </div>
                <div class="col-4 text-center">
                    <p class="small text-dark mb-5">Petugas Penerima,</p>
                    <p class="fw-700 text-dark mb-0 border-bottom d-inline-block px-3"><?= htmlspecialchars($_SESSION['nama'] ?? 'Petugas SPP') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Memicu pencetakan otomatis setelah halaman dimuat -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
