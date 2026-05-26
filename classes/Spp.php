<?php
/**
 * Model SPP
 * Aplikasi SPP
 * 
 * Mengelola operasi basis data untuk tabel tb_spp_muhammadrifqisaifulloh.
 */
class Spp {
    private $conn;
    private $table_name = "tb_spp_muhammadrifqisaifulloh";

    // Atribut SPP
    public $id_spp;
    public $tahun;
    public $nominal;

    /**
     * Konstruktor untuk injeksi koneksi basis data.
     * 
     * @param PDO $db Objek koneksi basis data.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membaca seluruh data kebijakan SPP.
     * 
     * @return PDOStatement Hasil kueri data SPP.
     */
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY tahun DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca satu data SPP berdasarkan ID.
     * 
     * @param string $id ID SPP.
     * @return array|false Data SPP atau false jika tidak ditemukan.
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_spp = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambahkan data ketetapan SPP baru.
     * 
     * @param string $id ID SPP.
     * @param int $tahun Tahun Ajaran SPP.
     * @param string $nominal Nilai nominal bulanan.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($id, $tahun, $nominal) {
        $query = "INSERT INTO " . $this->table_name . " (id_spp, tahun, nominal) VALUES (:id, :tahun, :nominal)";
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $id = htmlspecialchars(strip_tags($id));
        $tahun = (int)$tahun;
        $nominal = htmlspecialchars(strip_tags($nominal));

        // Binding Parameter
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":tahun", $tahun, PDO::PARAM_INT);
        $stmt->bindParam(":nominal", $nominal);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Memperbarui data ketetapan SPP.
     * 
     * @param string $old_id ID SPP lama (sebagai target).
     * @param string $new_id ID SPP baru.
     * @param int $tahun Tahun SPP baru.
     * @param string $nominal Nominal baru.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($old_id, $new_id, $tahun, $nominal) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_spp = :new_id, tahun = :tahun, nominal = :nominal 
                  WHERE id_spp = :old_id";
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $old_id = htmlspecialchars(strip_tags($old_id));
        $new_id = htmlspecialchars(strip_tags($new_id));
        $tahun = (int)$tahun;
        $nominal = htmlspecialchars(strip_tags($nominal));

        // Binding Parameter
        $stmt->bindParam(":old_id", $old_id);
        $stmt->bindParam(":new_id", $new_id);
        $stmt->bindParam(":tahun", $tahun, PDO::PARAM_INT);
        $stmt->bindParam(":nominal", $nominal);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Menghapus data SPP.
     * 
     * @param string $id ID SPP.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_spp = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
