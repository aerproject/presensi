# Presensi Digital

**School Attendance Management System**

Repository resmi **AerProject** untuk pengembangan, distribusi, instalasi, pembaruan, dan pemeliharaan aplikasi **Presensi Digital**.

> Reference implementation: `presensi.duamei.web.id`

---

## 🎯 Tentang Project

**Presensi Digital** adalah sistem manajemen kehadiran siswa berbasis web untuk mengelola presensi, memantau kehadiran, menangani pengajuan izin, dan menyampaikan informasi kepada orang tua melalui WhatsApp Gateway.

### Fitur Utama

- Manajemen siswa, guru, kelas, dan tahun pelajaran
- Penempatan siswa berdasarkan tahun pelajaran
- Pencatatan dan rekapitulasi absensi
- Pengajuan izin dan sakit serta persetujuan orang tua
- Penetapan status Alpha otomatis
- Dashboard monitoring dan laporan kehadiran
- Integrasi WhatsApp Gateway
- Lisensi Trial dan Full
- Installer GUI lima tahap
- Scheduler dan pemeliharaan aplikasi

---

## 🏗️ Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | CodeIgniter 4 |
| Bahasa | PHP |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap, JavaScript |
| Web Server | Nginx |
| Panel Hosting | aaPanel |
| WhatsApp Gateway | Node.js / Baileys |
| License Management | License Server AerProject |
| Sistem Operasi | Linux |

Lingkungan instalasi yang telah diuji: **Ubuntu 24.04 LTS, aaPanel, Nginx, PHP 8.3, MySQL/MariaDB, dan HTTPS**.

---

## ⚙️ Persyaratan Instalasi

1. Server menggunakan aaPanel, Nginx, PHP, dan MySQL/MariaDB.
2. Domain telah diarahkan ke server dan HTTPS aktif.
3. Paket ZIP aplikasi tersedia beserta dependensi yang diperlukan.
4. Server dapat terhubung ke License Server AerProject.

---

## 🚀 Panduan Instalasi Presensi Digital

Instalasi aplikasi dilakukan melalui **installer web (GUI) dalam lima tahap**.

### Persiapan Awal

#### 1. Download Source Aplikasi

Buka [repository Presensi Digital](https://github.com/aerproject/presensi), pilih branch atau rilis distribusi yang sesuai, lalu klik **Code → Download ZIP**.

#### 2. Ekstrak Aplikasi

Upload dan ekstrak ZIP melalui **aaPanel → Files** ke direktori website, misalnya:

```text
/www/wwwroot/presensi.domain-sekolah.id/
```

Pastikan file aplikasi berada langsung dalam direktori website, tanpa folder tambahan hasil ekstraksi.

#### 3. Atur Document Root dan URL Rewrite

Pada **aaPanel → Website → Pengaturan Domain**, atur document root ke:

```text
/www/wwwroot/presensi.domain-sekolah.id/public
```

Pada menu **URL Rewrite**, gunakan konfigurasi Nginx berikut:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Simpan konfigurasi dan pastikan tidak terdapat blok `location /` yang bertentangan.

#### 4. Konfigurasi PHP

Pada pengaturan PHP di aaPanel, buka **Disabled Functions**. Jika `putenv` tercantum, hapus **hanya `putenv`**, lalu simpan dan restart PHP jika diperlukan.

#### 5. Buka Halaman Installer

Akses alamat berikut dan sesuaikan dengan domain website:

```text
https://presensi.domain-sekolah.id/trial/install
```

---

## 🧩 Tahap 1 — Konfigurasi Awal

Installer memeriksa kesiapan sistem, menyiapkan `.env` otomatis jika belum ada, dan menyimpan Base URL aplikasi. Lanjutkan setelah pemeriksaan berhasil.

## 🔐 Tahap 2 — Pengecekan Installation UUID

Installer memeriksa identitas instalasi melalui **License Server AerProject** untuk menentukan status instalasi dan lisensi.

## 🗄️ Tahap 3 — Database Setup

Masukkan konfigurasi MySQL/MariaDB, uji koneksi, lalu jalankan penyiapan database melalui installer hingga muncul status **DATABASE READY**.

## 🔑 Tahap 4 — Konfigurasi dan Aktivasi Trial

Lakukan aktivasi melalui **License Server AerProject** dan pastikan status lisensi **ACTIVE** serta validasi **VALID**.

## ✅ Tahap 5 — Final Installation

Buat akun administrator, selesaikan instalasi, lalu pilih **SELESAI & LOGIN** untuk masuk ke aplikasi.

---

## 🖥️ Verifikasi Setelah Instalasi

Pastikan website dapat diakses melalui HTTPS, halaman login berfungsi, administrator dapat masuk, dashboard tampil, koneksi database berjalan, dan lisensi aktif serta valid.

Pada instalasi baru, statistik dashboard dapat bernilai nol karena belum ada data operasional.

---

## 📚 Dokumentasi Tambahan

Panduan teknis lebih lanjut: [Panduan Instalasi Presensi Digital](docs/INSTALL.md).

---

## 🔄 Pengembangan dan Pemeliharaan

Pengembangan dilakukan melalui repository resmi AerProject. Setiap pembaruan perlu diperiksa dan diuji sebelum diterapkan pada production. Pembaruan source tidak mengharuskan instalasi baru diulang.

---

## 📌 Informasi Project

- **Nama:** Presensi Digital
- **Kategori:** School Attendance Management System
- **Developer:** AerProject
- **Framework:** CodeIgniter 4
- **License Management:** AerProject License Server
- **Repository:** https://github.com/aerproject/presensi

---

**Presensi Digital — AerProject © 2026**

*School Attendance Management System*
