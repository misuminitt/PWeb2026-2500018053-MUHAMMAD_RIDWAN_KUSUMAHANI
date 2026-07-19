# GudangKu - Sistem Pengelolaan Stok Barang Gudang

**Nama:** Muhammad Ridwan Kusumahani  
**NIM:** 2500018053  
**Kelas:** A

## Teknologi
HTML, CSS, JavaScript, PHP 8, dan SQLite (PDO). Tidak menggunakan CMS atau framework.

## Cara menjalankan
1. Salin folder proyek ke `htdocs` (XAMPP) atau direktori web server PHP.
2. Pastikan ekstensi `pdo_sqlite` dan `sqlite3` aktif.
3. Buka `http://localhost/PWeb_TA_2500018053_Muhammad_Ridwan_Kusumahani/`.
4. Database dan data contoh dibuat otomatis pada folder `data` saat pertama kali dibuka.

Alternatif terminal:
```bash
php -S localhost:8000
```
Lalu buka `http://localhost:8000`.

## Fitur
- Dashboard ringkasan stok
- CRUD data barang
- Pencarian barang
- Transaksi stok masuk dan keluar
- Validasi stok keluar
- Peringatan stok minimum
- Laporan berdasarkan periode dan fitur cetak
- Tampilan responsif
