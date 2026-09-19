# Trial Installation Architecture & Development Guide

## 1. Tujuan

Dokumen ini menjadi panduan standar untuk merancang, mengembangkan,
menguji, dan memelihara mekanisme instalasi Trial pada aplikasi
CodeIgniter 4 yang terintegrasi dengan License Server.

Implementasi menggunakan lima tahap utama:

1. Initial Check
2. Installation Identity & Trial Decision
3. Database Preparation
4. Application Configuration & License Bootstrap
5. Administrator Creation

Prinsip utama:

> Instalasi Trial tidak dianggap selesai hanya karena database berhasil dibuat.
> Instalasi baru dianggap selesai setelah credential lisensi diperoleh,
> disimpan secara aman, activation berhasil, validation berhasil,
> runtime license terverifikasi, dan administrator berhasil dibuat.

---

## 2. Arsitektur Umum

```text
Browser
   |
   v
TrialInstall Controller
   |
   +-- Initial Checks
   |
   +-- InstallationIdentity
   |
   +-- TrialBootstrapClient
   |       |
   |       +--> License Server
   |
   +-- EnvWriter
   |
   +-- LicenseService
   |       |
   |       +--> LicenseClient
   |               |
   |               +--> License Server
   |
   +-- LicenseCredentialStore
   |
   +-- Database / license_runtime
   |
   v
Application Ready
```

---

## 3. Lifecycle Installer

```text
STAGE 1 — Initial Check
        ↓
STAGE 2 — Installation UUID & Trial Decision
        ↓
STAGE 3 — Database Preparation
        ↓
STAGE 4 — Configuration & License Bootstrap
        ↓
Trial Claim → Credential Store → Activation → Validation
        ↓
Final Runtime Verification
        ↓
STAGE 5 — Administrator Creation
        ↓
APPLICATION READY
```

Decision pada Stage 2:

```text
NEW_TRIAL       → lanjut ke Stage 3
EXISTING_TRIAL  → recovery / upgrade path
EXISTING_FULL   → Full License state
```

---

## 4. Prinsip Arsitektur

Installer harus:

- deterministic;
- tidak membuat Trial ganda;
- tidak menimpa Full License;
- tidak membocorkan credential;
- tidak bergantung pada modifikasi vendor;
- dapat dilanjutkan pada tahap yang aman;
- memisahkan identitas instalasi dari identitas server;
- melakukan validation setelah activation;
- hanya membuat administrator setelah license valid.

Urutan utama:

```text
IDENTITY
   ↓
DECISION
   ↓
DATABASE
   ↓
CONFIGURATION
   ↓
CREDENTIAL
   ↓
ACTIVATION
   ↓
VALIDATION
   ↓
ADMINISTRATOR
```

---

## 5. Stage 1 — Initial Check

### Tujuan

Memastikan lingkungan aplikasi siap sebelum instalasi dilanjutkan.

### Controller

`app/Controllers/TrialInstall.php`

Method utama:
- `index()`
- `start()`

### Route

- `GET /trial/install`
- `POST /trial/install`

### View

`app/Views/trial/initial.php`

Stage 1 hanya melakukan pemeriksaan kesiapan.

Stage ini tidak boleh:
- membuat Trial License;
- menyimpan API secret;
- mengaktifkan lisensi;
- membuat administrator.

Jika seluruh pemeriksaan berhasil:

```text
trial_install_stage_1_complete = true
```

Kemudian proses dilanjutkan ke:

```text
/trial/install/uuid
```

---

## 6. Stage 2 — Installation Identity & Trial Decision

### 6.1 Installation UUID

File:

`app/Libraries/Installer/InstallationIdentity.php`

UUID disimpan pada:

`writable/installation/installation_uuid`

Karakteristik:
- stabil selama instalasi aplikasi;
- dibuat hanya jika belum tersedia;
- tidak diganti setiap request;
- digunakan untuk identifikasi instalasi Trial.

### Penting

Installation UUID bukan:
- `server_uuid`;
- `server_hash`.

Ketiga identitas tersebut memiliki fungsi berbeda.

### 6.2 Trial Check

Client:

`app/Libraries/License/TrialBootstrapClient.php`

Endpoint:

`POST /api/v1/trial/check`

Request:

```json
{
  "installation_uuid": "...",
  "app_version": "1.0.0"
}
```

Header:

```text
Content-Type: application/json
Accept: application/json
X-Timestamp
X-Nonce
X-App-Version
```

Response:

```json
{
  "success": true,
  "data": {
    "install_status": "NEW_TRIAL",
    "can_continue": true,
    "status": "...",
    "expires_at": "..."
  }
}
```

Status yang dikenali:
- `NEW_TRIAL`
- `EXISTING_TRIAL`
- `EXISTING_FULL`

`trial/check` bersifat read-only.

Endpoint ini tidak boleh membuat Trial atau mengeluarkan credential.

---

## 7. Stage 3 — Database Preparation

### Route

- `GET /trial/install/database`
- `POST /trial/install/database`

### Controller

`TrialInstall::database()`

### View

`app/Views/trial/database.php`

### Master SQL

`database/masterpresensi_fresh.sql`

### Input

- hostname
- port
- database
- username
- password

### Proses

1. Validasi input.
2. Koneksi database menggunakan mysqli.
3. Set charset `utf8mb4`.
4. Import master SQL.
5. Proses seluruh result set.
6. Simpan konfigurasi database ke session installer.

Session state:

```text
trial_install_database
trial_install_database_tested
trial_install_database_imported
trial_install_env_written
```

Database harus berhasil dipersiapkan sebelum Stage 4.

---

## 8. Stage 4 — Configuration & License Bootstrap

### Route

- `GET /trial/install/config`
- `POST /trial/install/config`
- `POST /trial/install/config/process`
- `POST /trial/install/bootstrap`

### View

- `app/Views/trial/configuration.php`
- `app/Views/trial/license-process.php`

### Library

- `app/Libraries/Installer/EnvWriter.php`
- `app/Libraries/License/TrialBootstrapClient.php`

### 8.1 Menulis `.env`

`EnvWriter` menulis konfigurasi hasil instalasi ke `.env`.

Nilai utama:

```text
CI_ENVIRONMENT
app.baseURL

database.default.hostname
database.default.database
database.default.username
database.default.password
database.default.port

encryption.key

LICENSE_BASE_URL
LICENSE_APP_CODE
LICENSE_APP_VERSION
LICENSE_TIMEOUT
LICENSE_API_KEY
LICENSE_API_SECRET
LICENSE_SECRET
```

Pada awal konfigurasi:

```text
LICENSE_API_KEY =
LICENSE_API_SECRET =
```

Credential baru diisi setelah Trial Claim berhasil.

`.env` tidak boleh masuk repository.
`.env.example` hanya menjadi template.

---

## 9. Trial Claim

Endpoint:

`POST /api/v1/trial/claim`

Request:

```json
{
  "installation_uuid": "...",
  "application_code": "PRESENSI",
  "app_version": "1.0.0"
}
```

Header:

```text
Content-Type: application/json
Accept: application/json
X-Timestamp
X-Nonce
X-App-Version
```

Response Trial baru:

```json
{
  "success": true,
  "data": {
    "install_status": "NEW_TRIAL",
    "license_type": "trial",
    "installation_uuid": "...",
    "license_key": "...",
    "api_key": "...",
    "api_secret": "...",
    "status": "...",
    "expires_at": "..."
  }
}
```

Credential yang diharapkan:
- `license_key`
- `api_key`
- `api_secret`

### Status khusus

`EXISTING_TRIAL`:

Installer tidak membuat Trial kedua.

`EXISTING_FULL`:

Installer tidak menimpa lisensi Full.

---

## 10. Penyimpanan Credential

API credential tidak boleh disimpan sebagai plaintext jika storage terenkripsi tersedia.

Credential diproses menggunakan:
- `LicenseCredentialStore`
- `LicenseCrypto`

License key disimpan melalui mekanisme storage lisensi aplikasi.

Log hanya boleh menunjukkan status keberadaan credential, bukan nilainya.

Contoh aman:

```text
api_key_present=true
api_secret_present=true
```

Bukan nilai credential sebenarnya.

---

## 11. License Activation

Client:

`app/Libraries/License/LicenseClient.php`

Service:

`app/Libraries/License/LicenseService.php`

Endpoint:

`POST /api/v1/activation`

Payload:

```json
{
  "license_key": "...",
  "server_uuid": "...",
  "hostname": "...",
  "domain": "...",
  "ip_address": "...",
  "mac_address": "...",
  "os_name": "...",
  "php_version": "...",
  "app_version": "1.0.0"
}
```

Identity berasal dari:

`app/Libraries/License/ServerIdentity.php`

Header keamanan:

```text
X-API-Key
X-Timestamp
X-Nonce
X-App-Version
X-Signature
```

Response yang digunakan aplikasi:

```json
{
  "success": true,
  "message": "...",
  "data": {
    "server_id": "...",
    "server_hash": "...",
    "license_type": "trial",
    "status": "active",
    "activated_at": "...",
    "expires_at": "..."
  }
}
```

Pemetaan ke `license_runtime`:

| Response | Runtime |
|---|---|
| `server_id` | `server_id` |
| `server_hash` | `server_hash` |
| `license_type` | `license_type` |
| `status` | `activate_status` |
| `activated_at` | `activated_at` |
| `expires_at` | `expires_at` |
| seluruh `data` | `metadata_json.activation` |

---

## 12. License Validation

Endpoint:

`POST /api/v1/validation`

Request:

```json
{
  "license_key": "...",
  "server_uuid": "...",
  "server_hash": "...",
  "app_version": "1.0.0"
}
```

Response minimal:

```json
{
  "success": true,
  "message": "...",
  "data": {
    "status": "valid",
    "license_type": "trial",
    "expires_at": "..."
  }
}
```

Pemetaan:

| Response | Runtime |
|---|---|
| `data.status` | `validate_status` |
| `data.license_type` | `license_type` |
| `data.expires_at` | `expires_at` |
| seluruh `data` | `metadata_json.validation` |
| waktu lokal | `last_validation_at` |

Validation harus berhasil setelah activation.

---

## 13. Final Verification Stage 4

Installer memverifikasi:
- tabel `users` tersedia;
- tabel `admin` tersedia;
- tabel `license_runtime` tersedia;
- runtime license tersedia;
- `validate_status = valid`;
- `.env` tersedia;
- `.env` dapat dibaca.

Status akhir Stage 4:

```text
license_type    = trial
activate_status = active
validate_status = valid
```

Jika semua terpenuhi:

```text
STAGE 4 SUCCESS
```

Installer dapat membuka Stage 5.

---

## 14. Stage 5 — Administrator

### Route

- `GET /trial/install/admin`
- `POST /trial/install/finalize`

### Controller

- `TrialInstall::admin()`
- `TrialInstall::finalizeAdmin()`

### View

`app/Views/trial/final.php`

### Prasyarat

```text
license_type    = trial
activate_status = active
validate_status = valid
```

### Input

- `nama_admin`
- `wa_admin`
- `username`
- `email`
- `password`
- `password_confirm`

Password disimpan menggunakan:

```php
password_hash($password, PASSWORD_DEFAULT)
```

Data administrator disimpan ke:

```text
users
admin
```

Menggunakan database transaction:

```text
BEGIN
  insert users
  insert admin
COMMIT
```

Jika gagal:

```text
ROLLBACK
```

---

## 15. File Aplikasi yang Diperlukan

### Core Installer

```text
app/Controllers/TrialInstall.php
app/Config/Routes.php
app/Config/License.php
app/Config/Session.php
app/Controllers/BaseController.php
app/Filters/LicenseFilter.php
```

### Installer Libraries

```text
app/Libraries/Installer/InstallationIdentity.php
app/Libraries/Installer/EnvWriter.php
```

### License Libraries

```text
app/Libraries/License/TrialBootstrapClient.php
app/Libraries/License/LicenseClient.php
app/Libraries/License/LicenseService.php
app/Libraries/License/ServerIdentity.php
app/Libraries/License/LicenseCredentialStore.php
app/Libraries/License/LicenseCrypto.php
```

### Views

```text
app/Views/trial/initial.php
app/Views/trial/install.php
app/Views/trial/database.php
app/Views/trial/configuration.php
app/Views/trial/license-process.php
app/Views/trial/final.php
```

### Database

```text
database/masterpresensi_fresh.sql
```

---

## 16. Runtime Files

Runtime files bukan source code.

Contoh:

```text
.env
writable/installation/installation_uuid
writable/license/encryption.key
writable/session/*
writable/cache/*
writable/logs/*
```

Runtime files harus dilindungi `.gitignore`.

---

## 17. Environment Variables

Minimal:

```text
CI_ENVIRONMENT
app.baseURL

database.default.hostname
database.default.database
database.default.username
database.default.password
database.default.port

encryption.key

LICENSE_BASE_URL
LICENSE_API_KEY
LICENSE_APP_CODE
LICENSE_API_SECRET
LICENSE_APP_VERSION
LICENSE_SECRET
LICENSE_TIMEOUT
```

Jangan menyimpan credential nyata di:

```text
.env.example
source code
controller
view
documentation
Git repository
```

---

## 18. Installation UUID vs Server Identity

Jangan mencampurkan tiga identitas berikut.

### Installation UUID

`installation_uuid`

Mengidentifikasi instalasi aplikasi Trial.

### Server UUID

`server_uuid`

Mengidentifikasi server/host.

### Server Hash

`server_hash`

Identitas lisensi yang dikelola License Server.

Hubungan:

```text
Installation UUID
      |
      +--> Trial Check
      +--> Trial Claim

Server Identity
      |
      +--> Activation
      +--> Validation
      +--> Heartbeat

Server Hash
      |
      +--> Validation
      +--> Heartbeat
```

---

## 19. Security Rules

1. Jangan log API secret.
2. Jangan log license key lengkap.
3. Jangan menyimpan API secret plaintext jika storage terenkripsi tersedia.
4. Jangan memasukkan `.env` ke repository.
5. Jangan memasukkan runtime license credential ke repository.
6. Jangan membuat Trial pada endpoint `trial/check`.
7. Jangan membuat Trial kedua jika status `EXISTING_TRIAL`.
8. Jangan menimpa Full License.
9. Jangan menganggap activation saja cukup.
10. Selalu lakukan validation setelah activation.
11. Jangan membuat administrator sebelum license validation berhasil.
12. Jangan mengubah Installation UUID yang sudah valid.
13. Jangan menggunakan Installation UUID sebagai pengganti Server Identity.
14. Jangan memodifikasi vendor untuk menyelesaikan masalah aplikasi.
15. Jangan menggunakan path relatif untuk konfigurasi runtime yang seharusnya berasal dari `WRITEPATH`.

---

## 20. Session Path Rule

CodeIgniter menggunakan:

```php
WRITEPATH . "session"
```

sebagai lokasi session.

Jangan menambahkan konfigurasi `.env` seperti:

```text
session.savePath = writable/session
```

karena nilai relatif dapat meng-override konfigurasi CodeIgniter dan menghasilkan path runtime yang salah.

Jika override environment memang diperlukan, gunakan absolute path yang benar.

Source configuration tetap menggunakan:

```php
public string $savePath = WRITEPATH . "session";
```

---

## 21. Recovery dan Upgrade

Recovery endpoint:

```text
POST /trial/install/recover-credential
POST /trial/install/validate
```

Upgrade endpoint:

```text
GET  /trial/upgrade
GET  /trial/upgrade/process
GET  /trial/upgrade/status
POST /trial/upgrade
POST /trial/upgrade/bootstrap
POST /trial/upgrade/activate
```

Recovery dan upgrade bukan bagian dari happy-path initial installation.

Initial installation:

```text
Check
  ↓
UUID
  ↓
Database
  ↓
Configuration
  ↓
Trial Claim
  ↓
Activation
  ↓
Validation
  ↓
Administrator
```

---

## 22. Pengembangan Aplikasi Baru

Saat membuat aplikasi baru dengan sistem Trial yang sama:

### A. Identity

Sediakan:

```text
InstallationIdentity
ServerIdentity
```

### B. License Client

Sediakan client untuk:

```text
trial/check
trial/claim
activation
validation
heartbeat
```

### C. Installer

Sediakan minimal:

```text
Initial Check
UUID Check
Database Setup
Configuration
License Bootstrap
Administrator Setup
```

### D. Runtime

Sediakan:

```text
license_runtime
encrypted credential storage
installation UUID
```

### E. Environment

Sediakan `.env.example` tanpa secret nyata.

### F. Repository

Pastikan file runtime berikut tidak masuk repository:

```text
.env
writable runtime
license keys
API secrets
installation UUID
logs
cache
session
uploads
```

---

## 23. Checklist Final

### Stage 1

- [ ] Environment check
- [ ] PHP check
- [ ] Extension check
- [ ] Writable check
- [ ] License Server connectivity check
- [ ] No license mutation

### Stage 2

- [ ] Installation UUID tersedia
- [ ] UUID stabil
- [ ] Trial check berhasil
- [ ] NEW_TRIAL / EXISTING_TRIAL / EXISTING_FULL ditangani
- [ ] Tidak membuat Trial pada check

### Stage 3

- [ ] Database connection
- [ ] Charset utf8mb4
- [ ] Master SQL import
- [ ] Database session state

### Stage 4

- [ ] `.env` generated
- [ ] encryption key tersedia
- [ ] Trial claim
- [ ] API credential tersedia
- [ ] Credential disimpan aman
- [ ] License key tersimpan
- [ ] Activation berhasil
- [ ] Validation berhasil
- [ ] Runtime verification berhasil

### Stage 5

- [ ] License runtime active
- [ ] License runtime valid
- [ ] users table
- [ ] admin table
- [ ] password hashed
- [ ] database transaction
- [ ] Administrator berhasil dibuat

---

## 24. Definition of Done

Installer Trial dianggap selesai hanya apabila:

```text
Initial Check        = READY
Trial Decision       = NEW_TRIAL
Database             = READY
Configuration        = READY
Trial Credential     = STORED
Activation           = ACTIVE
Validation           = VALID
Runtime Verification = PASS
Administrator        = CREATED
```

Status akhir:

```text
APPLICATION READY
```

---

## 25. Prinsip Utama untuk Pengembangan Berikutnya

Pertahankan urutan:

```text
IDENTITY
   ↓
DECISION
   ↓
DATABASE
   ↓
CONFIGURATION
   ↓
CREDENTIAL
   ↓
ACTIVATION
   ↓
VALIDATION
   ↓
ADMINISTRATOR
```

Jangan membalik urutan tersebut tanpa alasan arsitektural yang jelas.

Dokumen ini merupakan blueprint implementasi Trial Installer dan
integrasi License Server untuk pengembangan aplikasi berikutnya.
