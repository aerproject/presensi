<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container-fluid">

  <h4>Tambah Relasi Siswa - Kelas - Tapel</h4>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('admin/plotkelas/store') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Pilihan Mode -->
    <div class="mb-3">
      <label class="form-label">Mode Penambahan</label>
      <select name="mode" id="mode" class="form-select" >
        <option value="satuan" <?= old('mode','satuan') == 'satuan' ? 'selected' : '' ?>>Tambah Per Siswa</option>
        <option value="kelas" <?= old('mode') == 'kelas' ? 'selected' : '' ?>>Tambah Per Kelas</option>
      </select>
    </div>

    <!-- Mode Satuan -->
    <div id="mode-satuan">
      <div class="mb-3">
        <label for="siswa_id" class="form-label">Siswa</label>
        <select name="siswa_id" id="siswa_id" class="form-select">
          <option value="">-- Pilih Siswa --</option>
          <?php if (!empty($siswa)): ?>
            <?php foreach ($siswa as $s): ?>
              <option value="<?= $s['id'] ?>" <?= old('siswa_id') == $s['id'] ? 'selected' : '' ?>>
                <?= esc($s['nama_siswa']) ?> (<?= esc($s['nis']) ?>)
              </option>
            <?php endforeach ?>
          <?php endif; ?>
        </select>
        <?= session('errors.siswa_id') ? '<div class="text-danger">'.session('errors.siswa_id').'</div>' : '' ?>
      </div>
    </div>

    <!-- Mode Per Kelas -->
    <div id="mode-kelas" class="d-none">
      <div class="mb-3">
        <label for="kelas_id" class="form-label">Kelas</label>
        <select name="kelas_id" id="kelas_id" class="form-select">
          <option value="">-- Pilih Kelas --</option>
          <?php if (!empty($kelas)): ?>
            <?php foreach ($kelas as $k): ?>
              <option value="<?= $k['id'] ?>" <?= old('kelas_id') == $k['id'] ? 'selected' : '' ?>>
                <?= esc($k['nama_kelas']) ?>
              </option>
            <?php endforeach ?>
          <?php endif; ?>
        </select>
        <?= session('errors.kelas_id') ? '<div class="text-danger">'.session('errors.kelas_id').'</div>' : '' ?>
      </div>
    </div>

    <!-- Tapel -->
    <div class="mb-3">
      <label for="tapel_id" class="form-label">Tahun Pelajaran</label>
      <select name="tapel_id" id="tapel_id" class="form-select">
        <option value="">-- Pilih Tapel --</option>
        <?php if (!empty($tapel)): ?>
          <?php foreach ($tapel as $t): ?>
            <option value="<?= $t['id'] ?>" <?= old('tapel_id') == $t['id'] ? 'selected' : '' ?>>
              <?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)
            </option>
          <?php endforeach ?>
        <?php endif; ?>
      </select>
      <?= session('errors.tapel_id') ? '<div class="text-danger">'.session('errors.tapel_id').'</div>' : '' ?>
    </div>

    <!-- Shift -->
    <div class="mb-3">
      <label for="shift" class="form-label">Shift</label>
      <select name="shift" id="shift" class="form-select">
        <option value="" <?= old('shift') === null ? 'selected' : '' ?>>Pilih Shift</option>
        <option value="pagi" <?= old('shift') == 'pagi' ? 'selected' : '' ?>>Pagi</option>
        <option value="siang" <?= old('shift') == 'siang' ? 'selected' : '' ?>>Siang</option>
      </select>
      <?= session('errors.shift') ? '<div class="text-danger">'.session('errors.shift').'</div>' : '' ?>
    </div>

    <!-- Jam Masuk -->
    <div class="mb-3">
      <label for="jam_masuk" class="form-label">Jam Masuk</label>
      <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" value="<?= old('jam_masuk') ?>">
      <?= session('errors.jam_masuk') ? '<div class="text-danger">'.session('errors.jam_masuk').'</div>' : '' ?>
    </div>

    <!-- Jam Pulang -->
    <div class="mb-3">
      <label for="jam_pulang" class="form-label">Jam Pulang</label>
      <input type="time" name="jam_pulang" id="jam_pulang" class="form-control" value="<?= old('jam_pulang') ?>">
      <?= session('errors.jam_pulang') ? '<div class="text-danger">'.session('errors.jam_pulang').'</div>' : '' ?>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="<?= base_url('admin/plotkelas') ?>" class="btn btn-secondary">Kembali</a>
  </form>

</div>
<script src="<?= base_url('js/admin-plotkelas-created.js') ?>"></script>

<?= $this->endSection() ?>
