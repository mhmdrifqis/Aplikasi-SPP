# 💳 Aplikasi Pembayaran SPP

Aplikasi berbasis web untuk mengelola pembayaran **Sumbangan Pembinaan Pendidikan (SPP)** siswa sekolah. Dibangun menggunakan PHP murni dengan arsitektur MVC sederhana, Bootstrap 5, dan MySQL sebagai basis data.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Proyek](#-struktur-proyek)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Cara Instalasi](#-cara-instalasi)
- [Konfigurasi Database](#-konfigurasi-database)
- [Akun Default](#-akun-default)
- [Modul Aplikasi](#-modul-aplikasi)
- [Tangkapan Layar](#-tangkapan-layar)
- [Lisensi](#-lisensi)

---

## ✨ Fitur Utama

- 🔐 **Autentikasi Multi-Level** — Login dengan tiga level pengguna: Admin, Petugas, dan Siswa
- 📊 **Dashboard Interaktif** — Ringkasan statistik pembayaran, grafik, dan log transaksi terbaru
- 👨‍🎓 **Manajemen Siswa** — Tambah, ubah, dan hapus data siswa
- 🏫 **Manajemen Kelas** — Kelola data kelas dan kompetensi keahlian
- 👨‍💼 **Manajemen Petugas** — Kelola akun pengguna dan level akses
- 💰 **Manajemen Tarif SPP** — Atur nominal SPP berdasarkan tahun
- 💳 **Transaksi Pembayaran** — Catat pembayaran SPP dengan perhitungan kembalian otomatis
- 🧾 **Cetak Bukti Pembayaran** — Cetak bukti transaksi langsung dari sistem
- 🔍 **Cek Status Pembayaran** — Pantau status pembayaran tiap siswa
- 📑 **Laporan Pembayaran** — Filter laporan per bulan atau rentang tanggal, siap cetak

---

## 🛠 Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.1 |
| Database | MySQL / MariaDB 10.4 |
| Frontend | HTML5, CSS3, JavaScript |
| Framework CSS | Bootstrap 5 |
| Ikon | Bootstrap Icons 1.11 |
| Server | Apache (XAMPP) |
| Koneksi DB | PHP PDO |

---

## 📁 Struktur Proyek

```
Aplikasi SPP/
├── assets/
│   ├── css/
│   │   └── style.css          # Stylesheet kustom global
│   └── js/                    # File JavaScript tambahan
├── auth/
│   ├── login.php              # Halaman login
│   └── logout.php             # Proses logout & destroy session
├── classes/
│   ├── Kelas.php              # Model data kelas
│   ├── Pembayaran.php         # Model data pembayaran (CRUD + laporan)
│   ├── Petugas.php            # Model data petugas/pengguna
│   ├── Siswa.php              # Model data siswa
│   └── Spp.php                # Model data tarif SPP
├── config/
│   └── Database.php           # Konfigurasi & koneksi PDO ke database
├── database/
│   └── tugas1_muhammadrifqisaifulloh.sql  # Dump SQL database
├── layout/
│   ├── header.php             # Template header (HTML head + navbar)
│   └── footer.php             # Template footer
├── modules/
│   ├── bukti_pembayaran/      # Modul cetak bukti pembayaran
│   ├── cek_pembayaran/        # Modul cek status pembayaran siswa
│   ├── kelas/                 # Modul CRUD data kelas
│   ├── pembayaran/            # Modul CRUD transaksi + laporan
│   ├── petugas/               # Modul CRUD data petugas
│   ├── siswa/                 # Modul CRUD data siswa
│   └── spp/                   # Modul CRUD tarif SPP
└── index.php                  # Dashboard utama aplikasi
```

---

## ⚙️ Persyaratan Sistem

Pastikan perangkat Anda sudah terpasang:

- **XAMPP** versi 8.x atau lebih baru (sudah termasuk Apache & MySQL)
- **PHP** versi 8.0 atau lebih baru
- **MariaDB** / **MySQL** versi 10.4 atau lebih baru
- Browser modern (Chrome, Firefox, Edge, dll.)

---

## 🚀 Cara Instalasi

### 1. Clone atau Ekstrak Proyek

Clone repositori ini atau ekstrak file zip ke folder `htdocs` XAMPP:

```bash
git clone https://github.com/mhmdrifqis/Aplikasi-SPP.git "C:/xampp/htdocs/Aplikasi SPP"
```

Atau ekstrak secara manual ke:
```
C:\xampp\htdocs\Aplikasi SPP\
```

### 2. Jalankan XAMPP

Buka **XAMPP Control Panel** dan aktifkan:
- ✅ **Apache**
- ✅ **MySQL**

### 3. Import Database

1. Buka browser dan akses `http://localhost/phpmyadmin`
2. Klik **"New"** untuk membuat database baru dengan nama:
   ```
   tugas1_muhammadrifqisaifulloh
   ```
3. Pilih tab **"Import"**, klik **"Choose File"**, dan pilih file:
   ```
   database/tugas1_muhammadrifqisaifulloh.sql
   ```
4. Klik tombol **"Go"** untuk mengimpor.

### 4. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/Aplikasi SPP/
```

---

## 🗄️ Konfigurasi Database

File konfigurasi database ada di `config/Database.php`. Sesuaikan jika diperlukan:

```php
private $host     = "localhost";         // Host database
private $db_name  = "tugas1_muhammadrifqisaifulloh"; // Nama database
private $username = "root";              // Username MySQL
private $password = "";                  // Password MySQL (kosong untuk XAMPP default)
```

---

## 👥 Akun Default

Berikut akun yang tersedia untuk login:

| Username | Password | Level | Nama |
|----------|----------|-------|------|
| `admin1` | `admin` | Admin | Rifqi Admin |
| `petugas1` | `petugas1` | Petugas | Saifulloh Petugas |
| `petugas2` | `petugas2` | Petugas | Muhammad Petugas |
| `siswa1` | `siswa1` | Siswa | Ahmad Nurhuda |
| `siswa2` | `siswa2` | Siswa | Rifqi Saifulloh |

> **Catatan:** Password disimpan dalam format hash MD5. Segera ganti password setelah instalasi pertama di lingkungan produksi.

---

## 📦 Modul Aplikasi

### 🔐 Login & Autentikasi
- Login dengan username dan password
- Sistem sesi berbasis PHP Session
- Tiga level akses: **Admin**, **Petugas**, dan **Siswa**
- Proteksi halaman dari akses tanpa login

### 📊 Dashboard
- Statistik total siswa, kelas, dan total SPP terkumpul
- Grafik persentase pembayaran (Lunas / Belum Lunas)
- Tabel 5 transaksi terbaru

### 👨‍🎓 Master Data — Siswa
- Daftar semua siswa beserta kelas dan status SPP
- Tambah, ubah, dan hapus data siswa
- Pencarian dan filter data

### 🏫 Master Data — Kelas
- Kelola data kelas dan kompetensi keahlian
- Tambah, ubah, dan hapus kelas

### 👨‍💼 Master Data — Petugas
- Kelola akun pengguna sistem
- Atur level akses (Admin / Petugas / Siswa)
- Tambah, ubah, dan hapus data petugas

### 💰 Master Data — SPP
- Atur tarif SPP berdasarkan tahun
- Tambah, ubah, dan hapus tarif SPP

### 💳 Transaksi Pembayaran
- Catat transaksi pembayaran SPP baru
- ID transaksi dibuat otomatis dengan format `PAYxxx`
- Perhitungan kembalian dilakukan secara otomatis
- Ubah dan hapus transaksi yang sudah ada
- Cetak bukti pembayaran per transaksi

### 📑 Laporan Pembayaran
- Tampil semua transaksi dalam tabel
- Filter berdasarkan bulan dan tahun
- Filter berdasarkan rentang tanggal kustom
- Cetak laporan langsung dari browser

### 🔍 Cek Pembayaran
- Cek status pembayaran siswa berdasarkan NISN
- Menampilkan tanggal bayar, status, dan jumlah bulan

---

## 📸 Tangkapan Layar

> Tambahkan screenshot aplikasi di sini.

---

## 👨‍💻 Pengembang

| | |
|---|---|
| **Nama** | Muhammad Rifqi Saifulloh |
| **GitHub** | [@mhmdrifqis](https://github.com/mhmdrifqis) |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan **tugas akademik**. Seluruh hak cipta milik pengembang.
