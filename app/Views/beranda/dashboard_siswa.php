<?= $this->extend('layouts/siswa') ?>

<?= $this->section('content') ?>

<?php
$successMessage = session()->getFlashdata('success');
$errorMessage   = session()->getFlashdata('error');
$warningMessage = session()->getFlashdata('warning');
$infoMessage    = session()->getFlashdata('info');

$status = strtolower((string) ($absenHariIni['status'] ?? 'belum absen'));

$statusClass = 'warning';
$statusIcon  = 'bi-clock-history';

if ($status === 'hadir') {
    $statusClass = 'success';
    $statusIcon  = 'bi-check-circle-fill';
} elseif ($status === 'izin') {
    $statusClass = 'info';
    $statusIcon  = 'bi-envelope-paper-fill';
} elseif ($status === 'sakit') {
    $statusClass = 'warning';
    $statusIcon  = 'bi-heart-pulse-fill';
} elseif ($status === 'alpha') {
    $statusClass = 'danger';
    $statusIcon  = 'bi-x-circle-fill';
}

$jumlahHadir = 0;
$jumlahIzin  = 0;
$jumlahSakit = 0;
$jumlahAlpha = 0;

foreach (($statistik ?? []) as $stat) {
    $statStatus = strtolower((string) ($stat['status'] ?? ''));
    $jumlah    = (int) ($stat['jumlah'] ?? 0);

    if ($statStatus === 'hadir') {
        $jumlahHadir = $jumlah;
    } elseif ($statStatus === 'izin') {
        $jumlahIzin = $jumlah;
    } elseif ($statStatus === 'sakit') {
        $jumlahSakit = $jumlah;
    } elseif ($statStatus === 'alpha') {
        $jumlahAlpha = $jumlah;
    }
}
?>

<style>
.siswa-dashboard {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 12px;
}

.siswa-dashboard * {
    box-sizing: border-box;
}

.siswa-hero {
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #0d6efd, #4f8dfd);
    color: #fff;
    box-shadow: 0 8px 24px rgba(13, 110, 253, .18);
}

.siswa-hero h4 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.siswa-hero p {
    margin: 0;
    opacity: .9;
    font-size: .9rem;
}

.siswa-card {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 5px 18px rgba(0,0,0,.07);
    margin-bottom: 16px;
    background: #fff;
}

.siswa-card .card-body {
    padding: 18px;
}

.identity-label {
    color: #6c757d;
    font-size: .76rem;
    margin-bottom: 3px;
}

.identity-value {
    font-weight: 600;
    word-break: break-word;
}

.identity-item {
    padding: 5px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 16px;
}

.stat-card {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,.06);
    background: #fff;
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
    background: #fff;
    margin-bottom: 16px;
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

.today-status {
    display: flex;
    align-items: center;
    gap: 12px;
}

.today-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.today-main {
    min-width: 0;
}

.today-label {
    color: #6c757d;
    font-size: .76rem;
    margin-bottom: 3px;
}

.today-status-text {
    font-weight: 700;
    font-size: 1.05rem;
}

.today-time {
    margin-left: auto;
    text-align: right;
}

.today-time-label {
    color: #6c757d;
    font-size: .72rem;
}

.today-time-value {
    font-weight: 700;
    font-size: .95rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 600;
}

.table-wrap {
    overflow-x: auto;
}

.siswa-table {
    width: 100%;
    margin: 0;
}

.siswa-table th {
    color: #6c757d;
    font-size: .73rem;
    font-weight: 600;
    white-space: nowrap;
}

.siswa-table td {
    font-size: .82rem;
    white-space: nowrap;
}

.empty-state {
    text-align: center;
    color: #6c757d;
    padding: 28px 15px;
    font-size: .85rem;
}

@media (min-width: 768px) {
    .siswa-dashboard {
        padding: 16px;
    }

    .stats-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .identity-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }
}

@media (max-width: 575.98px) {
    .today-status {
        align-items: flex-start;
    }

    .today-time {
        margin-left: auto;
    }

    .siswa-table th,
    .siswa-table td {
        padding: 10px 8px;
    }
}
</style>

<div class="siswa-dashboard">

    <?php if ($successMessage || $errorMessage || $warningMessage || $infoMessage): ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= esc($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($warningMessage): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= esc($warningMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($infoMessage): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm">
                <i class="bi bi-info-circle-fill me-2"></i>
                <?= esc($infoMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- HERO -->
    <div class="siswa-hero">
        <h4>
            Halo, <?= esc($student['nama_siswa'] ?? 'Siswa') ?>
        </h4>
        <p>
            Selamat datang di dashboard siswa. Pantau kehadiranmu dengan mudah.
        </p>
    </div>

    <!-- IDENTITAS SISWA -->
    <div class="siswa-card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-vcard fs-4 text-primary me-2"></i>
                <div>
                    <div class="section-title">Data Siswa</div>
                    <div class="section-subtitle">
                        Informasi siswa yang sedang login
                    </div>
                </div>
            </div>

            <div class="identity-grid">
                <div class="identity-item">
                    <div class="identity-label">Nama Siswa</div>
                    <div class="identity-value">
                        <?= esc($student['nama_siswa'] ?? '-') ?>
                    </div>
                </div>

                <div class="identity-item">
                    <div class="identity-label">Kelas</div>
                    <div class="identity-value">
                        <?= esc($student['nama_kelas'] ?? '-') ?>
                    </div>
                </div>

                <div class="identity-item">
                    <div class="identity-label">Jurusan</div>
                    <div class="identity-value">
                        <?= esc($student['nama_jurusan'] ?? '-') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="stats-grid">

        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-number text-success">
                    <?= $jumlahHadir ?>
                </div>
                <div class="stat-label">Hadir</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
                <div class="stat-number text-info">
                    <?= $jumlahIzin ?>
                </div>
                <div class="stat-label">Izin</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-number text-warning">
                    <?= $jumlahSakit ?>
                </div>
                <div class="stat-label">Sakit</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="stat-number text-danger">
                    <?= $jumlahAlpha ?>
                </div>
                <div class="stat-label">Alpha</div>
            </div>
        </div>

    </div>

    <!-- ABSENSI HARI INI -->
    <div class="section-card">
        <div class="card-header">
            <div class="section-title">Absensi Hari Ini</div>
            <div class="section-subtitle">
                Status kehadiran hari ini
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="today-status">

                <div class="today-icon bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?>">
                    <i class="bi <?= $statusIcon ?>"></i>
                </div>

                <div class="today-main">
                    <div class="today-label">Status Kehadiran</div>

                    <div class="today-status-text">
                        <?= esc(ucfirst($status)) ?>
                    </div>

                    <?php if (!empty($absenHariIni['keterangan_masuk'])): ?>
                        <span class="status-badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> mt-1">
                            <?= esc($absenHariIni['keterangan_masuk']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="today-time">
                    <div class="today-time-label">Jam Masuk</div>
                    <div class="today-time-value">
                        <?= esc($absenHariIni['jam_masuk'] ?? '--:--') ?>
                    </div>

                    <div class="today-time-label mt-2">Jam Pulang</div>
                    <div class="today-time-value">
                        <?= esc($absenHariIni['jam_pulang'] ?? '--:--') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- RIWAYAT -->
    <div class="section-card">
        <div class="card-header">
            <div class="section-title">Riwayat Kehadiran</div>
            <div class="section-subtitle">
                10 data absensi terakhir
            </div>
        </div>

        <div class="table-wrap">
            <table class="table table-hover align-middle siswa-table mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($riwayat)): ?>

                        <?php foreach ($riwayat as $r): ?>

                            <?php
                            $rStatus = strtolower((string) ($r['status'] ?? ''));

                            $rClass = 'warning';

                            if ($rStatus === 'hadir') {
                                $rClass = 'success';
                            } elseif ($rStatus === 'izin') {
                                $rClass = 'info';
                            } elseif ($rStatus === 'alpha') {
                                $rClass = 'danger';
                            }
                            ?>

                            <tr>
                                <td>
                                    <?= date('d M Y', strtotime($r['tanggal'])) ?>
                                </td>

                                <td>
                                    <span class="status-badge bg-<?= $rClass ?> bg-opacity-10 text-<?= $rClass ?>">
                                        <?= esc(ucfirst($rStatus)) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= esc($r['jam_masuk'] ?? '--:--') ?>
                                </td>

                                <td>
                                    <?= esc($r['jam_pulang'] ?? '--:--') ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                    Belum ada riwayat absensi.
                                </div>
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
