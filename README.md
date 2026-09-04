# Presensi Digital

**Master Repository — AerProject**

Repository utama untuk pengembangan, distribusi, instalasi, dan pemeliharaan aplikasi **Presensi Digital**.

> Reference implementation: `presensi.duamei.web.id`

---

## 🎯 Tentang Project

Presensi Digital adalah aplikasi absensi siswa yang dirancang untuk lingkungan sekolah, dengan dukungan:

- Absensi siswa secara real-time
- Manajemen data siswa dan penempatan akademik
- Pengajuan izin / sakit oleh orang tua
- Persetujuan pengajuan oleh operator/admin
- Dashboard publik status kehadiran
- Penandaan Alpha otomatis
- Integrasi WhatsApp Gateway
- Sistem License Trial dan Full
- Installer otomatis
- Scheduler otomatis
- Mekanisme update dan maintenance

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


### Commit-nya

Di bagian bawah editor GitHub:

**Commit message:**

```text
MASTER-01 — Repository Foundation
