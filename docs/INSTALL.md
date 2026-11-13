# Panduan Instalasi Presensi Digital

Panduan instalasi baru Presensi Digital melalui GUI lima tahap.

## 1. Persiapan Server

Lingkungan yang berhasil diuji:
- Ubuntu 24.04
- aaPanel dan Nginx
- PHP 8.3
- MySQL/MariaDB
- HTTPS
- CodeIgniter 4

Pastikan domain sudah mengarah ke server.

## 2. Download dan Ekstraksi ZIP

Unduh source dari repository GitHub AerProject Presensi.

Ekstrak ZIP ke direktori website, misalnya:

    /www/wwwroot/domain-sekolah.example/

Pastikan direktori root berisi app/, public/, writable/,
database/, composer.json, spark, dan .env.example.

Jangan menimpa aplikasi production yang sudah berjalan.

## 3. Pengaturan putenv — WAJIB

Segera setelah ekstraksi ZIP dan sebelum membuka installer:

1. Buka aaPanel.
2. Pilih PHP yang digunakan website.
3. Buka pengaturan Disabled Functions.
4. Cari fungsi putenv.
5. Jika ada, hapus hanya putenv dari daftar.
6. Simpan perubahan dan restart PHP.

Jangan menghapus fungsi lain dari Disabled Functions.

Fungsi putenv diperlukan oleh pemrosesan environment
CodeIgniter 4 pada lingkungan yang diuji.

## 4. Konfigurasi Website

Atur document root Nginx ke direktori public/ aplikasi.

Contoh:

    /www/wwwroot/domain-sekolah.example/public

Pastikan HTTPS, PHP, rewrite URL, dan izin tulis
direktori writable/ telah dikonfigurasi.

Jangan memberikan permission 777 secara menyeluruh.

## 5. Dependensi Composer

Jika vendor/ belum tersedia, jalankan dari root aplikasi:

    composer install --no-dev --prefer-dist --optimize-autoloader

Pastikan Composer dan PHP CLI menggunakan lingkungan
yang kompatibel dengan aplikasi.

## 6. Buka Installer GUI

Akses:

    https://domain-sekolah.example/trial/install

Lakukan instalasi mengikuti lima tahap berikut.

## Tahap 1 — Konfigurasi Awal

1. Klik CEK PEMERIKSAAN SISTEM.
2. Pastikan seluruh indikator READY.
3. Installer menyiapkan .env dari .env.example jika belum ada.
4. File .env yang sudah tersedia tidak ditimpa saat persiapan.
5. Masukkan Base URL HTTPS aplikasi.
6. Klik SIMPAN BASE URL.
7. Pastikan Base URL tersimpan.
8. Klik CEK INSTALLATION UUID.

Contoh Base URL:

    https://domain-sekolah.example/

## Tahap 2 — Pengecekan Installation UUID

Installer memeriksa UUID ke License Server.

Keputusan yang mungkin diterima:
- NEW_TRIAL
- EXISTING_TRIAL
- EXISTING_FULL

Ikuti keputusan dan navigasi GUI.

Pengecekan UUID bukan aktivasi lisensi.
Jangan mengganti UUID untuk menghindari pemeriksaan lisensi.

## Tahap 3 — Database Setup

1. Masukkan hostname database.
2. Masukkan nama database.
3. Masukkan username dan password database.
4. Jalankan pemeriksaan koneksi.
5. Ikuti proses impor Master SQL melalui GUI.
6. Pastikan status DATABASE READY.

Master SQL instalasi baru:

    database/masterpresensi_fresh.sql

Gunakan database baru. Jangan mengimpor Master SQL
ke database production yang sudah berisi data.

## Tahap 4 — Konfigurasi dan Aktivasi Trial

Lanjutkan proses konfigurasi melalui GUI.

Installer menyiapkan konfigurasi aplikasi, melakukan
aktivasi dan validasi Trial, serta pemeriksaan runtime.

Pastikan hasil aktivasi dan validasi berhasil.

Status yang diharapkan:

    ACTIVE / VALID

Jangan menyalin atau mengubah kredensial lisensi secara manual.

## Tahap 5 — Final Installation

1. Isi formulir pembuatan administrator.
2. Jalankan finalisasi instalasi.
3. Pastikan instalasi selesai.
4. Klik SELESAI & LOGIN.

## 7. Verifikasi Login

Login menggunakan akun administrator yang baru dibuat.

Pastikan:
- Login berhasil melalui HTTPS.
- Dashboard Admin tampil.
- Tidak ada error aplikasi.
- Status lisensi sesuai.

Pada instalasi baru, statistik dashboard dapat bernilai nol
sebelum data operasional dimasukkan.

## 8. Keamanan

- Jangan mengunggah .env ke GitHub.
- Jangan membagikan password atau API key.
- Jangan menghapus Installation UUID.
- Jangan mengulang impor database pada instalasi aktif.
- Simpan backup konfigurasi dan database secara aman.
- Gunakan HTTPS.
- Pastikan document root tetap mengarah ke public/.

## 9. Catatan

Panduan ini ditujukan untuk instalasi baru.

Aplikasi yang telah berhasil diinstal tidak perlu
menjalankan ulang proses instalasi.
