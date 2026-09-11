<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php if ($message = session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= esc($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($message = session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= esc($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php
$showTrialWelcome = false;
$trialLicenseInfo = [];

if (
    isset($licenseRuntime)
    && is_array($licenseRuntime)
) {
    $trialIsActive =
        strtolower(
            (string) ($licenseRuntime['license_type'] ?? '')
        ) === 'trial'
        &&
        strtolower(
            (string) ($licenseRuntime['activate_status'] ?? '')
        ) === 'active';

    $showTrialWelcome =
        $trialIsActive
        && !session()->get('trial_welcome_shown');

    if ($showTrialWelcome) {
        session()->set(
            'trial_welcome_shown',
            true
        );
    }

    $trialLicenseInfo = [
        'expires_at' =>
            $licenseRuntime['expires_at'] ?? null,
    ];
}
?>

<div
    class="container-fluid px-4"
    id="adminDashboard"
    data-stats-url="<?= base_url('admin/get-stats') ?>"
    data-jurusan-url="<?= base_url('admin/get-jurusan-stats') ?>"
    data-kelas-url="<?= base_url('admin/get-kelas-stats') ?>"
    data-izin-url="<?= base_url('admin/get-latest-izin') ?>"
>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>
            Dashboard Admin
        </h4>
        <span class="text-muted small"><?= date('d F Y') ?></span>
    </div>

    <div class="row g-3">

        <!-- Statistik Kehadiran -->
        <div class="col-xl-3 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="card-title fw-bold text-uppercase mb-3">
                        Kehadiran Hari Ini
                    </h6>
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistik Kanan -->
        <div class="col-xl-9 col-lg-8">
            <div class="row g-3">

                <!-- Statistik Jurusan -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-uppercase mb-3">
                                Statistik Jurusan
                            </h6>

                            <div class="row g-2" id="jurusanStats">
                                <div class="text-muted small">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Tingkat -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-uppercase mb-3">
                                Siswa per Tingkat
                            </h6>

                            <canvas id="tingkatChart" height="70"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Izin Terbaru -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="card-title fw-bold text-uppercase mb-3">
                                Izin Terbaru
                            </h6>

                            <ul class="list-group" id="izinList">
                                <li class="list-group-item text-muted">
                                    Loading...
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= base_url('js/admin-dashboard.js') ?>"></script>






<?php if ($showTrialWelcome): ?>

<div class="modal fade" id="trialWelcomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Selamat Datang di Halaman Admin
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            <div class="modal-body">

                <p>
                    Aplikasi saat ini berjalan menggunakan
                    <strong>Lisensi Trial</strong>.
                </p>

                <p>
                    Masa aktif sampai:
                    <strong>
                        <?= esc($trialLicenseInfo['expires_at'] ?? '-') ?>
                    </strong>
                </p>

                <p class="text-muted small">
                    Anda dapat menggunakan seluruh fitur aplikasi
                    selama masa trial berlangsung.
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-dismiss="modal"
                >
                    Mulai Menggunakan
                </button>

                <a
                    href="<?= base_url('admin/license') ?>"
                    class="btn btn-warning"
                >
                    Upgrade Lisensi Full
                </a>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement =
        document.getElementById('trialWelcomeModal');

    if (modalElement) {

        const modal =
            new bootstrap.Modal(modalElement);

        modal.show();

    }

});
</script>

<?php endif; ?>


<?= $this->endSection() ?>