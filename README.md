# Presensi Digital

**School Attendance Management System**

Repository utama untuk pengembangan, distribusi, instalasi, dan pemeliharaan aplikasi **Presensi Digital**.

> Reference implementation: `presensi.duamei.web.id`

---

## 🎯 Tentang Project

Presensi Digital adalah sistem manajemen kehadiran siswa untuk lingkungan sekolah.

Project ini mencakup:

- Manajemen data siswa
- Penempatan siswa berdasarkan tahun pelajaran
- Absensi siswa
- Izin dan sakit
- Persetujuan pengajuan orang tua
- Status Alpha otomatis
- Dashboard kehadiran
- Integrasi WhatsApp Gateway
- License Trial dan Full
- Installer aplikasi
- Scheduler otomatis
- Sistem update dan maintenance

---

## 🏗️ Teknologi

Project dikembangkan menggunakan komponen utama:

- **CodeIgniter 4**
- **PHP**
- **MySQL / MariaDB**
- **Bootstrap**
- **JavaScript**
- **WhatsApp Gateway**
- **Baileys / Node.js**
- **License Server AerProject**

Detail teknologi dapat berkembang mengikuti kebutuhan project tanpa mengubah identitas repository.

---


## 📦 Struktur Repository

Struktur utama aplikasi:

- app/ — Controller, Model, View, dan konfigurasi
- app/Controllers/TrialInstall.php — Installer GUI
- app/Libraries/Installer/ — Komponen instalasi
- app/Views/trial/ — Antarmuka instalasi
- database/masterpresensi_fresh.sql — Master SQL instalasi baru
- public/ — Document root website
- writable/ — Penyimpanan runtime
- docs/ — Dokumentasi
- .env.example — Template konfigurasi
- composer.json — Dependensi PHP

## 🚀 Instalasi Presensi Digital

Instalasi baru dilakukan melalui GUI dalam lima tahap:

1. Konfigurasi Awal
2. Pengecekan Installation UUID
3. Database Setup
4. Konfigurasi dan Aktivasi Trial
5. Final Installation

Panduan lengkap: [Instalasi Presensi Digital](docs/INSTALL.md).

**Penting:** Setelah ekstraksi ZIP, sebelum membuka
installer, hapus hanya fungsi `putenv` dari daftar
Disabled Functions PHP di aaPanel apabila masih terdaftar.

---

**AerProject © 2026**
