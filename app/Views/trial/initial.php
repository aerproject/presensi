<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Konfigurasi Awal</title>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e3a5f;
            --success: #16a34a;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --info-bg: #eff6ff;
            --info-border: #bfdbfe;
            --info-text: #1e40af;
            --muted: #6b7280;
            --text: #111827;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f3f4f6;
            color: var(--text);
        }

        .wrap {
            min-height: 100vh;
            padding: 36px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .wizard {
            width: 100%;
            max-width: 1040px;
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 55px rgba(15, 23, 42, .10);
        }

        .wizard-header {
            padding: 30px 34px 24px;
            border-bottom: 1px solid var(--border);
        }

        .wizard-title {
            margin: 0 0 28px;
            text-align: center;
            font-size: 30px;
            font-weight: 500;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0;
        }

        .step {
            position: relative;
            text-align: center;
        }

        .step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 21px;
            left: 50%;
            width: 100%;
            height: 3px;
            background: #d1d5db;
            z-index: 0;
        }

        .step.active:not(:last-child)::after {
            background: #d1d5db;
        }

        .step-bubble {
            position: relative;
            z-index: 1;
            width: 44px;
            height: 44px;
            margin: 0 auto 10px;
            border-radius: 999px;
            border: 3px solid #d1d5db;
            background: #fff;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
        }

        .step.active .step-bubble {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 6px rgba(37, 99, 235, .10);
        }

        .step-label {
            font-size: 14px;
            line-height: 1.35;
            color: var(--muted);
            white-space: nowrap;
        }

        .step.active .step-label {
            color: var(--text);
            font-weight: 700;
        }

        .wizard-body {
            padding: 38px 34px 34px;
        }

        .intro {
            text-align: center;
            color: #374151;
            margin-bottom: 28px;
        }

        .badge {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .badge-success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        h1 {
            margin: 0 0 12px;
            text-align: center;
            font-size: 32px;
        }

        .check-list {
            border: 1px solid var(--border);
            border-radius: 15px;
            overflow: hidden;
            margin-top: 24px;
        }

        .check-row {
            display: grid;
            grid-template-columns: 52px 1fr auto;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
        }

        .check-row:last-child {
            border-bottom: 0;
        }

        .check-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .check-icon.ok {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .check-icon.fail {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .check-name {
            font-weight: 800;
        }

        .check-value {
            color: var(--muted);
            font-size: 13px;
            margin-top: 3px;
            word-break: break-word;
        }

        .check-status {
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-ok {
            color: var(--success-text);
        }

        .status-fail {
            color: var(--danger-text);
        }

        .panel {
            margin-top: 22px;
            padding: 18px 20px;
            border-radius: 14px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            color: var(--info-text);
            line-height: 1.65;
        }

        .panel-danger {
            background: var(--danger-bg);
            border-color: #fecaca;
            color: var(--danger-text);
        }

        .actions {
            margin-top: 28px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 11px 20px;
            border-radius: 10px;
            border: 1px solid transparent;
            cursor: pointer;
            font-weight: 800;
            font-size: 14px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: var(--text);
            border-color: #d1d5db;
        }

        .flash {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 10px;
        }

        .flash-error {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .flash-success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        @media (max-width: 760px) {
            .wizard-header,
            .wizard-body {
                padding-left: 18px;
                padding-right: 18px;
            }

            .step-label {
                white-space: normal;
                font-size: 12px;
            }

            .step-bubble {
                width: 38px;
                height: 38px;
                font-size: 15px;
            }

            .step:not(:last-child)::after {
                top: 18px;
            }

            .check-row {
                grid-template-columns: 42px 1fr;
            }

            .check-status {
                grid-column: 2;
            }
        }

        /* Stage 1 — compact Base URL layout */
        .base-url-form {
            margin-top: 16px !important;
        }

        .base-url-form .actions {
            margin-top: 16px;
        }

        .base-url-form:has(input[readonly]) .actions:empty {
            display: none;
        }

        .base-url-confirmation {
            margin-top: 16px;
        }

        .base-url-navigation {
            margin-top: 16px;
        }
    </style>
</head>

<body>
<div class="wrap">

    <main class="wizard">

        <header class="wizard-header">

            <h2 class="wizard-title">
                Langkah Instalasi: Tahap 1
            </h2>

            <div class="steps">

                <div class="step active">
                    <div class="step-bubble">1</div>
                    <div class="step-label">
                        Konfigurasi Awal
                    </div>
                </div>

                <div class="step">
                    <div class="step-bubble">2</div>
                    <div class="step-label">
                        Pengecekan UUID
                    </div>
                </div>

                <div class="step">
                    <div class="step-bubble">3</div>
                    <div class="step-label">
                        Database Setup
                    </div>
                </div>

                <div class="step">
                    <div class="step-bubble">4</div>
                    <div class="step-label">
                        Konfigurasi
                    </div>
                </div>

                <div class="step">
                    <div class="step-bubble">5</div>
                    <div class="step-label">
                        Final Installation
                    </div>
                </div>

            </div>

        </header>

        <section class="wizard-body">

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

            <span class="badge badge-success">
                PRE-INSTALLATION
            </span>

            <h1>Konfigurasi Awal</h1>

            <div class="intro">
                Sistem akan memeriksa kebutuhan dasar aplikasi
                sebelum masuk ke pengecekan Installation UUID.
            </div>

            <?php if (($systemChecked ?? false) === true): ?>

            <div class="check-list">

                <?php foreach ($checks as $key => $check): ?>

                    <?php if ($key === 'allReady'): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div class="check-row">

                        <div class="check-icon <?= ($check['ok'] ?? false) ? 'ok' : 'fail' ?>">
                            <?= ($check['ok'] ?? false) ? '✓' : '!' ?>
                        </div>

                        <div>
                            <div class="check-name">
                                <?= esc($check['label'] ?? $key) ?>
                            </div>

                            <div class="check-value">
                                <?php if ($key === 'extensions' && !empty($check['details'])): ?>
                                    <?php foreach ($check['details'] as $extension => $ok): ?>
                                        <?= esc($extension) ?><?= $ok ? ' ✓' : ' ✕' ?><?= $extension !== array_key_last($check['details']) ? ' · ' : '' ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?= esc((string) ($check['value'] ?? '-')) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="check-status <?= ($check['ok'] ?? false) ? 'status-ok' : 'status-fail' ?>">
                            <?= ($check['ok'] ?? false) ? 'READY' : 'TIDAK SIAP' ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php endif; ?>

            <?php if (($systemChecked ?? false) !== true): ?>

                <div class="panel">
                    <strong>Pemeriksaan sistem belum dijalankan.</strong>
                    <p>
                        Klik tombol di bawah untuk memeriksa
                        persyaratan instalasi dan menyiapkan
                        file environment aplikasi.
                    </p>
                </div>

                <div class="actions">
                    <form
                        method="post"
                        action="<?= base_url('/trial/install/check-system') ?>"
                    >
                        <?= csrf_field() ?>

                        <button type="submit" class="btn btn-primary">
                            CEK PEMERIKSAAN SISTEM
                        </button>
                    </form>
                </div>

            <?php elseif (($allReady ?? false) === true): ?>

                <div class="panel">
                    <strong>Konfigurasi awal siap.</strong>
                    <br>
                    Semua kebutuhan dasar telah terpenuhi.
                    Pemeriksaan sistem berhasil. Selanjutnya
                    lakukan konfigurasi Base URL aplikasi.
                </div>



            <?php else: ?>

                <div class="panel panel-danger">
                    <strong>Konfigurasi awal belum siap.</strong>
                    <br>
                    Perbaiki persyaratan yang gagal, kemudian
                    jalankan pemeriksaan sistem kembali.
                </div>

                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/') ?>"
                    >
                        KEMBALI
                    </a>

                </div>

            <?php endif; ?>

            <?php if (($allReady ?? false) === true): ?>

                <div class="panel">
                    <strong>Konfigurasi Base URL Aplikasi</strong>
                    <p>
                        Masukkan domain HTTPS yang digunakan
                        untuk mengakses aplikasi Presensi Digital.
                    </p>
                </div>

                <form
                    method="post"
                    action="<?= base_url('/trial/install/base-url') ?>"
                    class="base-url-form"
                >
                    <?= csrf_field() ?>

                    <label
                        for="base_url"
                        style="display:block;font-weight:700;margin-bottom:8px;"
                    >
                        Base URL Aplikasi
                    </label>

                    <input
                        id="base_url"
                        name="base_url"
                        type="url"
                        <?= !empty($baseUrlSaved) ? 'readonly' : '' ?>
                        required
                        maxlength="255"
                        pattern="https://.*"
                        value="<?= esc((string) ($baseUrlSaved ?: $currentBaseUrl), 'attr') ?>"
                        placeholder="https://presensi.sekolah.sch.id/"
                        style="width:100%;padding:14px;border:1px solid #d1d5db;border-radius:10px;"
                    >

                    <div class="actions">


                        <?php if (empty($baseUrlSaved)): ?>
                            <a
                                href="<?= base_url('/beranda') ?>"
                                class="btn btn-secondary"
                            >
                                KELUAR INSTALASI
                            </a>
                            <button type="submit" class="btn btn-primary">
                                SIMPAN BASE URL
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (!empty($baseUrlSaved)): ?>
                    <div class="panel base-url-confirmation">
                        <strong>Base URL berhasil disimpan.</strong>
                        <p><?= esc($baseUrlSaved) ?></p>
                        <p>
                            Konfigurasi Tahap 1 telah lengkap.
                            Pengecekan Installation UUID dapat dilanjutkan.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($baseUrlSaved)): ?>
                    <div class="actions base-url-navigation">
                        <a
                            href="<?= base_url('/beranda') ?>"
                            class="btn btn-secondary"
                        >
                            KELUAR INSTALASI
                        </a>

                        <form
                            method="post"
                            action="<?= base_url('/trial/install') ?>"
                        >
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary">
                                CEK INSTALLATION UUID →
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <?php if (($systemChecked ?? false) === true
                && ($allReady ?? false) !== true): ?>

                <div class="actions">
                    <form
                        method="post"
                        action="<?= base_url('/trial/install/check-system') ?>"
                    >
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary">
                            PERIKSA ULANG SISTEM
                        </button>
                    </form>
                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
</body>
</html>
