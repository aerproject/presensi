<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proses Upgrade Full - PRESENSI</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f3f4f6;
            font-family: system-ui, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
            color: #111827;
        }

        .wrap {
            width: 100%;
            max-width: 760px;
        }

        .card {
            background: #fff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
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

        .info {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            padding: 13px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e5e7eb;
            font-weight: 700;
            flex: 0 0 30px;
        }

        .done .icon {
            background: #dcfce7;
            color: #166534;
        }

        .active .icon {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .label {
            font-weight: 700;
        }

        .muted {
            margin-top: 18px;
            color: #6b7280;
            font-size: 13px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 16px;
            border: 0;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-primary {
            background: #111827;
            color: #fff;
        }
    </style>
</head>

<body>
<div class="wrap">
    <main class="card">

        <span class="badge">
            FULL LICENSE UPGRADE
        </span>

        <h1>Proses Upgrade Lisensi</h1>

        <p>
            Sistem sedang menampilkan status proses upgrade
            berdasarkan runtime lisensi server.
        </p>

        <div class="info">
            Installation UUID:
            <strong><?= esc($installationUuid ?: '-') ?></strong>
        </div>

        <div class="step done">
            <div class="icon">✓</div>
            <div>
                <div class="label">Credential Full</div>
                <div class="muted">
                    Credential operasional sudah tersedia.
                </div>
            </div>
        </div>

        <div class="step <?= $licenseType === 'full' ? 'done' : 'active' ?>">
            <div class="icon">
                <?= $licenseType === 'full' ? '✓' : '2' ?>
            </div>
            <div>
                <div class="label">Lisensi Full</div>
                <div class="muted">
                    Status:
                    <?= esc(strtoupper($licenseType ?: 'PROCESSING')) ?>
                </div>
            </div>
        </div>

        <div class="step <?= $activateStatus === 'active' ? 'done' : 'active' ?>">
            <div class="icon">
                <?= $activateStatus === 'active' ? '✓' : '3' ?>
            </div>
            <div>
                <div class="label">Aktivasi Full</div>
                <div class="muted">
                    Status:
                    <?= esc(strtoupper($activateStatus ?: 'PENDING')) ?>
                </div>
            </div>
        </div>

        <div class="step <?= $validateStatus === 'valid' ? 'done' : 'active' ?>">
            <div class="icon">
                <?= $validateStatus === 'valid' ? '✓' : '4' ?>
            </div>
            <div>
                <div class="label">Validasi Full</div>
                <div class="muted">
                    Status:
                    <?= esc(strtoupper($validateStatus ?: 'PENDING')) ?>
                </div>
            </div>
        </div>

        <div class="muted">
            Halaman ini hanya menampilkan status proses.
            Proses aktivasi dilakukan oleh sistem.
        </div>

        <?php if (
            $licenseType === 'full'
            && $activateStatus !== 'active'
        ): ?>

            <div class="actions">

                <form
                    method="post"
                    action="<?= base_url('/trial/upgrade/activate') ?>"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Aktivasi Lisensi Full
                    </button>
                </form>

            </div>

        <?php elseif (
            $licenseType === 'full'
            && $activateStatus === 'active'
            && $validateStatus === 'valid'
        ): ?>

            <div class="actions">

                <a
                    class="btn btn-primary"
                    href="<?= base_url('/trial/upgrade/status') ?>"
                >
                    Lihat Status Upgrade
                </a>

            </div>

        <?php endif; ?>

    </main>
</div>
</body>
</html>
