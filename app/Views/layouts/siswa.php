<?php
$dbPengaturan = \Config\Database::connect();

$pengaturanAplikasi = $dbPengaturan
    ->table('pengaturan')
    ->select('nama_aplikasi')
    ->where('aktif', 1)
    ->orderBy('id', 'DESC')
    ->get()
    ->getRowArray();

$namaAplikasi = trim($pengaturanAplikasi['nama_aplikasi'] ?? '');

if ($namaAplikasi === '') {
    $namaAplikasi = 'Presensi Digital';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,
                   initial-scale=1,
                   maximum-scale=1,
                   user-scalable=no">

    <meta name="theme-color" content="#0d6efd">

    <title>
        <?= esc($title ?? 'Dashboard Siswa') ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>
        /* =====================================================
           LAYOUT SISWA
           MOBILE FIRST
           ===================================================== */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            background: #f4f7fb;
            overflow-x: hidden;
        }

        body {
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            color: #172033;
            padding-top: 64px;
            padding-bottom: 58px;
        }

        /* =========================
           NAVBAR
           ========================= */

        .siswa-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;

            height: 64px;

            background: linear-gradient(
                135deg,
                #0d6efd,
                #1769e0
            );

            box-shadow:
                0 3px 12px rgba(0, 0, 0, .12);
        }

        .siswa-navbar-inner {
            width: 100%;
            height: 64px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding:
                0 16px;
        }

        .siswa-brand {
            display: flex;
            align-items: center;
            gap: 9px;

            color: #fff;
            text-decoration: none;

            font-size: 1.05rem;
            font-weight: 700;

            white-space: nowrap;
        }

        .siswa-brand-icon {
            width: 34px;
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: rgba(255,255,255,.18);
            color: #fff;

            font-size: 1.05rem;
        }

        .siswa-menu-btn {
            width: 40px;
            height: 40px;

            border: 0;
            border-radius: 10px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            background: rgba(255,255,255,.12);
            color: #fff;

            font-size: 1.35rem;
        }

        .siswa-menu-btn:active {
            background: rgba(255,255,255,.25);
        }

        /* =========================
           MOBILE MENU
           ========================= */

        .siswa-menu {
            position: fixed;

            top: 64px;
            right: 10px;

            width: min(230px, calc(100vw - 20px));

            z-index: 1049;

            display: none;

            padding: 8px;

            border-radius: 14px;

            background: #fff;

            box-shadow:
                0 10px 30px rgba(0,0,0,.16);

            border: 1px solid #e9edf3;
        }

        .siswa-menu.show {
            display: block;
        }

        .siswa-menu a {
            display: flex;
            align-items: center;
            gap: 10px;

            padding: 12px 13px;

            border-radius: 10px;

            color: #172033;
            text-decoration: none;

            font-size: .9rem;
            font-weight: 500;
        }

        .siswa-menu a:hover,
        .siswa-menu a:active {
            background: #f1f5ff;
            color: #0d6efd;
        }

        /* =========================
           MAIN
           ========================= */

        .siswa-main {
            width: 100%;
            min-height: calc(100vh - 122px);

            padding:
                14px 14px 20px;
        }

        .siswa-container {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
        }

        /* =========================
           FOOTER
           ========================= */

        .siswa-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;

            z-index: 1040;

            min-height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 8px 12px;

            background: rgba(255,255,255,.97);

            border-top: 1px solid #e5e9ef;

            box-shadow:
                0 -3px 12px rgba(0,0,0,.05);

            font-size: .75rem;
            color: #6c757d;

            text-align: center;
        }

        /* =========================
           DESKTOP
           ========================= */

        @media (min-width: 768px) {

            body {
                padding-top: 68px;
                padding-bottom: 62px;
            }

            .siswa-navbar,
            .siswa-navbar-inner {
                height: 68px;
            }

            .siswa-navbar-inner {
                padding-left: 24px;
                padding-right: 24px;
            }

            .siswa-main {
                padding:
                    22px 20px 28px;
            }

            .siswa-menu {
                top: 68px;
                right: 20px;
            }
        }

        /* =========================
           HP SANGAT KECIL
           ========================= */

        @media (max-width: 360px) {

            .siswa-navbar-inner {
                padding-left: 12px;
                padding-right: 12px;
            }

            .siswa-brand {
                font-size: .95rem;
            }

            .siswa-main {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>
</head>

<body>

<!-- =====================================================
     NAVBAR SISWA
     ===================================================== -->

<header class="siswa-navbar">

    <div class="siswa-navbar-inner">

        <a
            href="<?= site_url('siswa/dashboard') ?>"
            class="siswa-brand">

            <span class="siswa-brand-icon">
                <i class="bi bi-shield-check"></i>
            </span>

            <span><?= esc($namaAplikasi) ?></span>

        </a>

        <button
            type="button"
            class="siswa-menu-btn"
            id="siswaMenuButton"
            aria-label="Buka menu">

            <i class="bi bi-list"></i>

        </button>

    </div>

</header>


<!-- =====================================================
     MENU SISWA
     ===================================================== -->

<div class="siswa-menu" id="siswaMenu">

    <a href="<?= site_url('siswa/dashboard') ?>">
        <i class="bi bi-grid-1x2-fill"></i>
        Dashboard
    </a>

    <a href="<?= site_url('siswa/riwayat-izin') ?>">
        <i class="bi bi-clock-history"></i>
        Izin
    </a>

    <a href="<?= site_url('siswa/riwayat-kehadiran') ?>">
        <i class="bi bi-chat-dots"></i>
        Kehadiran
    </a>

    <a href="<?= site_url('auth/logout') ?>">
        <i class="bi bi-box-arrow-right"></i>
        Keluar
    </a>

</div>


<!-- =====================================================
     CONTENT
     ===================================================== -->

<main class="siswa-main">

    <div class="siswa-container">

        <?= $this->renderSection('content') ?>

    </div>

</main>


<!-- =====================================================
     FOOTER ORANG TUA
     ===================================================== -->

<footer class="siswa-footer">

    <span>
        Absensi Digital |
        <i class="bi bi-shield-check"></i>
        AerProject © <?= date('Y') ?>
    </span>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>

<script>
(function () {

    const button = document.getElementById('siswaMenuButton');
    const menu   = document.getElementById('siswaMenu');

    if (!button || !menu) {
        return;
    }

    button.addEventListener('click', function (event) {

        event.stopPropagation();

        menu.classList.toggle('show');

        const icon = button.querySelector('i');

        if (menu.classList.contains('show')) {
            icon.className = 'bi bi-x-lg';
        } else {
            icon.className = 'bi bi-list';
        }

    });

    document.addEventListener('click', function (event) {

        if (!menu.contains(event.target) &&
            !button.contains(event.target)) {

            menu.classList.remove('show');

            const icon = button.querySelector('i');

            if (icon) {
                icon.className = 'bi bi-list';
            }
        }

    });

})();
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
