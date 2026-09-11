<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

  <h3 class="mb-3"><i class="bi bi-collection me-2"></i> Tambah Kelas</h3>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('/admin/kelas/store') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label for="jurusan_id" class="form-label">Jurusan</label>
      <select name="jurusan_id" id="jurusan_id" class="form-select">
        <option value="">-- Pilih Jurusan --</option>
        <?php foreach ($jurusan as $j): ?>
          <option value="<?= $j['id'] ?>" <?= old('jurusan_id') == $j['id'] ? 'selected' : '' ?>>
            <?= esc($j['nama_jurusan']) ?>
          </option>
        <?php endforeach ?>
      </select>
      <?= session('errors.jurusan_id') ? '<div class="text-danger">'.session('errors.jurusan_id').'</div>' : '' ?>
    </div>

    <div class="mb-3">
      <label for="kelas" class="form-label">Kelas</label>
      <select name="kelas" id="kelas" class="form-select">
        <option value="">-- Pilih Kelas --</option>
        <option value="X" <?= old('kelas') == 'X' ? 'selected' : '' ?>>X</option>
        <option value="XI" <?= old('kelas') == 'XI' ? 'selected' : '' ?>>XI</option>
        <option value="XII" <?= old('kelas') == 'XII' ? 'selected' : '' ?>>XII</option>
      </select>
      <?= session('errors.kelas') ? '<div class="text-danger">'.session('errors.kelas').'</div>' : '' ?>
    </div>

    <div class="mb-3">
      <label for="nama_kelas" class="form-label">Nama Kelas</label>
      <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" value="<?= old('nama_kelas') ?>">
      <?= session('errors.nama_kelas') ? '<div class="text-danger">'.session('errors.nama_kelas').'</div>' : '' ?>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="<?= base_url('/admin/kelas') ?>" class="btn btn-secondary">Kembali</a>
  </form>

</div>
<?= $this->endSection() ?>
