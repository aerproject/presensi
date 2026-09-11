<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
<h3 class="mb-3"><i class="bi bi-collection me-2"></i>Tambah Mapping Kelas</h3>

<form action="<?= base_url('admin/mapping/store') ?>" method="post">
  <?= csrf_field() ?>

  <div class="mb-3">
    <label class="form-label">Kelas Lama</label>
    <select name="kelas_id" class="form-select" required>
      <option value="">-- Pilih Kelas Lama --</option>
      <?php foreach ($kelas as $k): ?>
        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
      <?php endforeach ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Kelas Baru</label>
    <select name="next_kelas_id" class="form-select">
      <option value="">-- Lulus / Tidak Ada --</option>
      <?php foreach ($kelas as $k): ?>
        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
      <?php endforeach ?>
    </select>
  </div>

  <button type="submit" class="btn btn-success">Simpan</button>
  <a href="<?= base_url('admin/mapping') ?>" class="btn btn-secondary">Kembali</a>
</form>

</div>
<?= $this->endSection() ?>
