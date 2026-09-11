<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
  <h3 class="mb-3"><i class="bi bi-diagram-3 me-2"></i>Tambah Jurusan</h3>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('admin/jurusan/store') ?>" method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label for="nama_jurusan" class="form-label">Nama Jurusan</label>
      <input type="text" 
             class="form-control <?= (isset(session()->getFlashdata('errors')['nama_jurusan'])) ? 'is-invalid' : '' ?>" 
             id="nama_jurusan" 
             name="nama_jurusan" 
             value="<?= old('nama_jurusan') ?>">
    </div>

    <div class="mb-3">
      <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
      <input type="text" 
             class="form-control <?= (isset(session()->getFlashdata('errors')['kode_jurusan'])) ? 'is-invalid' : '' ?>" 
             id="kode_jurusan" 
             name="kode_jurusan" 
             value="<?= old('kode_jurusan') ?>">
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/jurusan') ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>
<?= $this->endSection() ?>
