<?php
/**
 * Model Kelas
 * Aplikasi SPP
 * 
 * Mengelola operasi basis data untuk tabel tb_kelas_muhammadrifqisaifulloh.
 */
class Kelas {
    private $conn;
    private $table_name = "tb_kelas_muhammadrifqisaifulloh";

    // Atribut Kelas
    public $id_kelas;
    public $nama_kelas;
    public $komp_keahlian;

    /**
     * Konstruktor untuk injeksi koneksi basis data.
     * 
     * @param PDO $db Objek koneksi basis data.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membaca seluruh data kelas.
     * 
     * @return PDOStatement Hasil kueri data kelas.
     */
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca satu data kelas berdasarkan ID.
     * 
     * @param string $id ID Kelas.
     * @return array|false Data kelas atau false jika tidak ditemukan.
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_kelas = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambahkan data kelas baru.
     * 
     * @param string $id ID Kelas.
     * @param string $nama Nama Kelas.
     * @param string $komp Kompetensi Keahlian (Jurusan).
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($id, $nama, $komp) {
        $query = "INSERT INTO " . $this->table_name . " (id_kelas, nama_kelas, komp_keahlian) 
                  VALUES (:id, :nama, :komp)";
        $stmt = $this->conn->prepare($query);

        // Sanitasasi Input
        $id = htmlspecialchars(strip_tags($id));
        $nama = htmlspecialchars(strip_tags($nama));
        $komp = htmlspecialchars(strip_tags($komp));

        // Binding Parameter
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":komp", $komp);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Memperbarui data kelas yang sudah ada.
     * 
     * @param string $old_id ID Kelas lama (sebagai target).
     * @param string $new_id ID Kelas baru.
     * @param string $nama Nama Kelas baru.
     * @param string $komp Jurusan baru.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($old_id, $new_id, $nama, $komp) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_kelas = :new_id, nama_kelas = :nama, komp_keahlian = :komp 
                  WHERE id_kelas = :old_id";
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $old_id = htmlspecialchars(strip_tags($old_id));
        $new_id = htmlspecialchars(strip_tags($new_id));
        $nama = htmlspecialchars(strip_tags($nama));
        $komp = htmlspecialchars(strip_tags($komp));

        // Binding Parameter
        $stmt->bindParam(":old_id", $old_id);
        $stmt->bindParam(":new_id", $new_id);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":komp", $komp);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Menghapus data kelas.
     * 
     * @param string $id ID Kelas.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_kelas = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
