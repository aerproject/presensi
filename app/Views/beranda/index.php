<?= $this->extend('layouts/main') ?>

<?php
$aplikasiModel = new \App\Models\AplikasiModel();
$pengaturan = $aplikasiModel->first();

$namaAplikasi = $pengaturan['nama_aplikasi'] ?? 'PRESENSI DIGITAL';
$namaSekolah  = $pengaturan['nama_sekolah'] ?? 'SMK TEKNOLOGI NUSANTARA';
?>

<style>
.dashboard-school-name {
    margin-top: 4px !important;
    margin-bottom: 8px !important;
    font-size: 24px !important;
    line-height: 1.2 !important;
    font-weight: 800 !important;
                         color: #0b1f3a !important;
    text-transform: uppercase !important;
    color: #555 !important;
    letter-spacing: 0.5px !important;
}

@media (max-width: 576px) {
    .dashboard-school-name {
        font-size: 28px !important;
    }
}
</style>


<?= $this->section('content') ?>
<div class="dashboard-wrapper container-fluid px-2 px-md-4">
    
    <div class="text-center my-4">
        <h4 class="fw-bold text-primary d-flex justify-content-center align-items-center gap-2">
            <span
                class="d-inline-flex justify-content-center align-items-center"
                style="
                    width: 44px;
                    height: 44px;
                    background: #ffffff;
                    border: 1px solid #cfd6df;
                    border-radius: 8px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.12);
                    flex-shrink: 0;
                "
            >
                <svg
                    width="36"
                    height="36"
                    viewBox="0 0 28 28"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <!-- QR Code berwarna -->
                    <rect x="1" y="1" width="9" height="9" rx="1.5" fill="#0d6efd"/>
                    <rect x="3.5" y="3.5" width="4" height="4" rx=".5" fill="#ffffff"/>

                    <rect x="18" y="1" width="9" height="9" rx="1.5" fill="#198754"/>
                    <rect x="20.5" y="3.5" width="4" height="4" rx=".5" fill="#ffffff"/>

                    <rect x="1" y="18" width="9" height="9" rx="1.5" fill="#fd7e14"/>
                    <rect x="3.5" y="20.5" width="4" height="4" rx=".5" fill="#ffffff"/>

                    <rect x="13" y="13" width="4" height="4" rx=".5" fill="#0d6efd"/>
                    <rect x="19" y="13" width="3" height="3" rx=".5" fill="#dc3545"/>
                    <rect x="24" y="13" width="3" height="5" rx=".5" fill="#198754"/>
                    <rect x="13" y="19" width="3" height="3" rx=".5" fill="#6f42c1"/>
                    <rect x="18" y="19" width="5" height="3" rx=".5" fill="#0d6efd"/>
                    <rect x="24" y="21" width="3" height="6" rx=".5" fill="#dc3545"/>
                    <rect x="13" y="24" width="5" height="3" rx=".5" fill="#198754"/>
                    <rect x="20" y="24" width="3" height="3" rx=".5" fill="#fd7e14"/>
                </svg>
            </span>

            <span>DASHBOARD ABSENSI</span>
        </h4>
       <?php if (!empty($namaSekolah)): ?>
            <div class="dashboard-school-name"
                 style="font-size: 24px !important;
                        line-height: 1.2 !important;
                        font-weight: 800 !important;
                         color: #0b1f3a !important;
                        text-transform: uppercase !important;
                        margin-top: 6px !important;
                        margin-bottom: 8px !important;">
                <?= esc(strtoupper($namaSekolah)) ?>
            </div>
        <?php endif; ?>
        <p class="text-muted" style="font-size: 18px !important; margin-bottom: 0;">Data kehadiran siswa real-time</p>
    </div>

<div class="row g-2 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-dark text-white rounded-2">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-5">
                    <i class="bi bi-people-fill me-2"></i> Total Siswa
                </span>
                <h3 class="mb-0 fw-bold" data-stat="totalSiswa">0</h3>
            </div>
        </div>
    </div>

<div class="col-6">
    <div class="card shadow-sm border-0 bg-success text-white rounded-2 h-100">
        <div class="card-body p-3 position-relative">
            <!-- Ikon besar samar di sisi kiri, bergeser sedikit -->
            <i class="bi bi-person-check-fill position-absolute top-50 start-0 translate-middle-y text-white-50 ms-4" 
   class="dashboard-stat-icon dashboard-stat-icon-light"></i>

            
            <!-- Konten kanan -->
            <div class="d-flex flex-column align-items-end pe-2">
                <span class="fw-bold fs-6">Hadir</span>
                <h3 class="fw-bold mt-1 mb-0" data-stat="hadir">0</h3>
            </div>
        </div>
    </div>
</div>


<div class="col-6">
    <div class="card shadow-sm border-0 bg-warning text-dark rounded-2 h-100">
        <div class="card-body p-3 position-relative">
            <!-- Ikon besar samar di sisi kiri -->
            <i class="bi bi-pencil-square position-absolute top-50 start-0 translate-middle-y text-dark opacity-50 ms-4" 
               class="dashboard-stat-icon dashboard-stat-icon-dark"></i>

            <!-- Konten kanan -->
            <div class="d-flex flex-column align-items-end pe-2">
                <span class="fw-bold fs-6">Izin</span>
                <h3 class="fw-bold mt-1 mb-0" data-stat="izin">0</h3>
            </div>
        </div>
    </div>
</div>

<div class="col-6">
    <div class="card shadow-sm border-0 bg-info text-white rounded-2 h-100">
        <div class="card-body p-3 position-relative">
            <!-- Ikon besar samar di sisi kiri -->
            <i class="bi bi-heart-pulse-fill position-absolute top-50 start-0 translate-middle-y text-white-50 ms-4" 
               class="dashboard-stat-icon dashboard-stat-icon-light"></i>

            <!-- Konten kanan -->
            <div class="d-flex flex-column align-items-end pe-2">
                <span class="fw-bold fs-6">Sakit</span>
                <h3 class="fw-bold mt-1 mb-0" data-stat="sakit">0</h3>
            </div>
        </div>
    </div>
</div>

<div class="col-6">
    <div class="card shadow-sm border-0 bg-danger text-white rounded-2 h-100">
        <div class="card-body p-3 position-relative">
            <!-- Ikon besar samar di sisi kiri -->
            <i class="bi bi-x-circle-fill position-absolute top-50 start-0 translate-middle-y text-white-50 ms-4" 
               class="dashboard-stat-icon dashboard-stat-icon-light"></i>

            <!-- Konten kanan -->
            <div class="d-flex flex-column align-items-end pe-2">
                <span class="fw-bold fs-6">Alpha</span>
                <h3 class="fw-bold mt-1 mb-0" data-stat="alpha">0</h3>
            </div>
        </div>
    </div>
</div>


</div>


    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-2">
                <div class="card-body">
                    <h6 class="text-center fw-bold text-secondary">Persentase</h6>
                    <div class="dashboard-chart-container">
                        <canvas id="chartPersentase"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0 rounded-2">
                <div class="card-body">
                    <h6 class="text-center fw-bold text-secondary">Statistik Ketidakhadiran</h6>
                    <div class="dashboard-chart-container">
                        <canvas id="chartTidakHadir"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/beranda.js') ?>"></script>
<?= $this->endSection() ?>


<script id="public-dashboard-app-name">
document.addEventListener('DOMContentLoaded', function () {
    const namaAplikasi = <?= json_encode($namaAplikasi ?? 'Absensi Digital') ?>;

    const navbarBrand = document.querySelector(
        '.custom-navbar .navbar-brand'
    );

    if (!navbarBrand) {
        return;
    }

    /*
     * Pertahankan icon shield dari layout.
     * Hanya teks nama aplikasi yang diganti.
     */
    const icon = navbarBrand.querySelector('i');

    navbarBrand.innerHTML = '';

    if (icon) {
        navbarBrand.appendChild(icon);
    }

    navbarBrand.appendChild(
        document.createTextNode(' ' + namaAplikasi)
    );
});
</script>

