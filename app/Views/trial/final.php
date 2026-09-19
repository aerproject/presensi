<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Instalasi - Tahap 5</title>

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

        .errors {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: 12px;
            background: var(--danger-bg);
            border: 1px solid #fecaca;
            color: var(--danger-text);
            line-height: 1.6;
        }

        .success-box {
            margin-bottom: 22px;
            padding: 18px 20px;
            border-radius: 14px;
            background: var(--success-bg);
            border: 1px solid #bbf7d0;
            color: var(--success-text);
            line-height: 1.65;
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
                Langkah Instalasi: Tahap 5
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

                <div class="step completed">
                    <div class="step-bubble">✓</div>
                    <div class="step-label">
                        Konfigurasi
                    </div>
                </div>

                <div class="step active">
                    <div class="step-bubble">5</div>
                    <div class="step-label">
                        Final Installation
                    </div>
                </div>

            </div>

        </header>

        <section class="wizard-body">

            <span class="badge">
                FINAL INSTALLATION
            </span>

            <h1>
                Buat Administrator
            </h1>

            <div class="intro">
                Aktivasi dan validasi Trial telah berhasil.
                Lengkapi data Administrator untuk menyelesaikan
                instalasi aplikasi.
            </div>

            <div class="panel">
                <strong>Installation UUID</strong><br>
                <?= esc($installationUuid ?? '-') ?>

                <br><br>

                <strong>Status Lisensi</strong><br>
                <?= esc($licenseStatus ?? 'ACTIVE / VALID') ?>
            </div>

            <?php if (!empty($adminCreated)): ?>

                <div class="success-box">
                    <strong>✅ Administrator berhasil dibuat.</strong>
                    <br><br>
                    Username:
                    <?= esc($adminUsername ?? '-') ?>
                    <br>
                    Email:
                    <?= esc($adminEmail ?? '-') ?>
                    <br><br>
                    Instalasi aplikasi telah selesai dan
                    siap digunakan.
                </div>

                <div class="actions">

                    <a
                        class="btn btn-primary"
                        href="<?= base_url('/auth/login') ?>"
                    >
                        SELESAI & LOGIN →
                    </a>

                </div>

            <?php else: ?>

                <?php if (!empty($success)): ?>
                    <div class="success-box">
                        <?= esc($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="errors">
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors) && is_array($errors)): ?>
                    <div class="errors">
                        <?php foreach ($errors as $message): ?>
                            <div><?= esc($message) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form
                    method="post"
                    action="<?= base_url('/trial/install/finalize') ?>"
                >

                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="nama_admin">
                            Nama Administrator
                        </label>

                        <input
                            type="text"
                            id="nama_admin"
                            name="nama_admin"
                            value="<?= esc(
                                old(
                                    'nama_admin',
                                    $namaAdmin ?? ''
                                )
                            ) ?>"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="wa_admin">
                            No. WhatsApp Administrator
                        </label>

                        <input
                            type="text"
                            id="wa_admin"
                            name="wa_admin"
                            value="<?= esc(
                                old(
                                    'wa_admin',
                                    $waAdmin ?? ''
                                )
                            ) ?>"
                            maxlength="20"
                            autocomplete="tel"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="username">
                            Username Administrator
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?= esc(
                                old(
                                    'username',
                                    $username ?? ''
                                )
                            ) ?>"
                            maxlength="50"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="email">
                            Email Administrator
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= esc(
                                old(
                                    'email',
                                    $email ?? ''
                                )
                            ) ?>"
                            maxlength="100"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">
                            Password Administrator
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password_confirm">
                            Konfirmasi Password
                        </label>

                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="actions">

                        <a
                            class="btn btn-secondary"
                            href="<?= base_url('/trial/install/config') ?>"
                        >
                            KEMBALI
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            SELESAIKAN INSTALASI →
                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </section>

    </main>

</div>
</body>
</html>
