<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
  <h3 class="mb-3"><i class="bi bi-diagram-3 me-2"></i>Data Jurusan</h3>
  <a href="<?= base_url('admin/jurusan/create') ?>" class="btn btn-primary mb-3">Tambah Jurusan</a>

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

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th width="5%">NO</th>
          <th>Nama Jurusan</th>
          <th width="27%">Kode</th>
          <th width="25%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($jurusan)): ?>
          <?php foreach ($jurusan as $index => $j): ?>
            <tr>
              <td class="text-center"><?= $index + 1 ?></td>
              <td><?= esc($j['nama_jurusan']) ?></td>
              <td class="text-center"><?= esc($j['kode_jurusan']) ?></td>
              <td class="text-center">
                <a href="<?= base_url('admin/jurusan/edit/' . $j['id']) ?>" class="btn btn-sm btn-warning me-1">
                  <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="<?= base_url('admin/jurusan/delete/' . $j['id']) ?>" 
                   class="btn btn-sm btn-danger"
                   data-confirm-delete-jurusan>
                  <i class="bi bi-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center">Belum ada data jurusan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?= base_url('js/admin-jurusan-index.js') ?>"></script>
<?= $this->endSection() ?>
