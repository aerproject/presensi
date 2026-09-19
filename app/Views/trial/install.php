<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Pengecekan UUID</title>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e3a5f;
            --success: #16a34a;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --info-bg: #eff6ff;
            --info-border: #bfdbfe;
            --info-text: #1e40af;
            --border: #d1d5db;
            --muted: #6b7280;
            --text: #111827;
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
            background: #ffffff;
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 18px 55px rgba(15, 23, 42, .10);
        }

        .wizard-header {
            padding: 30px 34px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .wizard-title {
            margin: 0 0 28px;
            text-align: center;
            font-size: 30px;
            font-weight: 500;
            letter-spacing: -.02em;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0;
            align-items: start;
        }

        .step {
            position: relative;
            text-align: center;
            min-width: 0;
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

        .step.completed:not(:last-child)::after {
            background: var(--primary);
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

        .step.completed .step-bubble {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
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

        .step.active .step-label,
        .step.completed .step-label {
            color: #111827;
            font-weight: 700;
        }

        .wizard-body {
            padding: 38px 34px 34px;
        }

        .intro {
            text-align: center;
            margin-bottom: 24px;
            color: #374151;
            font-size: 16px;
        }

        .badge {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: .02em;
        }

        .badge-success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .badge-warning {
            background: var(--warning-bg);
            color: var(--warning-text);
        }

        .badge-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        h1 {
            margin: 0 0 24px;
            text-align: center;
            font-size: 32px;
            letter-spacing: -.025em;
        }

        .panel {
            margin-top: 18px;
            padding: 20px 22px;
            border-radius: 14px;
            border: 1px solid var(--info-border);
            background: var(--info-bg);
        }

        .panel-title {
            font-weight: 800;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .panel p {
            margin: 5px 0;
            line-height: 1.65;
        }

        .panel-info {
            color: var(--info-text);
        }

        .panel-success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: var(--success-text);
        }

        .panel-warning {
            background: var(--warning-bg);
            border-color: #fde68a;
            color: var(--warning-text);
        }

        .panel-danger {
            background: var(--danger-bg);
            border-color: #fecaca;
            color: var(--danger-text);
        }

        .uuid-box {
            margin-top: 18px;
            padding: 20px 22px;
            border-radius: 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .uuid-label {
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        .uuid-value {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 18px;
            color: #1e40af;
            word-break: break-all;
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
            transition: .15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-dark {
            background: var(--primary-dark);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: #111827;
            border-color: #d1d5db;
        }

        .btn-warning {
            background: #d97706;
            color: #fff;
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

            .wizard-title {
                font-size: 24px;
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

            h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>
<div class="wrap">

    <main class="wizard">

        <header class="wizard-header">

            <h2 class="wizard-title">
                Langkah Instalasi: Tahap 2
            </h2>

            <div class="steps">

                <div class="step completed">
                    <div class="step-bubble">✓</div>
                    <div class="step-label">
                        Konfigurasi Awal
                    </div>
                </div>

                <div class="step active">
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

            <div class="intro">
                Pengecekan lisensi dan Installation UUID selesai.
                Ikuti instruksi sesuai status instalasi.
            </div>

            <?php if (!$allowed): ?>

                <span class="badge badge-danger">
                    INSTALLATION BLOCKED
                </span>

                <h1>Instalasi Tidak Dapat Dilanjutkan</h1>

                <div class="panel panel-danger">
                    <?= esc(
                        $notice
                        ?? 'Instalasi tidak dapat dilakukan pada server ini.'
                    ) ?>
                </div>

                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/trial/install') ?>"
                    >
                        KEMBALI
                    </a>

                    <a
                        class="btn btn-primary"
                        href="<?= base_url('/trial/upgrade') ?>"
                    >
                        UPGRADE KE FULL
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
                            STOP INSTALASI
                        </button>
                    </form>

                </div>

            <?php else: ?>

                <?php if (($uuidDecision ?? '') === 'NEW_TRIAL'): ?>

                    <span class="badge badge-success">
                        TRIAL READY
                    </span>

                    <h1>Pengecekan UUID Berhasil</h1>

                    <div class="panel panel-info">
                        <div class="panel-title">
                            Instalasi aplikasi dapat dilanjutkan
                        </div>
                        <p>
                            Installation UUID belum terdaftar pada
                            License Server.
                        </p>
                        <p>
                            Instalasi Trial baru dapat dilakukan
                            pada server ini.
                        </p>
                    </div>

                <?php elseif (
                    ($uuidDecision ?? '') === 'EXISTING_TRIAL'
                ): ?>

                    <?php if (($uuidCanContinue ?? false) === true): ?>

                        <span class="badge badge-warning">
                            EXISTING TRIAL
                        </span>

                        <h1>Trial Sudah Terdaftar</h1>

                        <div class="panel panel-warning">
                            <div class="panel-title">
                                Instalasi ulang tidak dapat dilakukan
                            </div>

                            <p>
                                Server ini sudah memiliki lisensi
                                Trial aktif untuk Installation UUID ini.
                            </p>

                            <p>
                                Lisensi Trial berlaku sampai:
                                <strong>
                                    <?= esc(
                                        $uuidExpiresAt ?: '-'
                                    ) ?>
                                </strong>
                            </p>

                            <p>
                                Untuk menggunakan aplikasi ini,
                                kembali ke halaman utama,
                                hentikan instalasi, atau lakukan
                                upgrade ke Full.
                            </p>
                        </div>

                    <?php else: ?>

                        <span class="badge badge-danger">
                            TRIAL EXPIRED
                        </span>

                        <h1>Hak Akses Trial Sudah Expired</h1>

                        <div class="panel panel-danger">
                            <div class="panel-title">
                                Instalasi ulang tidak dapat dilakukan
                            </div>

                            <p>
                                Installation UUID ini sudah terdaftar
                                pada server ini.
                            </p>

                            <p>
                                Hak akses lisensi Trial sudah expired
                                pada:
                                <strong>
                                    <?= esc(
                                        $uuidExpiresAt ?: '-'
                                    ) ?>
                                </strong>
                            </p>

                            <p>
                                Trial baru tidak dapat dibuat untuk
                                Installation UUID ini.
                            </p>
                        </div>

                    <?php endif; ?>

                <?php elseif (
                    ($uuidDecision ?? '') === 'EXISTING_FULL'
                ): ?>

                    <span class="badge badge-danger">
                        EXISTING FULL
                    </span>

                    <h1>Lisensi Full Sudah Terdaftar</h1>

                    <div class="panel panel-danger">
                        <div class="panel-title">
                            Instalasi Trial tidak dapat dilakukan
                        </div>

                        <p>
                            Installation UUID ini sudah terdaftar
                            dengan lisensi Full pada server ini.
                        </p>
                    </div>

                <?php else: ?>

                    <?php if (!empty($uuidDecisionError)): ?>

                        <span class="badge badge-danger">
                            UUID CHECK FAILED
                        </span>

                        <h1>Pengecekan UUID Gagal</h1>

                        <div class="panel panel-danger">
                            <div class="panel-title">
                                License Server tidak dapat
                                memberikan keputusan instalasi.
                            </div>

                            <p>
                                <?= esc($uuidDecisionError) ?>
                            </p>
                        </div>

                    <?php else: ?>

                        <span class="badge badge-danger">
                            UNKNOWN STATE
                        </span>

                        <h1>Status Instalasi Tidak Dikenal</h1>

                        <div class="panel panel-danger">
                            <p>
                                Status Installation UUID tidak
                                dapat ditentukan dengan aman.
                                Instalasi dihentikan.
                            </p>
                        </div>

                    <?php endif; ?>

                <?php endif; ?>

                <div class="uuid-box">
                    <div class="uuid-label">
                        Installation UUID
                    </div>

                    <div class="uuid-value">
                        <?= esc($installationUuid ?: '-') ?>
                    </div>
                </div>

                <?php if (
                    ($uuidDecision ?? '') === 'NEW_TRIAL'
                ): ?>

                    <div class="actions">

                        <a
                            class="btn btn-secondary"
                            href="<?= base_url('/trial/install') ?>"
                        >
                            KEMBALI
                        </a>

                        <form
                            method="post"
                            action="<?= base_url('/trial/install/database') ?>"
                        >
                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-dark"
                            >
                                LANJUT KE DATABASE SETUP →
                            </button>
                        </form>

                    </div>

                <?php elseif (
                    ($uuidDecision ?? '') === 'EXISTING_TRIAL'
                ): ?>

                    <div class="actions">

                        <a
                            class="btn btn-secondary"
                            href="<?= base_url('/trial/install') ?>"
                        >
                            KEMBALI
                        </a>

                        <a
                            class="btn btn-warning"
                            href="<?= base_url('/trial/upgrade') ?>"
                        >
                            UPGRADE KE FULL
                        </a>

                        <form
                            method="post"
                            action="<?= base_url('/trial/install/stop') ?>"
                            style="display:inline;"
                        >
                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-secondary"
                            >
                                STOP INSTALASI
                            </button>
                        </form>

                    </div>

                <?php elseif (
                    ($uuidDecision ?? '') === 'EXISTING_FULL'
                ): ?>

                    <div class="actions">

                        <a
                            class="btn btn-secondary"
                            href="<?= base_url('/trial/install') ?>"
                        >
                            KEMBALI
                        </a>

                        <form
                            method="post"
                            action="<?= base_url('/trial/install/stop') ?>"
                            style="display:inline;"
                        >
                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-secondary"
                            >
                                STOP INSTALASI
                            </button>
                        </form>

                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </section>

    </main>

</div>
</body>
</html>
