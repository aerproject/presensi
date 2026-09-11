<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
  <h3 class="mb-3">Edit Libur Sekolah</h3>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <form action="<?= base_url('/admin/libursekolah/update/' . $libur['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label for="tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
      <input type="text" class="form-control" id="tahun_pelajaran" name="tahun_pelajaran" 
             value="<?= esc($libur['tahun_pelajaran']) ?>" readonly>
    </div>

    <div class="mb-3">
      <label for="semester" class="form-label">Semester</label>
      <input type="text" class="form-control" id="semester" name="semester" 
             value="<?= esc($libur['semester']) ?>" readonly>
    </div>

    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal Libur</label>
      <input type="date" class="form-control" id="tanggal" name="tanggal" 
             value="<?= esc($libur['tanggal']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="keterangan" class="form-label">Keterangan</label>
      <input type="text" class="form-control" id="keterangan" name="keterangan" 
             value="<?= esc($libur['keterangan']) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= base_url('/admin/libursekolah') ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>

<?= $this->endSection() ?>
