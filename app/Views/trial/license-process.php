<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Tahap 4</title>

    <style>
        :root {
            --primary: #2563eb;
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

        .step.completed .step-bubble,
        .step.active .step-bubble {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        .step.active .step-bubble {
            box-shadow: 0 0 0 6px rgba(37, 99, 235, .10);
        }

        .step-label {
            font-size: 14px;
            line-height: 1.35;
            color: var(--muted);
            white-space: nowrap;
        }

        .step.completed .step-label,
        .step.active .step-label {
            color: var(--text);
            font-weight: 700;
        }

        .wizard-body {
            padding: 38px 34px 34px;
        }

        .badge {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
            background: var(--success-bg);
            color: var(--success-text);
        }

        h1 {
            margin: 0 0 12px;
            text-align: center;
            font-size: 32px;
        }

        .intro {
            text-align: center;
            color: #374151;
            margin-bottom: 26px;
            line-height: 1.6;
        }

        .panel {
            margin-bottom: 22px;
            padding: 18px 20px;
            border-radius: 14px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            color: var(--info-text);
            line-height: 1.65;
        }

        .panel-success {
            background: var(--success-bg);
            border-color: #bbf7d0;
            color: var(--success-text);
        }

        .panel-danger {
            background: var(--danger-bg);
            border-color: #fecaca;
            color: var(--danger-text);
        }

        .uuid-box {
            margin-bottom: 22px;
            padding: 18px 20px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .uuid-label {
            font-weight: 800;
            margin-bottom: 7px;
        }

        .uuid-value {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            color: #1e40af;
            word-break: break-all;
        }

        .process-list {
            display: grid;
            gap: 12px;
            margin-top: 20px;
        }

        .process-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .process-item.success {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .process-item.error {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .process-item.pending {
            background: #f9fafb;
        }

        .process-icon {
            width: 28px;
            height: 28px;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 800;
        }

        .process-icon.success {
            background: #dcfce7;
            color: #166534;
        }

        .process-icon.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .process-icon.pending {
            background: #e5e7eb;
            color: #6b7280;
        }

        .process-message {
            margin-top: 5px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.5;
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
        }
    </style>
</head>

<body>
<div class="wrap">

    <main class="wizard">

        <header class="wizard-header">

            <h2 class="wizard-title">
                Langkah Instalasi: Tahap 4
            </h2>

            <div class="steps">

                <div class="step completed">
                    <div class="step-bubble">✓</div>
                    <div class="step-label">
                        Konfigurasi Awal
                    </div>
                </div>

                <div class="step completed">
                    <div class="step-bubble">✓</div>
                    <div class="step-label">
                        Pengecekan UUID
                    </div>
                </div>

                <div class="step completed">
                    <div class="step-bubble">✓</div>
                    <div class="step-label">
                        Database Setup
                    </div>
                </div>

                <div class="step active">
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

            <span class="badge">
                CONFIGURATION
            </span>

            <h1>
                Konfigurasi & Aktivasi Trial
            </h1>

            <div class="intro">
                Sistem sedang menyelesaikan konfigurasi aplikasi
                dan proses aktivasi Trial License.
                Hasil setiap proses ditampilkan di bawah ini.
            </div>

            <div class="uuid-box">
                <div class="uuid-label">
                    Installation UUID
                </div>

                <div class="uuid-value">
                    <?= esc($installationUuid ?? '-') ?>
                </div>

                <?php if (!empty($licenseType)): ?>
                    <div style="margin-top: 12px;">
                        <strong>License Type:</strong>
                        <?= esc(strtoupper($licenseType)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $steps = [
                'store' => 'Store License Credential',
                'license_key' => 'Store Trial License Key',
                'activate' => 'Activate Trial License',
                'validate' => 'Validate Trial License',
                'final' => 'Verifikasi Final Instalasi',
            ];
            ?>

            <div class="process-list">

                <?php foreach ($steps as $key => $label): ?>

                    <?php
                    $status = strtolower(
                        trim(
                            (string) (
                                $stepsStatus[$key]['status']
                                ?? 'pending'
                            )
                        )
                    );

                    $message = (string) (
                        $stepsStatus[$key]['message']
                        ?? 'Belum diproses.'
                    );

                    if ($status === 'success') {
                        $itemClass = 'success';
                        $iconClass = 'success';
                        $icon = '✓';
                    } elseif ($status === 'error') {
                        $itemClass = 'error';
                        $iconClass = 'error';
                        $icon = '!';
                    } else {
                        $itemClass = 'pending';
                        $iconClass = 'pending';
                        $icon = '–';
                    }
                    ?>

                    <div class="process-item <?= esc($itemClass) ?>">

                        <span class="process-icon <?= esc($iconClass) ?>">
                            <?= esc($icon) ?>
                        </span>

                        <div>
                            <strong>
                                <?= esc($label) ?>
                            </strong>

                            <div class="process-message">
                                <?= esc($message) ?>
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php if (($overallStatus ?? '') === 'success'): ?>

                <div class="panel panel-success" style="margin-top: 22px;">
                    <strong>Konfigurasi Tahap 4 berhasil.</strong>
                    <br>
                    Environment aplikasi, credential lisensi,
                    Trial License Key, aktivasi, validasi, dan
                    verifikasi final telah berhasil diselesaikan.
                </div>

                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/trial/install/config') ?>"
                    >
                        KEMBALI
                    </a>

                    <a
                        class="btn btn-primary"
                        href="<?= base_url('/trial/install/admin') ?>"
                    >
                        LANJUT KE FINAL INSTALLATION →
                    </a>

                </div>

            <?php else: ?>

                <div class="panel panel-danger" style="margin-top: 22px;">
                    <strong>Proses Tahap 4 belum selesai.</strong>
                    <br>
                    Periksa proses yang berstatus gagal sebelum
                    melanjutkan ke Tahap 5.
                </div>

                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/trial/install/config') ?>"
                    >
                        KEMBALI KE KONFIGURASI
                    </a>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>
</body>
</html>
