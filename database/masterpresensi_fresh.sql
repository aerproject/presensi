/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: masterpresensi
-- ------------------------------------------------------
-- Server version	10.11.10-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_akademik_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status_absensi_id` int(11) NOT NULL DEFAULT 1,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_absensi_harian` (`siswa_akademik_id`,`tanggal`),
  KEY `fk_absensi_status` (`status_absensi_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(100) NOT NULL,
  `wa_admin` varchar(20) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--


--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_akademik_id` int(10) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status` enum('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'alpha',
  `keterangan` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_student_date` (`siswa_akademik_id`,`tanggal`),
  KEY `fk_attendance_skt` (`siswa_akademik_id`),
  CONSTRAINT `fk_attendance_siswa_akademik` FOREIGN KEY (`siswa_akademik_id`) REFERENCES `siswa_akademik` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `broadcast`
--

DROP TABLE IF EXISTS `broadcast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `broadcast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `jenis_pesan` enum('perorangan','kelas','tingkat','jurusan') NOT NULL,
  `isi_pesan` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `tingkat` varchar(10) DEFAULT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `status` enum('draft','queued','sent','failed','partial') DEFAULT 'draft',
  `waktu_kirim` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_broadcast_kelas` (`kelas_id`),
  KEY `fk_broadcast_jurusan` (`jurusan_id`),
  KEY `student_id` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `broadcast`
--

LOCK TABLES `broadcast` WRITE;
/*!40000 ALTER TABLE `broadcast` DISABLE KEYS */;
/*!40000 ALTER TABLE `broadcast` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guru` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned DEFAULT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`),
  UNIQUE KEY `uniq_guru_users_id` (`users_id`),
  CONSTRAINT `fk_guru_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guru`
--

LOCK TABLES `guru` WRITE;
/*!40000 ALTER TABLE `guru` DISABLE KEYS */;
/*!40000 ALTER TABLE `guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hari_kerja`
--

DROP TABLE IF EXISTS `hari_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hari_kerja` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hari` varchar(10) NOT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hari_kerja`
--

LOCK TABLES `hari_kerja` WRITE;
/*!40000 ALTER TABLE `hari_kerja` DISABLE KEYS */;
INSERT INTO `hari_kerja` VALUES
(1,'Senin',1,'2026-06-29 10:31:50','2026-09-03 09:35:31'),
(2,'Selasa',1,'2026-06-29 10:31:50','2026-09-03 09:35:31'),
(3,'Rabu',1,'2026-06-29 10:31:50','2026-09-03 09:35:31'),
(4,'Kamis',1,'2026-06-29 10:31:50','2026-09-03 09:35:31'),
(5,'Jumat',1,'2026-06-29 10:31:50','2026-09-03 09:35:31'),
(6,'Sabtu',1,'2026-06-29 10:31:50','2026-09-03 09:35:31');
/*!40000 ALTER TABLE `hari_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `izin`
--

DROP TABLE IF EXISTS `izin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `izin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `siswa_akademik_id` int(11) NOT NULL,
  `ortu_id` int(11) NOT NULL,
  `jenis_izin` enum('sakit','izin') NOT NULL,
  `isi_pesan` text NOT NULL,
  `upload_surat` varchar(255) DEFAULT NULL,
  `status_izin` enum('diajukan','disetujui','ditolak') DEFAULT 'diajukan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_siswa` (`siswa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `izin`
--

LOCK TABLES `izin` WRITE;
/*!40000 ALTER TABLE `izin` DISABLE KEYS */;
/*!40000 ALTER TABLE `izin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jam_belajar`
--

DROP TABLE IF EXISTS `jam_belajar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jam_belajar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shift` enum('full day','pagi','sore') NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jam_belajar`
--

LOCK TABLES `jam_belajar` WRITE;
/*!40000 ALTER TABLE `jam_belajar` DISABLE KEYS */;
INSERT INTO `jam_belajar` VALUES
(1,'full day','07:15:00','17:30:00'),
(2,'pagi','07:15:00','13:45:00'),
(3,'sore','14:00:00','17:30:00');
/*!40000 ALTER TABLE `jam_belajar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jurusan`
--

DROP TABLE IF EXISTS `jurusan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jurusan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_jurusan` varchar(10) NOT NULL,
  `nama_jurusan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_jurusan` (`kode_jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jurusan`
--

LOCK TABLES `jurusan` WRITE;
/*!40000 ALTER TABLE `jurusan` DISABLE KEYS */;
/*!40000 ALTER TABLE `jurusan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kelas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kelas` varchar(5) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `jurusan_id` (`jurusan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas_mapping`
--

DROP TABLE IF EXISTS `kelas_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kelas_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `next_kelas_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `next_kelas_id` (`next_kelas_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas_mapping`
--

LOCK TABLES `kelas_mapping` WRITE;
/*!40000 ALTER TABLE `kelas_mapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `kelas_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libursekolah`
--

DROP TABLE IF EXISTS `libursekolah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libursekolah` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tapel_id` int(10) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tapel_tanggal` (`tapel_id`,`tanggal`),
  CONSTRAINT `fk_libur_tapel` FOREIGN KEY (`tapel_id`) REFERENCES `tapel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libursekolah`
--

LOCK TABLES `libursekolah` WRITE;
/*!40000 ALTER TABLE `libursekolah` DISABLE KEYS */;
/*!40000 ALTER TABLE `libursekolah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license_renewal_requests`
--

DROP TABLE IF EXISTS `license_renewal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `license_renewal_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `request_uuid` varchar(36) NOT NULL,
  `license_id` int(11) unsigned DEFAULT NULL,
  `application_id` int(11) unsigned DEFAULT NULL,
  `server_id` int(11) unsigned DEFAULT NULL,
  `server_uuid` varchar(255) DEFAULT NULL,
  `server_hash` varchar(255) DEFAULT NULL,
  `current_expires_at` datetime DEFAULT NULL,
  `requested_expires_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `submitted_by` int(11) unsigned DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_license_renewal_requests_request_uuid` (`request_uuid`),
  KEY `license_id` (`license_id`),
  KEY `application_id` (`application_id`),
  KEY `server_id` (`server_id`),
  KEY `server_uuid` (`server_uuid`),
  KEY `server_hash` (`server_hash`),
  KEY `status` (`status`),
  KEY `submitted_by` (`submitted_by`),
  KEY `submitted_at` (`submitted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_renewal_requests`
--


--
-- Table structure for table `license_runtime`
--

DROP TABLE IF EXISTS `license_runtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `license_runtime` (
  `id` int(11) unsigned NOT NULL DEFAULT 1,
  `license_id` int(11) unsigned DEFAULT NULL,
  `application_id` int(11) unsigned DEFAULT NULL,
  `license_type` varchar(20) DEFAULT NULL,
  `license_key_enc` text DEFAULT NULL,
  `license_key_iv` varchar(255) DEFAULT NULL,
  `license_key_tag` varchar(255) DEFAULT NULL,
  `server_id` int(11) unsigned DEFAULT NULL,
  `server_uuid` varchar(255) DEFAULT NULL,
  `server_hash` varchar(255) DEFAULT NULL,
  `activate_status` varchar(50) DEFAULT NULL,
  `validate_status` varchar(50) DEFAULT NULL,
  `app_version` varchar(50) DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `last_validation_at` datetime DEFAULT NULL,
  `last_heartbeat_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `metadata_json` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `server_uuid` (`server_uuid`),
  KEY `server_hash` (`server_hash`),
  KEY `validate_status` (`validate_status`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_runtime`
--


--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `jenis_pesan` varchar(20) NOT NULL,
  `isi_pesan` text NOT NULL,
  `waktu_kirim` datetime NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_messages_queue` (`status`,`waktu_kirim`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2026-08-12-201842','App\\Database\\Migrations\\AddUniqueAttendancePerStudentDate','default','App',1786566030,1),
(2,'2026-08-12-210542','App\\Database\\Migrations\\AddMessageQueueIndex','default','App',1786568832,2),
(3,'2026-08-13-024010','App\\Database\\Migrations\\AddErrorMessageToMessages','default','App',1786588913,3),
(4,'2026-08-14-012417','App\\Database\\Migrations\\CreateLicenseRuntimeTable','default','App',1786671158,4),
(5,'2026-08-15-213500','App\\Database\\Migrations\\GLI03D2_AddLicenseStateFields','default','App',1786804560,5),
(8,'2026-08-26-034649','App\\Database\\Migrations\\CreateLicenseRenewalRequestsTable','default','App',1787730243,6),
(9,'2026-08-26-210818','App\\Database\\Migrations\\AddRequestUuidToLicenseRenewalRequestsTable','default','App',1787778896,7),
(10,'2026-08-26-211716','App\\Database\\Migrations\\AddUniqueRequestUuidIndexToLicenseRenewalRequestsTable','default','App',1787779244,8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_ortu` varchar(100) NOT NULL,
  `wa_ortu` varchar(20) DEFAULT NULL,
  `users_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_parents_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
/*!40000 ALTER TABLE `parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_aplikasi` varchar(100) NOT NULL,
  `nama_sekolah` varchar(150) NOT NULL,
  `tahun_pelajaran` varchar(20) NOT NULL,
  `kop_surat` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aktif` tinyint(1) NOT NULL DEFAULT 0,
  `batas_masuk_sebelum` int(11) NOT NULL DEFAULT 60,
  `batas_masuk_sesudah` int(11) NOT NULL DEFAULT 120,
  `batas_pulang_sebelum` int(11) NOT NULL DEFAULT 0,
  `batas_pulang_sesudah` int(11) NOT NULL DEFAULT 180,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan`
--

LOCK TABLES `pengaturan` WRITE;
/*!40000 ALTER TABLE `pengaturan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengaturan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan_bak_batas_absensi`
--

DROP TABLE IF EXISTS `pengaturan_bak_batas_absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengaturan_bak_batas_absensi` (
  `id` int(11) NOT NULL DEFAULT 0,
  `nama_aplikasi` varchar(100) NOT NULL,
  `nama_sekolah` varchar(150) NOT NULL,
  `tahun_pelajaran` varchar(20) NOT NULL,
  `kop_surat` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aktif` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan_bak_batas_absensi`
--


--
-- Table structure for table `plot_kelas`
--

DROP TABLE IF EXISTS `plot_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plot_kelas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` int(10) unsigned NOT NULL,
  `kelas_id` int(10) unsigned NOT NULL,
  `tapel_id` int(10) unsigned NOT NULL,
  `shift` enum('pagi','siang','malam') DEFAULT 'pagi',
  `jam_masuk` time DEFAULT '07:00:00',
  `jam_pulang` time DEFAULT '14:00:00',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_siswa_tapel` (`siswa_id`,`tapel_id`),
  KEY `fk_plot_kelas` (`kelas_id`),
  KEY `fk_plot_tapel` (`tapel_id`),
  CONSTRAINT `fk_plot_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_plot_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_plot_tapel` FOREIGN KEY (`tapel_id`) REFERENCES `tapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plot_kelas`
--

LOCK TABLES `plot_kelas` WRITE;
/*!40000 ALTER TABLE `plot_kelas` DISABLE KEYS */;
/*!40000 ALTER TABLE `plot_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qrcodes`
--

DROP TABLE IF EXISTS `qrcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qrcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(50) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qrcode` (`nis`,`kelas_id`),
  KEY `fk_qrcodes_kelas` (`kelas_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qrcodes`
--

LOCK TABLES `qrcodes` WRITE;
/*!40000 ALTER TABLE `qrcodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `qrcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `wa_siswa` varchar(20) DEFAULT NULL,
  `users_id` int(10) unsigned DEFAULT NULL,
  `kelas_id` int(10) unsigned DEFAULT NULL,
  `jurusan_id` int(10) unsigned DEFAULT NULL,
  `ortu_id` int(10) unsigned DEFAULT NULL,
  `status_siswa_id` int(10) unsigned NOT NULL DEFAULT 1,
  `tahun_masuk` year(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  KEY `fk_students_users` (`users_id`),
  KEY `fk_students_kelas` (`kelas_id`),
  KEY `fk_students_jurusan` (`jurusan_id`),
  KEY `fk_students_ortu` (`ortu_id`),
  KEY `fk_siswa_status` (`status_siswa_id`),
  CONSTRAINT `fk_siswa_status` FOREIGN KEY (`status_siswa_id`) REFERENCES `status_siswa` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_siswa_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa_akademik`
--

DROP TABLE IF EXISTS `siswa_akademik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa_akademik` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `jurusan_id` int(10) unsigned DEFAULT NULL,
  `tapel_id` int(11) NOT NULL,
  `jam_belajar_id` int(10) unsigned NOT NULL,
  `status_akademik_id` int(11) NOT NULL DEFAULT 1,
  `tanggal_naik_kelas` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_siswa_akademik_siswa` (`siswa_id`),
  KEY `fk_siswa_akademik_kelas` (`kelas_id`),
  KEY `fk_siswa_akademik_tapel` (`tapel_id`),
  KEY `fk_siswa_akademik_status` (`status_akademik_id`),
  KEY `fk_sa_jurusan` (`jurusan_id`),
  KEY `fk_siswaakademik_jambelajar` (`jam_belajar_id`),
  CONSTRAINT `fk_siswaakademik_jambelajar` FOREIGN KEY (`jam_belajar_id`) REFERENCES `jam_belajar` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa_akademik`
--

LOCK TABLES `siswa_akademik` WRITE;
/*!40000 ALTER TABLE `siswa_akademik` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa_akademik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa_kelas_tapel`
--

DROP TABLE IF EXISTS `siswa_kelas_tapel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa_kelas_tapel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` int(10) unsigned NOT NULL,
  `kelas_id` int(10) unsigned NOT NULL,
  `tapel_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_kelas` (`kelas_id`),
  KEY `fk_tapel` (`tapel_id`),
  KEY `idx_siswa_tapel` (`siswa_id`,`tapel_id`),
  CONSTRAINT `fk_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`),
  CONSTRAINT `fk_tapel` FOREIGN KEY (`tapel_id`) REFERENCES `tapel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa_kelas_tapel`
--

LOCK TABLES `siswa_kelas_tapel` WRITE;
/*!40000 ALTER TABLE `siswa_kelas_tapel` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa_kelas_tapel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_absensi`
--

DROP TABLE IF EXISTS `status_absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_status` (`nama_status`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_absensi`
--

LOCK TABLES `status_absensi` WRITE;
/*!40000 ALTER TABLE `status_absensi` DISABLE KEYS */;
INSERT INTO `status_absensi` VALUES
(1,'hadir','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(2,'izin','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(3,'sakit','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(4,'alpha','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(5,'terlambat','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(6,'dispensasi','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(7,'dinas luar','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(8,'PKL','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(9,'ujian','2026-06-22 03:54:46','2026-06-22 03:54:46'),
(10,'libur khusus','2026-06-22 03:54:46','2026-06-22 03:54:46');
/*!40000 ALTER TABLE `status_absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_akademik`
--

DROP TABLE IF EXISTS `status_akademik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_akademik` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_status` (`nama_status`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_akademik`
--

LOCK TABLES `status_akademik` WRITE;
/*!40000 ALTER TABLE `status_akademik` DISABLE KEYS */;
INSERT INTO `status_akademik` VALUES
(1,'aktif',NULL,NULL),
(2,'naik_kelas',NULL,NULL),
(3,'tinggal_kelas',NULL,NULL),
(4,'pindah',NULL,NULL);
/*!40000 ALTER TABLE `status_akademik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_siswa`
--

DROP TABLE IF EXISTS `status_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_siswa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_status` (`nama_status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_siswa`
--

LOCK TABLES `status_siswa` WRITE;
/*!40000 ALTER TABLE `status_siswa` DISABLE KEYS */;
INSERT INTO `status_siswa` VALUES
(1,'aktif',NULL,NULL),
(2,'lulus',NULL,NULL),
(3,'mutasi',NULL,NULL),
(4,'keluar',NULL,NULL),
(5,'dropout',NULL,NULL);
/*!40000 ALTER TABLE `status_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surat`
--

DROP TABLE IF EXISTS `surat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `ortu_id` int(11) NOT NULL,
  `jenis` enum('izin','sakit') DEFAULT 'izin',
  `tanggal_absensi` date DEFAULT NULL,
  `isi_pesan` text DEFAULT NULL,
  `status` enum('diajukan','ditangguhkan','disetujui','ditolak') NOT NULL DEFAULT 'diajukan',
  `upload_surat` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_surat_ortu` (`ortu_id`),
  KEY `fk_surat_siswa` (`siswa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surat`
--

LOCK TABLES `surat` WRITE;
/*!40000 ALTER TABLE `surat` DISABLE KEYS */;
/*!40000 ALTER TABLE `surat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tapel`
--

DROP TABLE IF EXISTS `tapel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tapel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tahun_pelajaran` varchar(20) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_tapel_semester` (`tahun_pelajaran`,`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tapel`
--

LOCK TABLES `tapel` WRITE;
/*!40000 ALTER TABLE `tapel` DISABLE KEYS */;
/*!40000 ALTER TABLE `tapel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','walikelas','ortu','siswa') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--


--
-- Table structure for table `wa_message_logs`
--

DROP TABLE IF EXISTS `wa_message_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_message_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `message_id` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_message_logs`
--

LOCK TABLES `wa_message_logs` WRITE;
/*!40000 ALTER TABLE `wa_message_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `wa_message_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `walikelas`
--

DROP TABLE IF EXISTS `walikelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `walikelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(10) unsigned NOT NULL,
  `tapel_id` int(10) unsigned NOT NULL,
  `guru_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `skema_absen` enum('full_day','blok_time') NOT NULL DEFAULT 'full_day',
  `sesi` enum('pagi','siang') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_walikelas` (`kelas_id`,`tapel_id`),
  KEY `fk_walikelas_tapel` (`tapel_id`),
  KEY `fk_walikelas_guru` (`guru_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `walikelas`
--

LOCK TABLES `walikelas` WRITE;
/*!40000 ALTER TABLE `walikelas` DISABLE KEYS */;
/*!40000 ALTER TABLE `walikelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wapikey`
--

DROP TABLE IF EXISTS `wapikey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wapikey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(50) NOT NULL,
  `wa_api_url` varchar(255) NOT NULL,
  `wa_api_key` varchar(255) NOT NULL,
  `admin_phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wapikey`
--


--
-- Dumping events for database 'absensiku'
--

--
-- Dumping routines for database 'absensiku'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 21:09:00
