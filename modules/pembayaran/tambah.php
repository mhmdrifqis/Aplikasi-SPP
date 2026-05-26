<?php
/**
 * Modul Tambah Entri Pembayaran (Transaksi Baru)
 * Aplikasi SPP
 * 
 * Antarmuka pencatatan transaksi pembayaran uang SPP bulanan siswa.
 * Memuat nominal tarif SPP secara dinamis dan menghitung uang kembalian secara interaktif.
 */
$title = "Entri Transaksi SPP";
require_once '../../layout/header.php';
require_once '../../config/Database.php';
require_once '../../classes/Pembayaran.php';
require_once '../../classes/Siswa.php';

// Validasi otorisasi (Hanya Admin dan Petugas yang diperbolehkan masuk)
if ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'petugas') {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Anda tidak memiliki hak akses untuk halaman ini!'
    ];
    header("Location: ../../index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$pembayaran = new Pembayaran($db);

// Membaca data siswa untuk dropdown
$siswa = new Siswa($db);
$list_siswa = $siswa->read();

$error = '';

// Mencari ID pembayaran terakhir dengan format PAY(angka)
$query_id = "SELECT id_pembayaran FROM tb_pembayaran_muhammadrifqisaifulloh WHERE id_pembayaran LIKE 'PAY%' ORDER BY CAST(SUBSTRING(id_pembayaran, 4) AS UNSIGNED) DESC LIMIT 1";
$stmt_id = $db->prepare($query_id);
$stmt_id->execute();
$last_row = $stmt_id->fetch(PDO::FETCH_ASSOC);

if ($last_row) {
    // Mengambil angka setelah 'PAY' dan menambahkannya dengan 1
    $last_id = $last_row['id_pembayaran'];
    $last_num = (int)substr($last_id, 3);
    $next_num = $last_num + 1;
    $transaction_code = "PAY" . $next_num;
} else {
    // Jika tidak ada data transaksi sebelumnya, mulai dari PAY1
    $transaction_code = "PAY1";
}

if (isset($_POST['submit'])) {
    $id_pembayaran = $_POST['id_pembayaran'];
    $status = $_POST['status'];
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $tgl_terakhir_bayar = $_POST['tgl_terakhir_bayar'];
    $batas_pembayaran = $_POST['batas_pembayaran'];
    $jumlah_bulan = $_POST['jumlah_bulan'];
    $id_spp = $_POST['id_spp'];
    $nominal_bayar = $_POST['nominal_bayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $kembalian = $_POST['kembalian'];

    if (empty($id_pembayaran) || empty($nisn) || empty($id_spp) || empty($jumlah_bayar)) {
        $error = "ID Pembayaran, Siswa, SPP, dan Jumlah Bayar wajib diisi!";
    } else {
        // Menyimpan log transaksi pembayaran
        if ($pembayaran->create($id_pembayaran, $status, $nisn, $tgl_bayar, $tgl_terakhir_bayar, $batas_pembayaran, $jumlah_bulan, $id_spp, $nominal_bayar, $jumlah_bayar, $kembalian)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Transaksi pembayaran SPP berhasil dicatat.'
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal mencatat transaksi pembayaran. Periksa input dan relasi data.";
        }
    }
}
?>

<div class="mb-4">
    <h4 class="fw-700 m-0">Entri Transaksi Pembayaran SPP</h4>
    <p class="text-muted small m-0">Catat pembayaran uang SPP bulanan siswa di sini</p>
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
                        <label for="id_pembayaran" class="form-label">ID Transaksi (Otomatis)</label>
                        <input type="text" name="id_pembayaran" id="id_pembayaran" class="form-control" value="<?= $transaction_code ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="nisn" class="form-label">Pilih Siswa</label>
                        <select name="nisn" id="nisn" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php 
                            while ($s = $list_siswa->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?= htmlspecialchars($s['nisn']) ?>" 
                                        data-idspp="<?= htmlspecialchars($s['id_spp']) ?>" 
                                        data-nominalspp="<?= htmlspecialchars($s['nominal']) ?>">
                                    <?= htmlspecialchars($s['nama']) ?> - (NISN: <?= htmlspecialchars($s['nisn']) ?>) [Kelas <?= htmlspecialchars($s['kelas_display']) ?>]
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label for="nominal_spp_display" class="form-label">Tarif SPP Bulanan</label>
                        <input type="text" id="nominal_spp_display" class="form-control bg-light" value="Rp 0" readonly>
                        <!-- Input tersembunyi untuk submit id_spp -->
                        <input type="hidden" name="id_spp" id="id_spp">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label for="jumlah_bulan" class="form-label">Jumlah Bulan Dibayar</label>
                        <select name="jumlah_bulan" id="jumlah_bulan" class="form-select" required>
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Bulan</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label for="nominal_bayar" class="form-label">Total Harus Dibayar</label>
                        <input type="number" name="nominal_bayar" id="nominal_bayar" class="form-control bg-light" readonly required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" id="tgl_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="tgl_terakhir_bayar" class="form-label">Tanggal Terakhir Bayar</label>
                        <input type="date" name="tgl_terakhir_bayar" id="tgl_terakhir_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="batas_pembayaran" class="form-label">Batas Pembayaran</label>
                        <input type="date" name="batas_pembayaran" id="batas_pembayaran" class="form-control" value="<?= date('Y-m-d', strtotime('+1 month')) ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="status" class="form-label">Status Pembayaran</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Sudah Lunas" selected>Sudah Lunas</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="jumlah_bayar" class="form-label">Jumlah Uang Dibayar (Rupiah)</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" placeholder="Masukkan jumlah uang tunai dari pembayar" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="kembalian" class="form-label">Uang Kembalian</label>
                        <input type="number" name="kembalian" id="kembalian" class="form-control bg-light" readonly required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
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
    let tarifBulanan = 0;

    // Kejadian saat dropdown siswa berubah
    $("#nisn").on("change", function () {
        const selectedOption = $(this).find("option:selected");
        
        if (selectedOption.val() !== "") {
            const idSpp = selectedOption.data("idspp");
            tarifBulanan = parseFloat(selectedOption.data("nominalspp"));

            // Mengisi field tersembunyi
            $("#id_spp").val(idSpp);
            $("#nominal_spp_display").val("Rp " + tarifBulanan.toLocaleString("id-ID"));
            
            recalculateTotals();
        } else {
            tarifBulanan = 0;
            $("#id_spp").val("");
            $("#nominal_spp_display").val("Rp 0");
            $("#nominal_bayar").val("");
            $("#kembalian").val("");
            $("#jumlah_bayar").val("");
        }
    });

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
