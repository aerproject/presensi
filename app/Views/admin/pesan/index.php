<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-bell me-2"></i>
                Notifikasi Pesan Absensi
            </h3>
            <small class="text-muted">
                Monitor queue WhatsApp: pending, sent, dan failed.
            </small>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- SUMMARY -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-3 fw-bold text-warning">
                        <?= number_format((int) ($summary['pending'] ?? 0)) ?>
                    </div>
                    <div class="small text-muted">
                        Menunggu diproses worker
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Sent</div>
                    <div class="fs-3 fw-bold text-success">
                        <?= number_format((int) ($summary['sent'] ?? 0)) ?>
                    </div>
                    <div class="small text-muted">
                        Berhasil dikirim
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Failed</div>
                    <div class="fs-3 fw-bold text-danger">
                        <?= number_format((int) ($summary['failed'] ?? 0)) ?>
                    </div>
                    <div class="small text-muted">
                        Gagal dikirim
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <form method="get" class="row g-2">

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending"
                            <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>
                            Pending
                        </option>
                        <option value="sent"
                            <?= ($status ?? '') === 'sent' ? 'selected' : '' ?>>
                            Sent
                        </option>
                        <option value="failed"
                            <?= ($status ?? '') === 'failed' ? 'selected' : '' ?>>
                            Failed
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="masuk"
                            <?= ($jenis ?? '') === 'masuk' ? 'selected' : '' ?>>
                            Masuk
                        </option>
                        <option value="pulang"
                            <?= ($jenis ?? '') === 'pulang' ? 'selected' : '' ?>>
                            Pulang
                        </option>
                        <option value="alpha"
                            <?= ($jenis ?? '') === 'alpha' ? 'selected' : '' ?>>
                            Alpha
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Kelas</label>
                    <select name="kelas" class="form-select">
                        <option value="">Semua Kelas</option>

                        <?php foreach ($kelasList as $k): ?>
                            <option
                                value="<?= esc($k['id']) ?>"
                                <?= ($k['id'] == ($kelas ?? '')) ? 'selected' : '' ?>
                            >
                                <?= esc($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nama / NIS</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Cari nama atau NIS..."
                        value="<?= esc($nama ?? '') ?>"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tampilkan</label>
                    <select name="perPage" class="form-select">
                        <?php foreach ([10, 25, 50, 100] as $n): ?>
                            <option
                                value="<?= $n ?>"
                                <?= ((int) $perPage === $n) ? 'selected' : '' ?>
                            >
                                <?= $n ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-dark text-center">
                        <tr>
                            <th width="4%">ID</th>
                            <th width="14%">Siswa</th>
                            <th width="8%">NIS</th>
                            <th width="9%">Kelas</th>
                            <th width="7%">Jenis</th>
                            <th width="8%">Status</th>
                            <th width="12%">Waktu</th>
                            <th>Pesan</th>
                            <th width="18%">Error</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (empty($pesan)): ?>

                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data pesan.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($pesan as $p): ?>

                            <?php
                                $statusValue = strtolower((string) ($p['status'] ?? ''));

                                $badgeClass = match ($statusValue) {
                                    'sent'    => 'bg-success',
                                    'failed'  => 'bg-danger',
                                    'pending' => 'bg-warning text-dark',
                                    default   => 'bg-secondary'
                                };
                            ?>

                            <tr>

                                <td class="text-center">
                                    <?= esc($p['id']) ?>
                                </td>

                                <td>
                                    <?= esc($p['nama_siswa'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= esc($p['nis'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= esc($p['nama_kelas'] ?? 'Belum ditempatkan') ?>
                                </td>

                                <td class="text-center">
                                    <?= esc($p['jenis_pesan'] ?? '-') ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= esc(strtoupper($statusValue)) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= !empty($p['waktu_kirim'])
                                        ? date('d-m-Y H:i:s', strtotime($p['waktu_kirim']))
                                        : '-'
                                    ?>
                                </td>

                                <td>
                                    <div style="white-space: pre-line;">
                                        <?= esc($p['isi_pesan'] ?? '') ?>
                                    </div>
                                </td>

                                <td>

                                    <?php if ($statusValue === 'failed'): ?>

                                        <div class="text-danger small">
                                            <?= esc($p['error_message'] ?? 'Tidak ada detail error.') ?>
                                        </div>

                                    <?php else: ?>

                                        <span class="text-muted">-</span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <!-- PAGINATION -->
    <div class="mt-3">
        <?= $pager->links('default', 'bootstrap') ?>
    </div>

</div>

<?= $this->endSection() ?>
