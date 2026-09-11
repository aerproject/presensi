<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <h3 class="mb-3"><i class="bi bi-journal-text me-2"></i> Data Tahun Pelajaran</h3>

    <!-- Alert -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Tombol create -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bi bi-plus-circle"></i> Tambah Tahun Pelajaran
        </button>

        <!-- Filter perPage -->
        <form method="get" action="<?= base_url('admin/tapel') ?>" class="d-flex align-items-center gap-2">
            <select name="perPage" class="form-select w-auto">
                <option value="10" <?= $perPage==10?'selected':'' ?>>10</option>
                <option value="25" <?= $perPage==25?'selected':'' ?>>25</option>
                <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
                <option value="100" <?= $perPage==100?'selected':'' ?>>100</option>
            </select>
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </form>
    </div>

    <!-- Tabel -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="5%">#</th>
                    <th>Tahun Pelajaran</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($tapel)): ?>
                    <?php $no = 1 + ($pager->getCurrentPage('default')-1) * $pager->getPerPage('default'); ?>
                    <?php foreach($tapel as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($row['tahun_pelajaran']) ?></td>
                            <td><?= esc($row['semester']) ?></td>
                            <td>
                                <?php if(!$row['aktif']): ?>
                                    <a href="<?= base_url('admin/tapel/setActive/'.$row['id']) ?>" 
                                       class="btn btn-sm btn-primary"
                                       onclick="return confirm('Aktifkan Tahun Pelajaran ini?')">
                                        <i class="bi bi-check-circle"></i> Aktifkan
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('/admin/tapel/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('default', 'bootstrap') ?>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modalCreate" tabindex="-1">
  <div class="modal-dialog">
    <form action="<?= base_url('/admin/tapel/create') ?>" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Tambah Tahun Pelajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Tahun Pelajaran</label>
          <input type="text" name="tahun_pelajaran" class="form-control" placeholder="contoh: 2024/2025" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Semester</label>
          <select name="semester" class="form-select" required>
            <option value="">-- Pilih Semester --</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
          </select>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif">
          <label class="form-check-label" for="aktif">Aktif</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
