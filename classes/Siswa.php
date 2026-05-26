<?php
/**
 * Model Siswa
 * Aplikasi SPP
 * 
 * Mengelola operasi basis data untuk tabel tb_siswa_muhammadrifqisaifulloh.
 */
class Siswa {
    private $conn;
    private $table_name = "tb_siswa_muhammadrifqisaifulloh";

    // Atribut Siswa
    public $nisn;
    public $nis;
    public $nama;
    public $id_kelas;
    public $nama_kelas;
    public $alamat;
    public $no_telp;
    public $id_spp;

    /**
     * Konstruktor untuk injeksi koneksi basis data.
     * 
     * @param PDO $db Objek koneksi basis data.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membaca seluruh data siswa dengan relasi kelas dan SPP.
     * 
     * @return PDOStatement Hasil kueri data siswa.
     */
    public function read() {
        $query = "SELECT s.*, k.nama_kelas as kelas_display, k.komp_keahlian, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " s
                  JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON s.id_spp = spp.id_spp
                  ORDER BY s.nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca satu data siswa berdasarkan NISN.
     * 
     * @param string $nisn NISN Siswa.
     * @return array|false Data siswa atau false jika tidak ditemukan.
     */
    public function readOne($nisn) {
        $query = "SELECT s.*, k.nama_kelas as kelas_display, k.komp_keahlian, spp.tahun, spp.nominal 
                  FROM " . $this->table_name . " s
                  JOIN tb_kelas_muhammadrifqisaifulloh k ON s.id_kelas = k.id_kelas
                  JOIN tb_spp_muhammadrifqisaifulloh spp ON s.id_spp = spp.id_spp
                  WHERE s.nisn = :nisn LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nisn", $nisn);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fungsi pembantu untuk mencari Nama Kelas berdasarkan ID Kelas.
     * Hal ini berguna untuk menjaga integritas foreign key komposit.
     * 
     * @param string $id_kelas ID Kelas.
     * @return string Nama Kelas.
     */
    private function lookupNamaKelas($id_kelas) {
        $query = "SELECT nama_kelas FROM tb_kelas_muhammadrifqisaifulloh WHERE id_kelas = :id_kelas LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_kelas", $id_kelas);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['nama_kelas'] : '';
    }

    /**
     * Menambahkan data profil siswa baru.
     * 
     * @param string $nisn NISN (10 karakter).
     * @param string $nis NIS (8 karakter).
     * @param string $nama Nama Siswa.
     * @param string $id_kelas ID Kelas.
     * @param string $alamat Alamat.
     * @param string $no_telp Nomor Telepon.
     * @param string $id_spp ID Ketentuan SPP.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($nisn, $nis, $nama, $id_kelas, $alamat, $no_telp, $id_spp) {
        // Melakukan lookup otomatis nama kelas untuk menjaga integritas referensial kunci komposit
        $nama_kelas = $this->lookupNamaKelas($id_kelas);

        $query = "INSERT INTO " . $this->table_name . " 
                  (nisn, nis, nama, id_kelas, nama_kelas, alamat, no_telp, id_spp) 
                  VALUES (:nisn, :nis, :nama, :id_kelas, :nama_kelas, :alamat, :no_telp, :id_spp)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $nisn = htmlspecialchars(strip_tags($nisn));
        $nis = htmlspecialchars(strip_tags($nis));
        $nama = htmlspecialchars(strip_tags($nama));
        $id_kelas = htmlspecialchars(strip_tags($id_kelas));
        $alamat = htmlspecialchars(strip_tags($alamat));
        $no_telp = htmlspecialchars(strip_tags($no_telp));
        $id_spp = htmlspecialchars(strip_tags($id_spp));

        // Binding Parameter
        $stmt->bindParam(":nisn", $nisn);
        $stmt->bindParam(":nis", $nis);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":id_kelas", $id_kelas);
        $stmt->bindParam(":nama_kelas", $nama_kelas);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":no_telp", $no_telp);
        $stmt->bindParam(":id_spp", $id_spp);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Memperbarui data profil siswa.
     * 
     * @param string $old_nisn NISN Lama (target baris).
     * @param string $new_nisn NISN Baru.
     * @param string $nis NIS.
     * @param string $nama Nama Siswa.
     * @param string $id_kelas ID Kelas.
     * @param string $alamat Alamat.
     * @param string $no_telp Nomor Telepon.
     * @param string $id_spp ID SPP.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($old_nisn, $new_nisn, $nis, $nama, $id_kelas, $alamat, $no_telp, $id_spp) {
        $nama_kelas = $this->lookupNamaKelas($id_kelas);

        $query = "UPDATE " . $this->table_name . " 
                  SET nisn = :new_nisn, nis = :nis, nama = :nama, id_kelas = :id_kelas, 
                      nama_kelas = :nama_kelas, alamat = :alamat, no_telp = :no_telp, id_spp = :id_spp 
                  WHERE nisn = :old_nisn";
        
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $old_nisn = htmlspecialchars(strip_tags($old_nisn));
        $new_nisn = htmlspecialchars(strip_tags($new_nisn));
        $nis = htmlspecialchars(strip_tags($nis));
        $nama = htmlspecialchars(strip_tags($nama));
        $id_kelas = htmlspecialchars(strip_tags($id_kelas));
        $alamat = htmlspecialchars(strip_tags($alamat));
        $no_telp = htmlspecialchars(strip_tags($no_telp));
        $id_spp = htmlspecialchars(strip_tags($id_spp));

        // Binding Parameter
        $stmt->bindParam(":old_nisn", $old_nisn);
        $stmt->bindParam(":new_nisn", $new_nisn);
        $stmt->bindParam(":nis", $nis);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":id_kelas", $id_kelas);
        $stmt->bindParam(":nama_kelas", $nama_kelas);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":no_telp", $no_telp);
        $stmt->bindParam(":id_spp", $id_spp);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Menghapus data siswa.
     * 
     * @param string $nisn NISN Siswa.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($nisn) {
        $query = "DELETE FROM " . $this->table_name . " WHERE nisn = :nisn";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nisn", $nisn);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
