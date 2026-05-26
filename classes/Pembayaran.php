<?php
/**
 * Model Pembayaran
 * Aplikasi SPP
 * 
 * Mengelola operasi transaksi pembayaran untuk tabel tb_pembayaran_muhammadrifqisaifulloh
 * dan tabel sinkronisasi cek status tb_cek_pembayaran_muhammadrifqisaifulloh.
 */
class Pembayaran {
    private $conn;
    private $table_name = "tb_pembayaran_muhammadrifqisaifulloh";

    // Atribut Pembayaran
    public $id_pembayaran;
    public $status;
    public $nisn;
    public $tgl_bayar;
    public $tgl_terakhir_bayar;
    public $batas_pembayaran;
    public $jumlah_bulan;
    public $id_spp;
    public $nominal_bayar;
    public $jumlah_bayar;
    public $kembalian;

    /**
     * Konstruktor untuk injeksi koneksi basis data.
     * 
     * @param PDO $db Objek koneksi basis data.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membaca seluruh data log transaksi pembayaran.
     * 
     * @return PDOStatement Kueri histori pembayaran.
     */
    public function readAll() {
        $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca log transaksi pembayaran khusus untuk satu siswa tertentu.
     * 
     * @param string $nisn NISN Siswa.
     * @return PDOStatement Riwayat pembayaran siswa tersebut.
     */
    public function readBySiswa($nisn) {
        $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  WHERE p.nisn = :nisn
                  ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nisn", $nisn);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca log transaksi pembayaran terfilter rentang tanggal.
     * 
     * @param string $tgl_mulai Tanggal awal.
     * @param string $tgl_selesai Tanggal akhir.
     * @return PDOStatement Kueri histori pembayaran terfilter.
     */
    public function readFiltered($tgl_mulai, $tgl_selesai) {
        $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  WHERE p.tgl_bayar BETWEEN :tgl_mulai AND :tgl_selesai
                  ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tgl_mulai", $tgl_mulai);
        $stmt->bindParam(":tgl_selesai", $tgl_selesai);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca detail satu kuitansi/transaksi pembayaran berdasarkan ID.
     * 
     * @param string $id ID Pembayaran.
     * @return array|false Informasi transaksi pembayaran atau false.
     */
    public function readOne($id) {
        $query = "SELECT p.*, s.nama, s.nis, s.id_kelas, k.nama_kelas, k.komp_keahlian, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  WHERE p.id_pembayaran = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mencatat transaksi pembayaran baru dan memperbarui/sinkronisasi ke tabel status cek pembayaran.
     * Menggunakan basis transaksi PDO (Rollback jika salah satu langkah gagal).
     * 
     * @return bool True jika transaksi sukses dicatat seluruhnya, false jika gagal.
     */
    public function create($id_pembayaran, $status, $nisn, $tgl_bayar, $tgl_terakhir_bayar, $batas_pembayaran, $jumlah_bulan, $id_spp, $nominal_bayar, $jumlah_bayar, $kembalian) {
        try {
            $this->conn->beginTransaction();

            // 1. Memasukkan record transaksi pembayaran
            $query = "INSERT INTO " . $this->table_name . " 
                      (id_pembayaran, status, nisn, tgl_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, id_spp, nominal_bayar, jumlah_bayar, kembalian) 
                      VALUES (:id_pembayaran, :status, :nisn, :tgl_bayar, :tgl_terakhir_bayar, :batas_pembayaran, :jumlah_bulan, :id_spp, :nominal_bayar, :jumlah_bayar, :kembalian)";
            
            $stmt = $this->conn->prepare($query);

            $id_pembayaran = htmlspecialchars(strip_tags($id_pembayaran));
            $status = htmlspecialchars(strip_tags($status));
            $nisn = htmlspecialchars(strip_tags($nisn));
            $tgl_bayar = !empty($tgl_bayar) ? htmlspecialchars(strip_tags($tgl_bayar)) : null;
            $tgl_terakhir_bayar = !empty($tgl_terakhir_bayar) ? htmlspecialchars(strip_tags($tgl_terakhir_bayar)) : null;
            $batas_pembayaran = !empty($batas_pembayaran) ? htmlspecialchars(strip_tags($batas_pembayaran)) : null;
            $jumlah_bulan = htmlspecialchars(strip_tags($jumlah_bulan));
            $id_spp = htmlspecialchars(strip_tags($id_spp));
            $nominal_bayar = htmlspecialchars(strip_tags($nominal_bayar));
            $jumlah_bayar = htmlspecialchars(strip_tags($jumlah_bayar));
            $kembalian = htmlspecialchars(strip_tags($kembalian));

            $stmt->bindParam(":id_pembayaran", $id_pembayaran);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":nisn", $nisn);
            $stmt->bindParam(":tgl_bayar", $tgl_bayar);
            $stmt->bindParam(":tgl_terakhir_bayar", $tgl_terakhir_bayar);
            $stmt->bindParam(":batas_pembayaran", $batas_pembayaran);
            $stmt->bindParam(":jumlah_bulan", $jumlah_bulan);
            $stmt->bindParam(":id_spp", $id_spp);
            $stmt->bindParam(":nominal_bayar", $nominal_bayar);
            $stmt->bindParam(":jumlah_bayar", $jumlah_bayar);
            $stmt->bindParam(":kembalian", $kembalian);

            $stmt->execute();

            // 2. Mengambil nama dan nomor telp siswa untuk menyesuaikan constraint foreign key komposit di tabel tb_cek_pembayaran
            $query_siswa = "SELECT nama, no_telp FROM tb_siswa_muhammadrifqisaifulloh WHERE nisn = :nisn LIMIT 1";
            $stmt_siswa = $this->conn->prepare($query_siswa);
            $stmt_siswa->bindParam(":nisn", $nisn);
            $stmt_siswa->execute();
            $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

            if (!$siswa) {
                throw new Exception("Siswa tidak ditemukan untuk sinkronisasi cek pembayaran.");
            }

            $nama = $siswa['nama'];
            $no_telp = $siswa['no_telp'];
            $tgl_sekarang = date('Y-m-d');

            // 3. Memasukkan atau memperbarui status di tabel tb_cek_pembayaran_muhammadrifqisaifulloh
            $query_check = "SELECT nisn FROM tb_cek_pembayaran_muhammadrifqisaifulloh WHERE nisn = :nisn LIMIT 1";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(":nisn", $nisn);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                // Perbarui record yang ada
                $query_sync = "UPDATE tb_cek_pembayaran_muhammadrifqisaifulloh 
                               SET tgl_terakhir_bayar = :tgl_terakhir_bayar, tgl_sekarang = :tgl_sekarang, 
                                   status_pembayaran = :status, jumlah_bulan = :jumlah_bulan, 
                                   nama = :nama, no_telp = :no_telp 
                               WHERE nisn = :nisn";
            } else {
                // Tambah record baru jika pertama kali membayar
                $query_sync = "INSERT INTO tb_cek_pembayaran_muhammadrifqisaifulloh 
                               (nisn, tgl_terakhir_bayar, tgl_sekarang, status_pembayaran, jumlah_bulan, nama, no_telp) 
                               VALUES (:nisn, :tgl_terakhir_bayar, :tgl_sekarang, :status, :jumlah_bulan, :nama, :no_telp)";
            }

            $stmt_sync = $this->conn->prepare($query_sync);
            $stmt_sync->bindParam(":nisn", $nisn);
            $stmt_sync->bindParam(":tgl_terakhir_bayar", $tgl_terakhir_bayar);
            $stmt_sync->bindParam(":tgl_sekarang", $tgl_sekarang);
            $stmt_sync->bindParam(":status", $status);
            $stmt_sync->bindParam(":jumlah_bulan", $jumlah_bulan);
            $stmt_sync->bindParam(":nama", $nama);
            $stmt_sync->bindParam(":no_telp", $no_telp);
            $stmt_sync->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Memperbarui data transaksi pembayaran dan menyelaraskan ke tabel cek status.
     * Menggunakan transaksi database PDO.
     * 
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($id_pembayaran, $status, $tgl_bayar, $tgl_terakhir_bayar, $batas_pembayaran, $jumlah_bulan, $nominal_bayar, $jumlah_bayar, $kembalian) {
        try {
            $this->conn->beginTransaction();

            // 1. Dapatkan NISN dari transaksi sebelum diupdate
            $pay_info = $this->readOne($id_pembayaran);
            if (!$pay_info) {
                throw new Exception("Transaksi pembayaran tidak ditemukan.");
            }
            $nisn = $pay_info['nisn'];

            // 2. Update record pembayaran
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, tgl_bayar = :tgl_bayar, tgl_terakhir_bayar = :tgl_terakhir_bayar, 
                          batas_pembayaran = :batas_pembayaran, jumlah_bulan = :jumlah_bulan, 
                          nominal_bayar = :nominal_bayar, jumlah_bayar = :jumlah_bayar, kembalian = :kembalian 
                      WHERE id_pembayaran = :id_pembayaran";
            
            $stmt = $this->conn->prepare($query);

            $id_pembayaran = htmlspecialchars(strip_tags($id_pembayaran));
            $status = htmlspecialchars(strip_tags($status));
            $tgl_bayar = !empty($tgl_bayar) ? htmlspecialchars(strip_tags($tgl_bayar)) : null;
            $tgl_terakhir_bayar = !empty($tgl_terakhir_bayar) ? htmlspecialchars(strip_tags($tgl_terakhir_bayar)) : null;
            $batas_pembayaran = !empty($batas_pembayaran) ? htmlspecialchars(strip_tags($batas_pembayaran)) : null;
            $jumlah_bulan = htmlspecialchars(strip_tags($jumlah_bulan));
            $nominal_bayar = htmlspecialchars(strip_tags($nominal_bayar));
            $jumlah_bayar = htmlspecialchars(strip_tags($jumlah_bayar));
            $kembalian = htmlspecialchars(strip_tags($kembalian));

            $stmt->bindParam(":id_pembayaran", $id_pembayaran);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":tgl_bayar", $tgl_bayar);
            $stmt->bindParam(":tgl_terakhir_bayar", $tgl_terakhir_bayar);
            $stmt->bindParam(":batas_pembayaran", $batas_pembayaran);
            $stmt->bindParam(":jumlah_bulan", $jumlah_bulan);
            $stmt->bindParam(":nominal_bayar", $nominal_bayar);
            $stmt->bindParam(":jumlah_bayar", $jumlah_bayar);
            $stmt->bindParam(":kembalian", $kembalian);

            $stmt->execute();

            // 3. Ambil data terbaru dari siswa bersangkutan (tgl terakhir bayar & status)
            $query_latest = "SELECT * FROM " . $this->table_name . " 
                             WHERE nisn = :nisn 
                             ORDER BY tgl_bayar DESC, id_pembayaran DESC LIMIT 1";
            $stmt_latest = $this->conn->prepare($query_latest);
            $stmt_latest->bindParam(":nisn", $nisn);
            $stmt_latest->execute();
            $latest = $stmt_latest->fetch(PDO::FETCH_ASSOC);

            if ($latest) {
                // Update status di tb_cek_pembayaran
                $query_sync = "UPDATE tb_cek_pembayaran_muhammadrifqisaifulloh 
                               SET tgl_terakhir_bayar = :tgl_terakhir_bayar, status_pembayaran = :status, jumlah_bulan = :jumlah_bulan 
                               WHERE nisn = :nisn";
                $stmt_sync = $this->conn->prepare($query_sync);
                $stmt_sync->bindParam(":nisn", $nisn);
                $stmt_sync->bindParam(":tgl_terakhir_bayar", $latest['tgl_terakhir_bayar']);
                $stmt_sync->bindParam(":status", $latest['status']);
                $stmt_sync->bindParam(":jumlah_bulan", $latest['jumlah_bulan']);
                $stmt_sync->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Membatalkan transaksi pembayaran dan memulihkan/sinkronisasi status cek pembayaran berdasarkan histori transaksi tersisa.
     * Menggunakan transaksi database PDO.
     * 
     * @param string $id ID Pembayaran.
     * @return bool True jika sukses dibatalkan, false jika gagal.
     */
    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // 1. Dapatkan informasi detail transaksi sebelum dihapus
            $pay_info = $this->readOne($id);
            if (!$pay_info) {
                throw new Exception("Transaksi pembayaran tidak ditemukan.");
            }
            $nisn = $pay_info['nisn'];

            // 2. Hapus pembayaran
            $query = "DELETE FROM " . $this->table_name . " WHERE id_pembayaran = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            // 3. Dapatkan transaksi pembayaran terupdate terakhir dari siswa tersebut
            $query_latest = "SELECT * FROM " . $this->table_name . " 
                             WHERE nisn = :nisn 
                             ORDER BY tgl_bayar DESC, id_pembayaran DESC LIMIT 1";
            $stmt_latest = $this->conn->prepare($query_latest);
            $stmt_latest->bindParam(":nisn", $nisn);
            $stmt_latest->execute();
            $latest = $stmt_latest->fetch(PDO::FETCH_ASSOC);

            // 4. Perbarui status cek pembayaran
            if ($latest) {
                $query_sync = "UPDATE tb_cek_pembayaran_muhammadrifqisaifulloh 
                               SET tgl_terakhir_bayar = :tgl_terakhir_bayar, status_pembayaran = :status, jumlah_bulan = :jumlah_bulan 
                               WHERE nisn = :nisn";
                $stmt_sync = $this->conn->prepare($query_sync);
                $stmt_sync->bindParam(":nisn", $nisn);
                $stmt_sync->bindParam(":tgl_terakhir_bayar", $latest['tgl_terakhir_bayar']);
                $stmt_sync->bindParam(":status", $latest['status']);
                $stmt_sync->bindParam(":jumlah_bulan", $latest['jumlah_bulan']);
                $stmt_sync->execute();
            } else {
                // Set status kembali ke 'Belum Lunas' dengan nominal 0 jika tidak ada riwayat transaksi tersisa
                $query_sync = "UPDATE tb_cek_pembayaran_muhammadrifqisaifulloh 
                               SET tgl_terakhir_bayar = NULL, status_pembayaran = 'Belum Lunas', jumlah_bulan = '0' 
                               WHERE nisn = :nisn";
                $stmt_sync = $this->conn->prepare($query_sync);
                $stmt_sync->bindParam(":nisn", $nisn);
                $stmt_sync->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Membaca log transaksi pembayaran filter status (Sudah Lunas / Belum Lunas).
     * 
     * @param string $status Status pembayaran ('Sudah Lunas' atau 'Belum Lunas').
     * @return PDOStatement Kueri histori pembayaran terfilter status.
     */
    public function readAllByStatus($status) {
        $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  WHERE p.status = :status
                  ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca log transaksi pembayaran terfilter rentang tanggal dan status.
     * 
     * @param string $tgl_mulai Tanggal awal.
     * @param string $tgl_selesai Tanggal akhir.
     * @param string $status Status pembayaran ('Sudah Lunas' atau 'Belum Lunas').
     * @return PDOStatement Kueri histori pembayaran terfilter tanggal & status.
     */
    public function readFilteredByStatus($tgl_mulai, $tgl_selesai, $status) {
        $query = "SELECT p.*, s.nama, s.id_kelas, k.nama_kelas, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " p
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON p.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON p.id_spp = spp.id_spp
                  WHERE p.tgl_bayar BETWEEN :tgl_mulai AND :tgl_selesai
                    AND p.status = :status
                  ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tgl_mulai", $tgl_mulai);
        $stmt->bindParam(":tgl_selesai", $tgl_selesai);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca seluruh status pembayaran di tabel tb_cek_pembayaran_muhammadrifqisaifulloh.
     * 
     * @return PDOStatement Hasil kueri cek status pembayaran.
     */
    public function readCheckStatus() {
        $query = "SELECT c.*, s.id_kelas, k.nama_kelas 
                  FROM tb_cek_pembayaran_muhammadrifqisaifulloh c
                  JOIN tb_siswa_muhammadrifqisaifulloh s ON c.nisn = s.nisn
                  LEFT JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  ORDER BY s.nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
