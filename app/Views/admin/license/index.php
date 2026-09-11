<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Lisensi</h1>
            <p class="text-muted mb-0">
                Informasi status lisensi aplikasi.
            </p>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Status Lisensi</h5>
                            <small class="text-muted">
                                Status lokal runtime lisensi
                            </small>
                        </div>

                        <?php if ($license->isValid()): ?>
                            <span class="badge text-bg-success">
                                VALID
                            </span>
                        <?php else: ?>
                            <span class="badge text-bg-danger">
                                <?= esc(
                                    strtoupper(
                                        $license->status() ?? 'UNKNOWN'
                                    )
                                ) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">
                                    Jenis Lisensi
                                </div>

                                <?php if ($license->isTrial()): ?>
                                    <div class="fs-5 fw-semibold">
                                        TRIAL
                                    </div>
                                <?php elseif ($license->isFull()): ?>
                                    <div class="fs-5 fw-semibold">
                                        FULL
                                    </div>
                                <?php else: ?>
                                    <div class="fs-5 fw-semibold text-muted">
                                        UNKNOWN
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">
                                    Status
                                </div>

                                <div class="fs-5 fw-semibold">
                                    <?= esc(
                                        strtoupper(
                                            $license->status() ?? 'UNKNOWN'
                                        )
                                    ) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">
                                    Berlaku Sampai
                                </div>

                                <?php
                                $expiresAt = $license->expiresAt();
                                ?>

                                <?= $expiresAt
                                    ? esc(
                                        $expiresAt->format(
                                            'd F Y H:i'
                                        )
                                    )
                                    : '-'
                                ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">
                                    Sisa Masa Berlaku
                                </div>

                                <?php
                                $days = $license->daysRemaining();
                                ?>

                                <div class="fs-5 fw-semibold">
                                    <?= $days !== null
                                        ? esc((string) $days) . ' hari'
                                        : '-'
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <?php if ($license->isTrial()): ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <div class="mb-3">
                            <h5 class="mb-1">
                                Upgrade Lisensi Full
                            </h5>

                            <small class="text-muted">
                                Upgrade lisensi Trial ke Full pada server yang sama.
                            </small>
                        </div>

                        <div class="alert alert-info mb-4">
                            Server UUID tetap dipertahankan.
                            Proses upgrade menggunakan
                            <strong>Full License Key</strong>
                            dan <strong>API Key Full</strong>.
                        </div>

                        <form
                            method="post"
                            action="<?= base_url('trial/upgrade/bootstrap') ?>"
                        >
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label
                                    for="full_license_key"
                                    class="form-label fw-semibold"
                                >
                                    Full License Key
                                </label>

                                <input
                                    type="text"
                                    id="full_license_key"
                                    name="license_key"
                                    class="form-control"
                                    value="<?= esc(old('license_key')) ?>"
                                    placeholder="Masukkan Full License Key"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label
                                    for="full_api_key"
                                    class="form-label fw-semibold"
                                >
                                    API Key Full
                                </label>

                                <input
                                    type="text"
                                    id="full_api_key"
                                    name="api_key"
                                    class="form-control"
                                    value="<?= esc(old('api_key')) ?>"
                                    placeholder="Masukkan API Key Full"
                                    required
                                >
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-warning"
                                >
                                    Upgrade Lisensi Full
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            <?php endif; ?>

            <?php if ($license->isFull()): ?>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">

                        <div class="mb-3">
                            <h5 class="mb-1">
                                Perpanjangan Lisensi Full
                            </h5>

                            <small class="text-muted">
                                Ajukan perpanjangan masa berlaku lisensi
                                Full untuk server ini.
                            </small>
                        </div>

                        <div class="alert alert-success mb-4">
                            Lisensi Full saat ini:
                            <strong>
                                <?= esc(
                                    strtoupper(
                                        $license->status() ?? 'UNKNOWN'
                                    )
                                ) ?>
                            </strong>.

                            <?php
                            $fullExpiresAt = $license->expiresAt();
                            ?>

                            <br>

                            Berlaku sampai:
                            <strong>
                                <?= $fullExpiresAt
                                    ? esc(
                                        $fullExpiresAt->format(
                                            'd F Y H:i'
                                        )
                                    )
                                    : '-'
                                ?>
                            </strong>
                        </div>

                        <a
                            href="<?= base_url('admin/license/renew') ?>"
                            class="btn btn-primary"
                        >
                            Ajukan Perpanjangan Lisensi
                        </a>

                    </div>
                </div>

            <?php endif; ?>

        </div>

        <div class="col-lg-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="mb-3">
                        Identity Audit
                    </h5>

                    <dl class="row mb-3">

                        <dt class="col-5 small text-muted">
                            Status
                        </dt>

                        <dd class="col-7">
                            <?= esc(
                                strtoupper(
                                    $identity['status'] ?? '-'
                                )
                            ) ?>
                        </dd>

                        <dt class="col-5 small text-muted">
                            Risk Level
                        </dt>

                        <dd class="col-7">
                            <?= esc(
                                strtoupper(
                                    $identity['risk_level'] ?? '-'
                                )
                            ) ?>
                        </dd>

                        <dt class="col-5 small text-muted">
                            Checked At
                        </dt>

                        <dd class="col-7">
                            <?= esc(
                                $identity['checked_at'] ?? '-'
                            ) ?>
                        </dd>

                    </dl>

                    <h5 class="mb-3">
                        Server Identity
                    </h5>

                    <dl class="row mb-0">

                        <dt class="col-5 small text-muted">
                            Server ID
                        </dt>

                        <dd class="col-7">
                            <?= esc(
                                (string) (
                                    $license->all()['server_id']
                                    ?? '-'
                                )
                            ) ?>
                        </dd>

                        <dt class="col-5 small text-muted">
                            Server UUID
                        </dt>

                        <dd class="col-7 text-break">
                            <?= esc(
                                (string) (
                                    $license->all()['server_uuid']
                                    ?? '-'
                                )
                            ) ?>
                        </dd>

                    </dl>

                </div>
            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
