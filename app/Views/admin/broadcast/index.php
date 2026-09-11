<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

    <h3 class="mb-3"><i class="bi bi-chat-dots me-2"></i> Daftar Pesan WhatsApp</h3>


    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <!-- Filter jumlah per page -->
        <form method="get" class="d-flex align-items-center">
            <label for="perPage" class="me-2">Tampilkan:</label>
            <select name="perPage" id="perPage" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="10" <?= ($perPage == 10 ? 'selected' : '') ?>>10</option>
                <option value="25" <?= ($perPage == 25 ? 'selected' : '') ?>>25</option>
                <option value="50" <?= ($perPage == 50 ? 'selected' : '') ?>>50</option>
                <option value="100" <?= ($perPage == 100 ? 'selected' : '') ?>>100</option>
            </select>
        </form>

        <a href="<?= base_url('/admin/broadcast-create') ?>" class="btn btn-success">➕ Buat Pesan Baru</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Jenis Pesan</th>
                    <th>Status</th>
                    <th>Waktu Kirim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($broadcasts)): ?>
                    <?php foreach ($broadcasts as $i => $b): ?>
                        <tr>
                            <td><?= ($pager->getCurrentPage() - 1) * $perPage + ($i + 1) ?></td>
                            <td><?= esc($b['judul']) ?></td>
                            <td><?= esc($b['jenis_pesan']) ?></td>
                            <td>
                                <?php if ($b['status'] === 'queued'): ?>
                                    <span class="badge bg-warning">Queued</span>
                                <?php elseif ($b['status'] === 'sent'): ?>
                                    <span class="badge bg-success">Sent</span>
                                <?php elseif ($b['status'] === 'failed'): ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php elseif ($b['status'] === 'partial'): ?>
                                    <span class="badge bg-info">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $b['waktu_kirim'] ?? '-' ?></td>
                            <td>
                                <a href="<?= base_url('/admin/broadcast/send/'.$b['id']) ?>" class="btn btn-sm btn-primary">Kirim</a>
                                <a href="<?= base_url('/admin/broadcast/delete/'.$b['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada broadcast.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginate -->
    <div class="d-flex justify-content-center">
        <?= $pager->links('default', 'bootstrap') ?>
    </div>

</div>
<?= $this->endSection() ?>
