<?php
/**
 * Model Petugas
 * Aplikasi SPP
 * 
 * Mengelola operasi basis data dan kueri autentikasi akun pengguna untuk tabel tb_petugas_muhammadrifqisaifulloh.
 */
class Petugas {
    private $conn;
    private $table_name = "tb_petugas_muhammadrifqisaifulloh";

    // Atribut Petugas
    public $id_petugas;
    public $username;
    public $password;
    public $nama_petugas;
    public $level;

    /**
     * Konstruktor untuk injeksi koneksi basis data.
     * 
     * @param PDO $db Objek koneksi basis data.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membaca seluruh data petugas/staf.
     * 
     * @return PDOStatement Kueri data petugas.
     */
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_petugas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Membaca satu data akun petugas berdasarkan ID.
     * 
     * @param string $id ID Petugas.
     * @return array|false Data petugas atau false jika tidak ditemukan.
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_petugas = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambahkan akun petugas/staf baru.
     * 
     * @param string $id ID Petugas.
     * @param string $username Username.
     * @param string $password Password (akan di-hash menggunakan MD5).
     * @param string $nama Nama Lengkap Petugas.
     * @param string $level Tingkat Akses ('admin', 'petugas', 'siswa').
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create($id, $username, $password, $nama, $level) {
        $query = "INSERT INTO " . $this->table_name . " (id_petugas, username, password, nama_petugas, level) 
                  VALUES (:id, :username, :password, :nama, :level)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitasi Input
        $id = htmlspecialchars(strip_tags($id));
        $username = htmlspecialchars(strip_tags($username));
        $nama = htmlspecialchars(strip_tags($nama));
        $level = htmlspecialchars(strip_tags($level));

        // Melakukan Hashing MD5 agar cocok dengan tipe VARCHAR(32) pada database
        $hashed_password = md5($password);

        // Binding Parameter
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":level", $level);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Memperbarui data akun petugas yang sudah ada.
     * 
     * @param string $old_id ID Petugas lama.
     * @param string $new_id ID Petugas baru.
     * @param string $username Username.
     * @param string $password Password baru (jika kosong, mempertahankan password lama).
     * @param string $nama Nama Lengkap.
     * @param string $level Level Hak Akses.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($old_id, $new_id, $username, $password, $nama, $level) {
        $old_id = htmlspecialchars(strip_tags($old_id));
        $new_id = htmlspecialchars(strip_tags($new_id));
        $username = htmlspecialchars(strip_tags($username));
        $nama = htmlspecialchars(strip_tags($nama));
        $level = htmlspecialchars(strip_tags($level));

        // Menyusun kueri berdasarkan perubahan password
        if (!empty($password)) {
            $query = "UPDATE " . $this->table_name . " 
                      SET id_petugas = :new_id, username = :username, password = :password, nama_petugas = :nama, level = :level 
                      WHERE id_petugas = :old_id";
            $hashed_password = md5($password);
        } else {
            $query = "UPDATE " . $this->table_name . " 
                      SET id_petugas = :new_id, username = :username, nama_petugas = :nama, level = :level 
                      WHERE id_petugas = :old_id";
        }

        $stmt = $this->conn->prepare($query);

        // Binding Parameter Umum
        $stmt->bindParam(":old_id", $old_id);
        $stmt->bindParam(":new_id", $new_id);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":level", $level);

        // Binding Password jika diubah
        if (!empty($password)) {
            $stmt->bindParam(":password", $hashed_password);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Menghapus akun petugas.
     * 
     * @param string $id ID Petugas.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_petugas = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Memverifikasi kecocokan kredensial login pengguna.
     * 
     * @param string $username Username login.
     * @param string $password Password plaintext.
     * @return array|false Informasi akun jika sukses, false jika gagal.
     */
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Memverifikasi kecocokan hash MD5
            if (md5($password) === $user['password']) {
                return $user;
            }
        }
        return false;
    }
}
?>
