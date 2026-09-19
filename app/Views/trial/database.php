<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Database Setup</title>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e3a5f;
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

        .intro {
            text-align: center;
            color: #374151;
            margin-bottom: 26px;
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
            margin: 0 0 24px;
            text-align: center;
            font-size: 32px;
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

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 800;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            background: #fff;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
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
        }
    </style>
</head>

<body>
<div class="wrap">

    <main class="wizard">

        <header class="wizard-header">

            <h2 class="wizard-title">
                Langkah Instalasi: Tahap 3
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

                <div class="step active">
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

            <?php if (!empty($error)): ?>
                <div class="flash flash-error">
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="flash flash-success">
                    <?= esc($success) ?>
                </div>
            <?php endif; ?>

            <div class="intro">
                Konfigurasikan database aplikasi sebelum sistem
                menyiapkan database dan melanjutkan ke Tahap 4.
            </div>

            <span class="badge badge-success">
                NEW TRIAL
            </span>

            <h1>Database Setup</h1>

            <div class="panel">
                <strong>Installation UUID</strong><br>
                <?= esc($installationUuid ?? '-') ?>
                <br><br>
                <strong>UUID Decision:</strong>
                <?= esc($uuidDecision ?? '-') ?>
            </div>

            <?php if (
                !empty($connectionChecked)
                && !empty($databaseImported)
            ): ?>

                <div class="panel panel-success">
                    <strong>✅ Koneksi Database Berhasil</strong>
                    <br>
                    Database berhasil terhubung dengan konfigurasi
                    yang diberikan.
                </div>

                <div class="panel panel-success">
                    <strong>✅ Master SQL Berhasil Diimpor</strong>
                    <br>
                    File <strong>masterpresensi_fresh.sql</strong>
                    berhasil diproses ke database.
                </div>

                <div class="summary">

                    <div class="summary-row">
                        <div class="summary-label">
                            Database Host
                        </div>

                        <div class="summary-value">
                            <?= esc(
                                $db['hostname'] ?? '-'
                            ) ?>
                        </div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-label">
                            Database Name
                        </div>

                        <div class="summary-value">
                            <?= esc(
                                $db['database'] ?? '-'
                            ) ?>
                        </div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-label">
                            Database Username
                        </div>

                        <div class="summary-value">
                            <?= esc(
                                $db['username'] ?? '-'
                            ) ?>
                        </div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-label">
                            Status Tahap 3
                        </div>

                        <div class="summary-value">
                            <strong>
                                DATABASE READY
                            </strong>
                        </div>
                    </div>

                </div>

                <div class="actions">

                    <a
                        class="btn btn-secondary"
                        href="<?= base_url('/trial/install/uuid') ?>"
                    >
                        KEMBALI
                    </a>

                    <a
                        class="btn btn-primary"
                        href="<?= base_url('/trial/install/config') ?>"
                    >
                        LANJUT KE KONFIGURASI →
                    </a>

                </div>

            <?php else: ?>

                <form
                    method="post"
                    action="<?= base_url('/trial/install/database') ?>"
                >
                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="hostname">
                            Database Host
                        </label>

                        <input
                            type="text"
                            id="hostname"
                            name="hostname"
                            value="<?= esc(
                                $db['hostname'] ?? 'localhost'
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="port">
                            Database Port
                        </label>

                        <input
                            type="number"
                            id="port"
                            name="port"
                            value="<?= esc(
                                $db['port'] ?? 3306
                            ) ?>"
                            min="1"
                            max="65535"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="database">
                            Database Name
                        </label>

                        <input
                            type="text"
                            id="database"
                            name="database"
                            value="<?= esc(
                                $db['database'] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="username">
                            Database Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?= esc(
                                $db['username'] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">
                            Database Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            value=""
                        >
                    </div>

                    <div class="actions">

                        <a
                            class="btn btn-secondary"
                            href="<?= base_url('/trial/install/uuid') ?>"
                        >
                            KEMBALI
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            LANJUT KE PENGECEKAN KONEKSI →
                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </section>

    </main>

</div>
</body>
</html>
