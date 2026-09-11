<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

    <h3 class="mb-3">⚙️ Konfigurasi WhatsApp API</h3>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php elseif (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif ?>

    <!-- Tombol tambah provider -->
    <div class="mb-3">
        <a href="<?= base_url('/admin/wapikey/create') ?>" class="btn btn-success">
            ➕ Tambah Provider
        </a>
    </div>

    <!-- Tabel daftar provider -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Provider</th>
                    <th>API Key</th>
                    <th>Admin Phone</th>
                    <th>Status</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($providers)): ?>
                    <?php foreach ($providers as $i => $row): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= esc($row['provider']) ?></td>
                            <td><code><?= esc($row['wa_api_key']) ?></code></td>
                            <td><?= esc($row['admin_phone']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <!-- Tombol edit -->
                                <a href="<?= base_url('/admin/wapikey/edit/'.$row['id']) ?>" 
                                   class="btn btn-sm btn-primary">Edit</a>

                                <!-- Tombol hapus -->
                                <a href="<?= base_url('/admin/wapikey/delete/'.$row['id']) ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin hapus provider ini?')">Delete</a>

                                <!-- Tombol toggle status -->
                                <?php if ($row['status'] === 'active'): ?>
                                    <a href="<?= base_url('/admin/wapikey/setInactive/'.$row['id']) ?>" 
                                       class="btn btn-sm btn-warning">Set Inactive</a>
                                <?php else: ?>
                                    <a href="<?= base_url('/admin/wapikey/setActive/'.$row['id']) ?>" 
                                       class="btn btn-sm btn-success">Set Active</a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada provider tersimpan.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

</div>
<?= $this->endSection() ?>
