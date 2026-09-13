<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Status Upgrade Full - PRESENSI</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f3f4f6;
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            color: #111827;
        }

        .wrap {
            width: 100%;
            max-width: 760px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .05em;
        }

        h1 {
            margin: 14px 0 10px;
            font-size: 30px;
        }

        p {
            line-height: 1.6;
        }

        .success {
            margin-top: 18px;
            padding: 16px;
            border-radius: 12px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 22px;
        }

        .status-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
        }

        .label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .value {
            font-size: 17px;
            font-weight: 700;
            word-break: break-word;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-primary {
            background: #111827;
            color: #ffffff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .muted {
            margin-top: 18px;
            color: #6b7280;
            font-size: 13px;
        }

        @media (max-width: 640px) {
            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="wrap">
    <main class="card">

        <span class="badge">
            FULL LICENSE ACTIVE
        </span>

        <h1>
            Upgrade Lisensi Berhasil
        </h1>

        <p>
            Lisensi Trial pada server ini telah berhasil
            di-upgrade menjadi Lisensi Full.
        </p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php else: ?>
            <div class="success">
                Lisensi Full sudah aktif dan tervalidasi pada server ini.
            </div>
        <?php endif; ?>

        <div class="status-grid">

            <div class="status-item">
                <div class="label">
                    Jenis Lisensi
                </div>

                <div class="value">
                    <?= esc(strtoupper($licenseType ?: '-')) ?>
                </div>
            </div>

            <div class="status-item">
                <div class="label">
                    Status Aktivasi
                </div>

                <div class="value">
                    <?= esc(strtoupper($activateStatus ?: '-')) ?>
                </div>
            </div>

            <div class="status-item">
                <div class="label">
                    Status Validasi
                </div>

                <div class="value">
                    <?= esc(strtoupper($validateStatus ?: '-')) ?>
                </div>
            </div>

            <div class="status-item">
                <div class="label">
                    Berlaku Sampai
                </div>

                <div class="value">
                    <?= esc(
                        $expiresAt
                            ? date(
                                'd F Y H:i',
                                strtotime($expiresAt)
                            )
                            : '-'
                    ) ?>
                </div>
            </div>

            <div class="status-item">
                <div class="label">
                    Installation UUID
                </div>

                <div class="value">
                    <?= esc($installationUuid ?: '-') ?>
                </div>
            </div>

        </div>

        <div class="actions">

            <a
                class="btn btn-primary"
                href="<?= base_url('/admin/dashboard') ?>"
            >
                Masuk ke Dashboard
            </a>

            <a
                class="btn btn-secondary"
                href="<?= base_url('/admin/license') ?>"
            >
                Lihat Status Lisensi
            </a>

        </div>

        <div class="muted">
            Server Identity tetap dipertahankan selama proses upgrade.
        </div>

    </main>
</div>
</body>
</html>
