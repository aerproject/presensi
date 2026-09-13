<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Aplikasi</title>

    <style>
        body {
            margin: 0;
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 680px;
            background: #ffffff;
            border-radius: 16px;
            padding: 32px;
            box-shadow:
                0 12px 40px rgba(0, 0, 0, .08);
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }

        p {
            line-height: 1.65;
        }

        .notice {
            margin-top: 20px;
            padding: 18px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .info {
            margin-top: 20px;
            padding: 18px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 10px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-disabled {
            background: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
        }

        .flash {
            margin-bottom: 18px;
            padding: 14px;
            border-radius: 10px;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .flash-success {
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>
<div class="wrap">
    <main class="card">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash flash-error">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="flash flash-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (!$allowed): ?>

            <span class="badge badge-danger">
                INSTALLATION BLOCKED
            </span>

            <h1>Trial Lisensi Tidak Tersedia</h1>

            <div class="notice">
                <?= esc(
                    $notice
                    ?? 'Instalasi tidak dapat dilakukan pada server ini.'
                ) ?>
            </div>

            <p>
                Server ini sudah memiliki identitas instalasi
                yang tersimpan. Trial Lisensi tidak dapat
                digunakan untuk membuat instalasi baru
                pada server yang sama.
            </p>

            <div class="actions">
                <a
                    class="btn btn-primary"
                    href="<?= base_url('/trial/upgrade') ?>"
                >
                    Upgrade ke Full
                </a>

                <form
                    method="post"
                    action="<?= base_url('/trial/install/stop') ?>"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Hentikan Instalasi
                    </button>
                </form>

                <a
                    class="btn btn-secondary"
                    href="<?= base_url('/') ?>"
                >
                    Kembali
                </a>
            </div>

        <?php else: ?>

            <span class="badge badge-success">
                TRIAL READY
            </span>

            <h1>Instalasi Aplikasi</h1>

            <div class="info">
                Aplikasi belum memiliki Lisensi Trial aktif.
                Instalasi Trial dapat dilanjutkan menggunakan
                Installation UUID aplikasi ini.
            </div>

            <div class="info">
                <strong>Installation UUID</strong>
                <br>
                <?= esc($installationUuid ?: '-') ?>
            </div>

            <p>
                Klik tombol Mulai Instalasi Trial untuk
                mengirim Installation UUID ke License Server.
                Trial License dan credential akan diproses
                secara otomatis oleh sistem.
            </p>

            <form
                method="post"
                action="<?= base_url('/trial/install/bootstrap') ?>"
            >
                <?= csrf_field() ?>

                <div class="actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Mulai Instalasi Trial
                    </button>

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/') ?>"
                    >
                        Batal
                    </a>

                </div>
            </form>

        <?php endif; ?>

    </main>
</div>
</body>
</html>
