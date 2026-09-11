<?= $this->extend('layouts/ortu') ?>

<?= $this->section('content') ?>

<?php
$successMessage = session()->getFlashdata('success');
$errorMessage   = session()->getFlashdata('error');
$warningMessage = session()->getFlashdata('warning');
$infoMessage    = session()->getFlashdata('info');
?>

<?php if ($successMessage || $errorMessage || $warningMessage || $infoMessage): ?>

    <div class="container-fluid px-3 pt-3">

        <?php if ($successMessage): ?>

            <div class="alert alert-success alert-dismissible fade show shadow-sm"
                 role="alert">

                <div class="d-flex align-items-center">

                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>

                    <div>
                        <strong>Berhasil</strong>
                        <div><?= esc($successMessage) ?></div>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>


        <?php if ($errorMessage): ?>

            <div class="alert alert-danger alert-dismissible fade show shadow-sm"
                 role="alert">

                <div class="d-flex align-items-center">

                    <i class="bi bi-exclamation-circle-fill fs-5 me-2"></i>

                    <div>
                        <strong>Gagal</strong>
                        <div><?= esc($errorMessage) ?></div>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>


        <?php if ($warningMessage): ?>

            <div class="alert alert-warning alert-dismissible fade show shadow-sm"
                 role="alert">

                <div class="d-flex align-items-center">

                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>

                    <div>
                        <strong>Peringatan</strong>
                        <div><?= esc($warningMessage) ?></div>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>


        <?php if ($infoMessage): ?>

            <div class="alert alert-info alert-dismissible fade show shadow-sm"
                 role="alert">

                <div class="d-flex align-items-center">

                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>

                    <div>
                        <?= esc($infoMessage) ?>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>

    </div>

<?php endif; ?>



<style>
/* =========================================================
   DASHBOARD ORTU — MOBILE FIRST
   ========================================================= */

.ortu-dashboard {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 12px;
}

.ortu-dashboard * {
    box-sizing: border-box;
}

.ortu-hero {
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #0d6efd, #4f8dfd);
    color: #fff;
    box-shadow: 0 8px 24px rgba(13, 110, 253, .18);
}

.ortu-hero h4 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.ortu-hero p {
    margin: 0;
    opacity: .9;
    font-size: .9rem;
}

.ortu-child-card {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 5px 18px rgba(0,0,0,.07);
}

.ortu-child-card .card-body {
    padding: 18px;
}

.child-label {
    color: #6c757d;
    font-size: .78rem;
    margin-bottom: 2px;
}

.child-value {
    font-weight: 600;
    word-break: break-word;
}

.child-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.stat-card {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,.06);
    min-width: 0;
}

.stat-card .card-body {
    padding: 14px;
}

.stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    margin-bottom: 9px;
}

.stat-number {
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    margin-top: 5px;
    color: #6c757d;
    font-size: .75rem;
}

.section-card {
    border: 0;
    border-radius: 18px;
    box-shadow: 0 5px 18px rgba(0,0,0,.06);
    overflow: hidden;
}

.section-card .card-header {
    background: #fff;
    border-bottom: 1px solid #edf0f2;
    padding: 15px 16px;
}

.section-title {
    font-weight: 700;
    margin: 0;
    font-size: .98rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: .75rem;
    margin-top: 2px;
}

/* Desktop table */
.ortu-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.ortu-table {
    margin-bottom: 0;
    min-width: 620px;
}

.ortu-table th {
    white-space: nowrap;
    font-size: .78rem;
}

.ortu-table td {
    font-size: .82rem;
    vertical-align: middle;
}

/* Mobile card list */
.mobile-list {
    display: none;
}

.mobile-item {
    padding: 14px 16px;
    border-bottom: 1px solid #edf0f2;
}

.mobile-item:last-child {
    border-bottom: 0;
}

.mobile-item-title {
    font-weight: 600;
    font-size: .88rem;
    margin-bottom: 8px;
}

.mobile-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.mobile-meta-label {
    color: #6c757d;
    font-size: .7rem;
}

.mobile-meta-value {
    font-size: .8rem;
    font-weight: 500;
    word-break: break-word;
}

.empty-state {
    padding: 28px 16px;
    text-align: center;
    color: #6c757d;
}

.empty-state i {
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
}

.ortu-action {
    width: 100%;
}

.ortu-action .btn {
    min-height: 46px;
    border-radius: 12px;
    font-weight: 600;
}

.filter-box {
    min-width: 110px;
}

.filter-box select {
    border-radius: 10px;
    font-size: .82rem;
}

/* =========================================================
   MOBILE
   ========================================================= */
@media (max-width: 767.98px) {

    .ortu-dashboard {
        padding: 10px 12px 85px;
    }

    .ortu-hero {
        padding: 17px;
        border-radius: 16px;
    }

    .ortu-hero h4 {
        font-size: 1rem;
        line-height: 1.45;
    }

    .ortu-hero p {
        font-size: .78rem;
    }

    .ortu-child-card,
    .section-card,
    .stat-card {
        border-radius: 15px;
    }

    .stats-grid {
        gap: 8px;
    }

    .stat-card .card-body {
        padding: 12px;
    }

    .stat-icon {
        width: 34px;
        height: 34px;
        font-size: .95rem;
        margin-bottom: 7px;
    }

    .stat-number {
        font-size: 1.2rem;
    }

    .stat-label {
        font-size: .7rem;
    }

    .desktop-list {
        display: none !important;
    }

    .mobile-list {
        display: block;
    }

    .section-card .card-header {
        padding: 13px 14px;
    }

    .section-title {
        font-size: .9rem;
    }

    .filter-row {
        width: 100%;
        margin-top: 10px;
    }

    .filter-box {
        width: 100%;
    }

    .filter-box select {
        width: 100%;
    }

    .ortu-action {
        margin-top: 12px;
    }

    .ortu-action .btn {
        width: 100%;
    }
}

/* =========================================================
   TABLET / DESKTOP
   ========================================================= */
@media (min-width: 768px) {

    .ortu-dashboard {
        padding: 20px;
    }

    .ortu-hero {
        padding: 24px;
    }

    .stats-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
}
</style>

<div class="ortu-dashboard">

    <!-- =====================================================
         HERO
         ===================================================== -->
    <div class="ortu-hero">
        <h4>
            Halo, Bapak/Ibu <?= esc($namaOrtu ?? 'Orang Tua') ?> 👋
        </h4>

        <p class="mb-1">
            Wali dari
            <strong><?= esc($namaSiswa ?? '-') ?></strong>,
            <?= esc($namaKelas ?? '-') ?>
        </p>

        <p>
            Selamat datang di Dashboard Absensi Digital Orang Tua Siswa
        </p>
    </div>


    <!-- =====================================================
         INFORMASI ANAK
         ===================================================== -->
    <?php if (!empty($siswa)): ?>

        <div class="card ortu-child-card mb-3">
            <div class="card-body">

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon bg-primary-subtle text-primary mb-0">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <div>
                        <div class="fw-bold">
                            <?= esc($siswa['nama_siswa'] ?? '-') ?>
                        </div>

                        <div class="text-muted small">
                            Informasi Siswa
                        </div>
                    </div>
                </div>

                <div class="row g-3">

                    <div class="col-6 col-md-3">
                        <div class="child-label">NIS</div>
                        <div class="child-value">
                            <?= esc($siswa['nis'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="child-label">Kelas</div>
                        <div class="child-value">
                            <?= esc($siswa['nama_kelas'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="child-label">Jurusan</div>
                        <div class="child-value">
                            <?= esc($siswa['nama_jurusan'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="child-label">Status</div>
                        <div class="child-value mt-1">
                            <span class="child-status bg-success-subtle text-success">
                                <i class="bi bi-check-circle-fill"></i>
                                Terdaftar
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTIK
         ===================================================== -->
    <div class="stats-grid mb-3">

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div class="stat-number">
                    <?= esc($rekap['hadir'] ?? 0) ?>
                </div>

                <div class="stat-label">
                    Hadir
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-envelope"></i>
                </div>

                <div class="stat-number">
                    <?= esc($rekap['izin'] ?? 0) ?>
                </div>

                <div class="stat-label">
                    Izin
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-thermometer-half"></i>
                </div>

                <div class="stat-number">
                    <?= esc($rekap['sakit'] ?? 0) ?>
                </div>

                <div class="stat-label">
                    Sakit
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>

                <div class="stat-number">
                    <?= esc($rekap['alpha'] ?? 0) ?>
                </div>

                <div class="stat-label">
                    Alpha
                </div>
            </div>
        </div>

    </div>


    <!-- =====================================================
         PENGAJUAN IZIN
         ===================================================== -->
    <div class="ortu-action mb-3">
        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalIzin">

            <i class="bi bi-send me-1"></i>
            Kirim Surat Izin
        </button>
    </div>


    <!-- =====================================================
         RIWAYAT ABSENSI
         ===================================================== -->
    <div class="card section-card mb-3">

        <div class="card-header">

            <div class="d-flex flex-column flex-md-row
                        justify-content-between
                        align-items-md-center">

                <div>
                    <h6 class="section-title">
                        Riwayat Absensi
                    </h6>

                    <div class="section-subtitle">
                        Rekap kehadiran siswa
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-box">
                        <select
                            id="filterBulan"
                            class="form-select form-select-sm">

                            <?php
                            $bulanAktif = date('Y-m');

                            for ($i = 0; $i < 12; $i++):
                                $bulan = date(
                                    'Y-m',
                                    strtotime("-{$i} month")
                                );

                                $label = date(
                                    'F Y',
                                    strtotime($bulan . '-01')
                                );
                            ?>

                                <option
                                    value="<?= esc($bulan) ?>"
                                    <?= $bulan === $bulanAktif ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>

                            <?php endfor; ?>

                        </select>
                    </div>
                </div>

            </div>

        </div>


        <!-- DESKTOP -->
        <div class="desktop-list ortu-table-wrap">

            <table class="table table-hover ortu-table">

                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($absensi)): ?>

                    <?php foreach ($absensi as $row): ?>

                        <tr>
                            <td>
                                <?= esc($row['tanggal'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['status'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['keterangan'] ?? '-') ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3">
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                Belum ada data absensi.
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- MOBILE -->
        <div class="mobile-list">

            <?php if (!empty($absensi)): ?>

                <?php foreach ($absensi as $row): ?>

                    <div class="mobile-item">

                        <div class="mobile-item-title">
                            <?= esc($row['tanggal'] ?? '-') ?>
                        </div>

                        <div class="mobile-meta">

                            <div>
                                <div class="mobile-meta-label">
                                    Status
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['status'] ?? '-') ?>
                                </div>
                            </div>

                            <div>
                                <div class="mobile-meta-label">
                                    Keterangan
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['keterangan'] ?? '-') ?>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    Belum ada data absensi.
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         RIWAYAT IZIN
         ===================================================== -->
    <div class="card section-card mb-3">

        <div class="card-header">

            <h6 class="section-title">
                Riwayat Pengajuan Izin
            </h6>

            <div class="section-subtitle">
                Status surat izin yang pernah diajukan
            </div>

        </div>


        <!-- DESKTOP -->
        <div class="desktop-list ortu-table-wrap">

            <table class="table table-hover ortu-table">

                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($riwayat_izin)): ?>

                    <?php foreach ($riwayat_izin as $row): ?>

                        <tr>
                            <td>
                                <?= esc($row['tanggal'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['jenis'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['status'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['keterangan'] ?? '-') ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="bi bi-envelope-open"></i>
                                Belum ada pengajuan izin.
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- MOBILE -->
        <div class="mobile-list">

            <?php if (!empty($riwayat_izin)): ?>

                <?php foreach ($riwayat_izin as $row): ?>

                    <div class="mobile-item">

                        <div class="mobile-item-title">
                            <?= esc($row['tanggal'] ?? '-') ?>
                        </div>

                        <div class="mobile-meta">

                            <div>
                                <div class="mobile-meta-label">
                                    Jenis
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['jenis'] ?? '-') ?>
                                </div>
                            </div>

                            <div>
                                <div class="mobile-meta-label">
                                    Status
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['status'] ?? '-') ?>
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="mobile-meta-label">
                                    Keterangan
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['keterangan'] ?? '-') ?>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty-state">
                    <i class="bi bi-envelope-open"></i>
                    Belum ada pengajuan izin.
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         PESAN / NOTIFIKASI
         ===================================================== -->
    <div class="card section-card mb-4">

        <div class="card-header">

            <h6 class="section-title">
                Notifikasi
            </h6>

            <div class="section-subtitle">
                Pesan terkait kehadiran siswa
            </div>

        </div>


        <!-- DESKTOP -->
        <div class="desktop-list ortu-table-wrap">

            <table class="table table-hover ortu-table">

                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Pesan</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($pesan)): ?>

                    <?php foreach ($pesan as $row): ?>

                        <tr>
                            <td>
                                <?= esc($row['created_at'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($row['message'] ?? $row['pesan'] ?? '-') ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="2">
                            <div class="empty-state">
                                <i class="bi bi-bell-slash"></i>
                                Belum ada notifikasi.
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- MOBILE -->
        <div class="mobile-list">

            <?php if (!empty($pesan)): ?>

                <?php foreach ($pesan as $row): ?>

                    <div class="mobile-item">

                        <div class="mobile-item-title">
                            <?= esc($row['created_at'] ?? '-') ?>
                        </div>

                        <div class="mobile-meta">

                            <div style="grid-column: 1 / -1;">
                                <div class="mobile-meta-label">
                                    Pesan
                                </div>

                                <div class="mobile-meta-value">
                                    <?= esc($row['message'] ?? $row['pesan'] ?? '-') ?>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty-state">
                    <i class="bi bi-bell-slash"></i>
                    Belum ada notifikasi.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<!-- =========================================================
     MODAL IZIN
     ========================================================= -->
<div
    class="modal fade"
    id="modalIzin"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-envelope-paper me-2"></i>
                    Kirim Surat Izin
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <form
                method="post"
                action="<?= site_url('ortu/izin') ?>"
                enctype="multipart/form-data">

                <?= csrf_field() ?>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Jenis Izin
                        </label>

                        <select
                            name="jenis_izin"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih jenis izin
                            </option>

                            <option value="izin">
                                Izin
                            </option>

                            <option value="sakit">
                                Sakit
                            </option>

                        </select>
                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            name="isi_pesan"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>
                    <div class="mb-3">

                        <label class="form-label">
                            Upload Surat
                        </label>

                        <input
                            type="file"
                            name="upload_surat"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">

                        <div class="form-text">
                            Format PDF, JPG, JPEG, atau PNG. Maksimal 2 MB.
                        </div>

                    </div>


                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-send me-1"></i>
                        Kirim

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<?= $this->endSection() ?>
