<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
  <h3 class="mb-3"><i class="bi bi-person-badge me-2"></i>Data Guru</h3>

  <!-- Tombol create di kiri + filter perPage di kanan -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('/admin/guru/create') ?>" class="btn btn-primary">Tambah Guru</a>

    <form method="get" action="<?= base_url('admin/guru') ?>" class="d-flex align-items-center gap-2">
      <select name="perPage" class="form-select w-auto">
        <option value="10" <?= $perPage==10?'selected':'' ?>>10</option>
        <option value="25" <?= $perPage==25?'selected':'' ?>>25</option>
        <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
        <option value="100" <?= $perPage==100?'selected':'' ?>>100</option>
      </select>
      <button type="submit" class="btn btn-primary">Terapkan</button>
    </form>
  </div>

  <!-- Alert -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= esc(session()->getFlashdata('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= esc(session()->getFlashdata('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Tabel Guru -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th width="5%">NO</th>
          <th>Nama Guru</th>
          <th>NIP</th>
          <th width="25%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($guru)): ?>
          <?php $no = 1 + ($pager->getCurrentPage('default') - 1) * $pager->getPerPage('default'); ?>
          <?php foreach ($guru as $g): ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= esc($g['nama_guru']) ?></td>
              <td class="text-center"><?= esc($g['nip']) ?></td>
              <td class="text-center">
                <a href="<?= base_url('/admin/guru/edit/' . $g['id']) ?>" class="btn btn-sm btn-warning me-1">
                  <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="<?= base_url('/admin/guru/delete/' . $g['id']) ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Hapus guru ini?')">
                  <i class="bi bi-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center">Belum ada data guru.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Navigasi pager -->
  <div class="mt-4 d-flex justify-content-center">
    <div><?= $pager->links('default', 'bootstrap') ?></div>
  </div>
</div>

<?= $this->endSection() ?>
