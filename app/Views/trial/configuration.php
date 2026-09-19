<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Instalasi - Konfigurasi</title>

    <style>
        :root {
            --primary: #2563eb;
            --success-bg: #dcfce7;
            --success-text: #166534;
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
            background: var(--success-bg);
            color: var(--success-text);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        h1 {
            margin: 0 0 14px;
            text-align: center;
            font-size: 32px;
        }

        .intro {
            text-align: center;
            color: #374151;
            margin-bottom: 28px;
            line-height: 1.65;
        }

        .panel {
            margin-top: 18px;
            padding: 20px 22px;
            border-radius: 14px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            color: var(--info-text);
        }

        .panel-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .panel p {
            margin: 6px 0;
            line-height: 1.65;
        }

        .summary {
            margin-top: 18px;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        .summary-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 18px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .summary-label {
            font-weight: 800;
        }

        .summary-value {
            color: #374151;
            word-break: break-word;
        }

        .uuid {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
        }

        .actions {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
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

            .summary-row {
                grid-template-columns: 1fr;
                gap: 5px;
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

            <h1>Konfigurasi Aplikasi</h1>

            <div class="intro">
                Database telah berhasil disiapkan.
                Tahap berikutnya akan menulis konfigurasi aplikasi
                dan menjalankan proses Trial License otomatis.
            </div>

            <div class="panel">

                <div class="panel-title">
                    Proses yang akan dijalankan
                </div>

                <p>
                    1. Menulis konfigurasi database ke file
                    <strong>.env</strong>.
                </p>

                <p>
                    2. Menyiapkan koneksi ke Trial License Server.
                </p>

                <p>
                    3. Claim Trial berdasarkan Installation UUID.
                </p>

                <p>
                    4. Menyimpan credential Trial secara terenkripsi.
                </p>

                <p>
                    5. Menyimpan Trial License Key.
                </p>

                <p>
                    6. Aktivasi dan validasi lisensi Trial.
                </p>

            </div>

            <div class="summary">

                <div class="summary-row">
                    <div class="summary-label">
                        Installation UUID
                    </div>

                    <div class="summary-value uuid">
                        <?= esc(
                            $installationUuid ?? '-'
                        ) ?>
                    </div>
                </div>

                <div class="summary-row">
                    <div class="summary-label">
                        Database Host
                    </div>

                    <div class="summary-value">
                        <?= esc(
                            $database['hostname'] ?? '-'
                        ) ?>
                    </div>
                </div>

                <div class="summary-row">
                    <div class="summary-label">
                        Database Name
                    </div>

                    <div class="summary-value">
                        <?= esc(
                            $database['database'] ?? '-'
                        ) ?>
                    </div>
                </div>

                <div class="summary-row">
                    <div class="summary-label">
                        Database User
                    </div>

                    <div class="summary-value">
                        <?= esc(
                            $database['username'] ?? '-'
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="actions">

                <a
                    class="btn btn-secondary"
                    href="<?= base_url('/trial/install/database') ?>"
                >
                    KEMBALI
                </a>

                <form
                    method="post"
                    action="<?= base_url('/trial/install/config/process') ?>"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        LANJUT KE AKTIVASI TRIAL →
                    </button>

                </form>

            </div>

        </section>

    </main>

</div>
</body>
</html>
