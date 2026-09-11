<?php
use App\Models\AplikasiModel;

$aplikasiModel = new AplikasiModel();
$pengaturan = $aplikasiModel->first();

$namaAplikasi = $pengaturan['nama_aplikasi'] ?? 'Absensi Digital';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title><?= esc($title ?? 'Absensi Digital :: SMK 2 Mei Bandar Lampung') ?></title>
    
    <?= $this->include('_partials/head') ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/beranda.css') ?>">
    
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold" href="<?= base_url('beranda') ?>">
            <i class="bi bi-shield-check me-1"></i> <?= esc(strtoupper($namaAplikasi)) ?>
            
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('beranda/izin') ?>">
                        <i class="bi bi-pencil-square"></i> Izin
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('beranda/sakit') ?>">
                        <i class="bi bi-heart-pulse"></i> Sakit
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('beranda/alpha') ?>">
                        <i class="bi bi-x-circle"></i> Alpha
                    </a>
                </li>

                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="nav-link btn-login" href="<?= base_url('auth/login') ?>">
                        Login
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?= $this->renderSection('content') ?>
</main>

<footer class="footer fixed-bottom text-center">
    <div class="container">
        Absensi Digital |
        <a href="<?= base_url('/absensi') ?>" class="text-decoration-none text-primary">
            <i class="bi bi-qr-code"></i>
        </a>
        | AerProject © <?= date('Y') ?>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?= $this->renderSection('scripts') ?>

</body>
</html>