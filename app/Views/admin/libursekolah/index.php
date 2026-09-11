<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <h3 class="mb-3"><i class="bi bi-calendar-event me-2"></i>Data Libur Sekolah</h3>

    <!-- Tombol create di kiri + filter perPage di kanan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('/admin/libursekolah/create') ?>" class="btn btn-primary">Tambah Libur</a>

        <form method="get" action="<?= base_url('admin/libursekolah') ?>" class="d-flex align-items-center gap-2">
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

    <!-- Tabel Libur -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th width="5%">NO</th>
            <th>TAHUN PELAJARAN</th>
            <th>SEMESTER</th>
            <th>TANGGAL</th>
            <th>KETERANGAN</th>
            <th width="20%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($libur)): ?>
            <?php $no = 1 + ($pager->getCurrentPage('default') - 1) * $pager->getPerPage('default'); ?>
            <?php foreach ($libur as $l): ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= esc($l['tahun_pelajaran']) ?></td>
                <td class="text-center"><?= esc($l['semester']) ?></td>
                <td class="text-center"><?= esc($l['tanggal']) ?></td>
                <td><?= esc($l['keterangan']) ?></td>
                <td class="text-center">
                  <a href="<?= base_url('/admin/libursekolah/edit/' . $l['id']) ?>" 
                    class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>

                  <a href="<?= base_url('/admin/libursekolah/delete/' . $l['id']) ?>" 
                    class="btn btn-sm btn-danger" 
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data libur ini?')">
                    <i class="bi bi-trash"></i> Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">Belum ada data libur sekolah.</td>
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
