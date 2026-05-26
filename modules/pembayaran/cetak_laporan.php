<?php
/**
 * Modul Cetak Laporan Rekapitulasi Pembayaran SPP
 * Aplikasi SPP
 * 
 * Memuat laporan transaksi keuangan pembayaran SPP berdasarkan rentang tanggal
 * filter atau rekap seluruh pembayaran jika tanpa filter.
 */
session_start();
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

// Validasi status login
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Validasi hak akses (Hanya Admin dan Petugas yang bisa cetak laporan rekap)
if ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'petugas') {
    echo "Anda tidak memiliki hak akses untuk mencetak laporan ini!";
    exit;
}

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);

// Membaca tanggal filter
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
$filter_status = $_GET['filter_status'] ?? ''; // '' = semua, 'Sudah Lunas', 'Belum Lunas'

// Label status untuk ditampilkan di laporan
$status_label = match($filter_status) {
    'Sudah Lunas' => 'Sudah Lunas',
    'Belum Lunas' => 'Belum Lunas',
    default       => 'Semua Status',
};

// Memuat data transaksi filter status lunas dan belum lunas
$hasTanggal = !empty($tgl_mulai) && !empty($tgl_selesai);
$hasStatus  = in_array($filter_status, ['Sudah Lunas', 'Belum Lunas']);

if ($hasTanggal && $hasStatus) {
    $stmt = $pembayaran->readFilteredByStatus($tgl_mulai, $tgl_selesai, $filter_status);
    $periode_text = date('d-m-Y', strtotime($tgl_mulai)) . ' s.d. ' . date('d-m-Y', strtotime($tgl_selesai));
} elseif ($hasTanggal) {
    $stmt = $pembayaran->readFiltered($tgl_mulai, $tgl_selesai);
    $periode_text = date('d-m-Y', strtotime($tgl_mulai)) . ' s.d. ' . date('d-m-Y', strtotime($tgl_selesai));
} elseif ($hasStatus) {
    $stmt = $pembayaran->readAllByStatus($filter_status);
    $periode_text = 'Semua Periode';
} else {
    $stmt = $pembayaran->readAll();
    $periode_text = 'Semua Periode';
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_rows = count($rows);
$total_revenue = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penerimaan SPP - <?= htmlspecialchars($periode_text) ?> | <?= htmlspecialchars($status_label) ?></title>
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
            font-family: 'Inter', sans-serif;
        }
        .print-container {
            max-width: 900px;
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
        <!-- Tombol Aksi (Disembunyikan saat mencetak) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Histori
            </a>
            <div class="d-flex gap-2">
                <?php if ($filter_status !== ''): ?>
                    <a href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>Semua Transaksi
                    </a>
                <?php endif; ?>
                <?php if ($filter_status !== 'Sudah Lunas'): ?>
                    <a href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=Sudah+Lunas" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-check-circle me-1"></i>Lunas
                    </a>
                <?php endif; ?>
                <?php if ($filter_status !== 'Belum Lunas'): ?>
                    <a href="cetak_laporan.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&filter_status=Belum+Lunas" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Belum Lunas
                    </a>
                <?php endif; ?>
                <button onclick="window.print();" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Box Laporan Rekapitulasi -->
        <div class="invoice-box shadow-sm">
            <div class="invoice-header d-flex justify-content-between align-items-center flex-wrap pb-3">
                <div>
                    <?php if ($filter_status === 'Sudah Lunas'): ?>
                        <h3 class="fw-800 m-0 text-success">LAPORAN SPP SUDAH LUNAS</h3>
                    <?php elseif ($filter_status === 'Belum Lunas'): ?>
                        <h3 class="fw-800 m-0 text-danger">LAPORAN SPP BELUM LUNAS</h3>
                    <?php else: ?>
                        <h3 class="fw-800 m-0 text-primary">LAPORAN PENERIMAAN SPP</h3>
                    <?php endif; ?>
                    <p class="text-muted small m-0">SMK Negeri Indonesia</p>
                </div>
                <div class="text-md-end mt-2 mt-md-0">
                    <h6 class="fw-700 m-0 text-dark">Status:
                        <?php if ($filter_status === 'Sudah Lunas'): ?>
                            <span class="badge bg-success">Sudah Lunas</span>
                        <?php elseif ($filter_status === 'Belum Lunas'): ?>
                            <span class="badge bg-danger">Belum Lunas</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Semua Transaksi</span>
                        <?php endif; ?>
                    </h6>
                    <p class="text-muted small m-0">Tanggal Cetak: <?= date('d M Y') ?></p>
                </div>
            </div>

            <div class="my-4">
                <table class="table table-borderless table-sm small w-auto">
                    <tr>
                        <td class="fw-600 text-dark pe-3">Periode Laporan</td>
                        <td>: <strong><?= htmlspecialchars($periode_text) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="fw-600 text-dark pe-3">Filter Status</td>
                        <td>: <strong><?= htmlspecialchars($status_label) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="fw-600 text-dark pe-3">Jumlah Transaksi</td>
                        <td>: <?= $total_rows ?> Rekord</td>
                    </tr>
                </table>
            </div>

            <!-- Detail Tabel Rekapitulasi -->
            <table class="table table-striped align-middle print-table my-4 small">
                <thead>
                    <tr class="table-dark">
                        <th width="5%">No</th>
                        <th width="12%">ID Transaksi</th>
                        <th>Siswa / NISN</th>
                        <th width="12%">Kelas</th>
                        <th width="12%">Tahun SPP</th>
                        <th width="15%">Tanggal Bayar</th>
                        <th class="text-end" width="15%">Jumlah Bayar</th>
                        <th width="10%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_rows == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ditemukan data transaksi pembayaran pada periode ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($rows as $r): 
                            $total_revenue += (float)$r['jumlah_bayar'];
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="fw-600 text-secondary"><?= htmlspecialchars($r['id_pembayaran']) ?></td>
                                <td>
                                    <div class="fw-700 text-dark"><?= htmlspecialchars($r['nama']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">NISN: <?= htmlspecialchars($r['nisn']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($r['nama_kelas']) ?></td>
                                <td>Tahun <?= htmlspecialchars($r['tahun']) ?></td>
                                <td><?= $r['tgl_bayar'] ? date('d-m-Y', strtotime($r['tgl_bayar'])) : '-' ?></td>
                                <td class="text-end fw-600 text-dark">Rp <?= number_format($r['jumlah_bayar'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php if ($r['status'] === 'Sudah Lunas'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-700 text-dark">
                            <td colspan="6" class="text-end py-3">
                                <?php if ($filter_status === 'Sudah Lunas'): ?>
                                    Total Penerimaan SPP Lunas
                                <?php elseif ($filter_status === 'Belum Lunas'): ?>
                                    Total Tagihan SPP Belum Lunas
                                <?php else: ?>
                                    Total Seluruh Penerimaan SPP
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-primary py-3" style="font-size: 1rem;">Rp <?= number_format($total_revenue, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="row mt-5 pt-4 border-top">
                <div class="col-8">
                    <p class="small text-muted mb-0">Catatan:</p>
                    <p class="small text-muted italic">
                        <?php if ($filter_status === 'Sudah Lunas'): ?>
                            Laporan ini hanya menampilkan transaksi dengan status <strong>Sudah Lunas</strong>.
                        <?php elseif ($filter_status === 'Belum Lunas'): ?>
                            Laporan ini hanya menampilkan transaksi dengan status <strong>Belum Lunas</strong>.
                        <?php else: ?>
                            Rekapitulasi ini dihasilkan secara otomatis oleh sistem sebagai laporan pertanggungjawaban penerimaan SPP.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-4 text-center">
                    <p class="small text-dark mb-5">Mengetahui,<br>Bendahara Sekolah,</p>
                    <p class="fw-700 text-dark mb-0 border-bottom d-inline-block px-3"><?= htmlspecialchars($_SESSION['nama'] ?? 'Bendahara SPP') ?></p>
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
