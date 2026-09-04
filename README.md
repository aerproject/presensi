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

```text
presensi/
├── app/
├── public/
├── system/
├── writable/
├── tests/
│
├── installer/
│   ├── install.php
│   ├── Installer.php
│   ├── Preflight.php
│   ├── DatabaseInstaller.php
│   ├── LicenseInstaller.php
│   ├── SchedulerInstaller.php
│   ├── WhatsAppWorkerScheduler.php
│   ├── AlphaScheduler.php
│   └── Rollback.php
│
├── scripts/
│   ├── install.sh
│   ├── update.sh
│   └── uninstall.sh
│
├── docs/
│   ├── INSTALL.md
│   ├── LICENSE.md
│   ├── SCHEDULER.md
│   └── UPGRADE.md
│
├── .env.example
├── .gitignore
├── composer.json
├── spark
└── README.md


