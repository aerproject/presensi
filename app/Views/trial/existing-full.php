<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Instalasi Tidak Dapat Dilanjutkan - PRESENSI
    </title>
</head>

<body>

<main>

    <h1>
        Instalasi Tidak Dapat Dilanjutkan
    </h1>

    <?php if (session('error')): ?>

        <p>
            <?= esc(session('error')) ?>
        </p>

    <?php endif; ?>

    <p>
        Installation UUID aplikasi ini sudah pernah
        terdaftar menggunakan lisensi Full.
    </p>

    <p>
        <strong>
            Installation UUID
        </strong>

        <br>

        <?= esc($installationUuid ?: '-') ?>
    </p>

    <p>
        Proses instalasi Trial tidak dapat dilanjutkan.
        Tidak diperlukan aktivasi atau upgrade lisensi
        untuk Installation UUID ini.
    </p>

    <p>

        <a href="<?= base_url('/') ?>">

            Kembali ke Halaman Utama

        </a>

    </p>

</main>

</body>
</html>
