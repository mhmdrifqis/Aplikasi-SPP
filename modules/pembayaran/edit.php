<?php
/**
 * Modul Edit Pembayaran (Ubah Transaksi)
 * Aplikasi SPP
 * 
 * Antarmuka untuk memperbarui data transaksi pembayaran SPP (misalnya pelunasan).
 * Memuat data transaksi lama dan memperbarui perhitungan nominal bayar serta kembalian secara interaktif.
 */
$title = "Ubah Transaksi SPP";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';

// Validasi otorisasi (Hanya Admin dan Petugas yang diperbolehkan masuk)
if ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'petugas') {
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
$pembayaran = new Pembayaran($db);
$row = $pembayaran->readOne($id);

if (!$row) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'Data transaksi tidak ditemukan.'
    ];
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $tgl_terakhir_bayar = $_POST['tgl_terakhir_bayar'];
    $batas_pembayaran = $_POST['batas_pembayaran'];
    $jumlah_bulan = $_POST['jumlah_bulan'];
    $nominal_bayar = $_POST['nominal_bayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $kembalian = $_POST['kembalian'];

    if (empty($jumlah_bayar)) {
        $error = "Jumlah Uang Dibayar wajib diisi!";
    } else {
        // Melakukan update data transaksi pembayaran
        if ($pembayaran->update($id, $status, $tgl_bayar, $tgl_terakhir_bayar, $batas_pembayaran, $jumlah_bulan, $nominal_bayar, $jumlah_bayar, $kembalian)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Transaksi pembayaran SPP berhasil diperbarui/dilunasi.'
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal memperbarui transaksi pembayaran. Periksa input basis data.";
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Ubah Transaksi Pembayaran SPP</h4>
    <p class="text-muted small m-0">Sesuaikan data transaksi pembayaran atau lakukan pelunasan untuk tagihan tertunda</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="form-card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">ID Transaksi</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($row['id_pembayaran']) ?>" readonly>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">Siswa Pembayar</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($row['nama']) ?> (NISN: <?= htmlspecialchars($row['nisn']) ?>)" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label class="form-label">Tarif SPP Bulanan</label>
                        <input type="text" id="nominal_spp_display" class="form-control bg-light" value="Rp <?= number_format($row['nominal'], 0, ',', '.') ?>" readonly>
                        <!-- Menyimpan nominal asli untuk perhitungan JS -->
                        <input type="hidden" id="nominal_spp" value="<?= htmlspecialchars($row['nominal']) ?>">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label for="jumlah_bulan" class="form-label">Jumlah Bulan Dibayar</label>
                        <select name="jumlah_bulan" id="jumlah_bulan" class="form-select" required>
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>" <?= ($row['jumlah_bulan'] == $i) ? 'selected' : '' ?>><?= $i ?> Bulan</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label for="nominal_bayar" class="form-label">Total Harus Dibayar</label>
                        <input type="number" name="nominal_bayar" id="nominal_bayar" class="form-control bg-light" value="<?= htmlspecialchars($row['nominal_bayar']) ?>" readonly required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" id="tgl_bayar" class="form-control" value="<?= htmlspecialchars($row['tgl_bayar'] ?: date('Y-m-d')) ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="tgl_terakhir_bayar" class="form-label">Tanggal Terakhir Bayar</label>
                        <input type="date" name="tgl_terakhir_bayar" id="tgl_terakhir_bayar" class="form-control" value="<?= htmlspecialchars($row['tgl_terakhir_bayar'] ?: date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="batas_pembayaran" class="form-label">Batas Pembayaran</label>
                        <input type="date" name="batas_pembayaran" id="batas_pembayaran" class="form-control" value="<?= htmlspecialchars($row['batas_pembayaran'] ?: date('Y-m-d', strtotime('+1 month'))) ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="status" class="form-label">Status Pembayaran</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Belum Lunas" <?= ($row['status'] === 'Belum Lunas') ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="Sudah Lunas" <?= ($row['status'] === 'Sudah Lunas') ? 'selected' : '' ?>>Sudah Lunas</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="jumlah_bayar" class="form-label">Jumlah Uang Dibayar (Rupiah)</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" value="<?= htmlspecialchars($row['jumlah_bayar']) ?>" placeholder="Masukkan jumlah uang tunai" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="kembalian" class="form-label">Uang Kembalian</label>
                        <input type="number" name="kembalian" id="kembalian" class="form-control bg-light" value="<?= htmlspecialchars($row['kembalian']) ?>" readonly required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const tarifBulanan = parseFloat($("#nominal_spp").val()) || 0;

    // Kejadian saat jumlah bulan berubah
    $("#jumlah_bulan").on("change", function () {
        recalculateTotals();
    });

    // Kejadian saat mengetik jumlah uang tunai bayar
    $("#jumlah_bayar").on("input", function () {
        calculateChange();
    });

    // Menghitung ulang total tagihan
    function recalculateTotals() {
        const months = parseInt($("#jumlah_bulan").val());
        const billingTotal = tarifBulanan * months;
        $("#nominal_bayar").val(billingTotal);
        calculateChange();
    }

    // Menghitung kembalian uang
    function calculateChange() {
        const billingTotal = parseFloat($("#nominal_bayar").val()) || 0;
        const cashAmount = parseFloat($("#jumlah_bayar").val()) || 0;
        
        const change = cashAmount - billingTotal;
        $("#kembalian").val(change);

        // Menyetel status lunas otomatis jika uang bayar mencukupi
        if (change >= 0) {
            $("#status").val("Sudah Lunas");
        } else {
            $("#status").val("Belum Lunas");
        }
    }
});
</script>

<?php
require_once '../../layout/footer.php';
?>
