<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">
                Ajukan Perpanjangan Lisensi
            </h1>

            <p class="text-muted mb-0">
                Pengajuan perpanjangan Lisensi Full untuk server ini.
            </p>
        </div>

        <a
            href="<?= base_url('/admin/license') ?>"
            class="btn btn-secondary"
        >
            Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="alert alert-info">
                Pengajuan ini tidak langsung mengubah masa berlaku lisensi.
                Setelah diajukan, status akan menjadi
                <strong>Pending</strong> dan diproses melalui mekanisme renewal.
            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">
                            Jenis Lisensi
                        </div>

                        <div class="fw-bold">
                            <?= esc(
                                strtoupper(
                                    $license->type()
                                )
                            ) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">
                            Status
                        </div>

                        <div class="fw-bold">
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
                        <div class="text-muted small">
                            Kondisi Lisensi
                        </div>

                        <div class="fw-bold">
                            <?= esc(
                                strtoupper(
                                    $license->condition()
                                )
                            ) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">
                            Berlaku Sampai
                        </div>

                        <div class="fw-bold">
                            <?= esc(
                                $license->expiresAt()
                                    ? $license
                                        ->expiresAt()
                                        ->format('d M Y H:i')
                                    : '-'
                            ) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="border rounded p-3">
                        <div class="text-muted small">
                            Server UUID
                        </div>

                        <div class="fw-bold text-break">
                            <?= esc(
                                $identity['server_uuid']
                                    ?? '-'
                            ) ?>
                        </div>
                    </div>
                </div>

            </div>

            <form
                method="post"
                action="<?= base_url('/admin/license/renew') ?>"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="notes"
                        class="form-label fw-semibold"
                    >
                        Catatan Pengajuan
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="5"
                        class="form-control"
                        placeholder="Tambahkan catatan pengajuan renewal jika diperlukan."
                    ><?= esc(old('notes')) ?></textarea>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Ajukan Perpanjangan
                    </button>

                    <a
                        href="<?= base_url('/admin/license') ?>"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
