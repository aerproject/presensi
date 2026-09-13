<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upgrade ke Full - PRESENSI</title>

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
            background: #fff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
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

        .info,
        .warning {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 12px;
            line-height: 1.6;
        }

        .info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
        }

        .field {
            margin-top: 18px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-primary {
            background: #111827;
            color: #fff;
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
    </style>
</head>

<body>
<div class="wrap">
    <main class="card">

        <span class="badge">
            FULL LICENSE UPGRADE
        </span>

        <h1>
            Upgrade Lisensi ke Full
        </h1>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="warning">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <div class="info">
            Installation UUID yang sudah terdaftar akan dipertahankan.
            Upgrade ini tidak membuat Server Identity baru.
        </div>

        <form
            method="post"
            action="<?= base_url('/trial/upgrade/bootstrap') ?>"
        >
            <?= csrf_field() ?>

            <div class="field">
                <label for="license_key">
                    Full License Key
                </label>

                <input
                    type="text"
                    id="license_key"
                    name="license_key"
                    value="<?= esc(old('license_key')) ?>"
                    required
                    autocomplete="off"
                    placeholder="Masukkan Full License Key"
                >
            </div>

            <div class="field">
                <label for="api_key">
                    Full API Key
                </label>

                <input
                    type="text"
                    id="api_key"
                    name="api_key"
                    value="<?= esc(old('api_key')) ?>"
                    required
                    autocomplete="off"
                    placeholder="Masukkan Full API Key"
                >
            </div>

            <div class="actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Lanjutkan Upgrade
                </button>

                <a
                    class="btn btn-secondary"
                    href="<?= base_url('/trial/install') ?>"
                >
                    Kembali
                </a>
            </div>
        </form>

        <div class="muted">
            API Secret tidak perlu dimasukkan. Sistem memperolehnya
            dari License Server dan menyimpannya secara terenkripsi.
        </div>

    </main>
</div>
</body>
</html>
