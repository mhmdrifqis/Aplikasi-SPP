<?php
/**
 * Kelas Koneksi Basis Data (PDO Wrapper)
 * Aplikasi SPP
 * 
 * Menyediakan instance koneksi PDO untuk digunakan di seluruh aplikasi.
 */
class Database {
    private $host = "localhost";
    private $db_name = "tugas1_muhammadrifqisaifulloh";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * Membangun koneksi ke basis data menggunakan PDO.
     * 
     * @return PDO|null Objek koneksi PDO atau null jika gagal.
     */
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            // Mengatur mode error PDO ke Exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Mengatur fetch mode default ke array asosiatif
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Set karakter enkoding ke utf8mb4
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            die("Koneksi database (PDO) gagal: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
