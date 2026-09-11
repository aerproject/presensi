<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container-fluid">

  <h4>Edit Relasi Siswa - Kelas - Tapel</h4>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('admin/plotkelas/update/'.$plotkelas['id']) ?>" method="post">
    <?= csrf_field() ?>

    <!-- Siswa -->
    <div class="mb-3">
      <label for="siswa_id" class="form-label">Siswa</label>
      <select name="siswa_id" id="siswa_id" class="form-select" required>
        <?php foreach ($siswa as $s): ?>
          <option value="<?= $s['id'] ?>" <?= $s['id'] == $plotkelas['siswa_id'] ? 'selected' : '' ?>>
            <?= esc($s['nama_siswa']) ?> (<?= esc($s['nis']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Kelas -->
    <div class="mb-3">
      <label for="kelas_id" class="form-label">Kelas</label>
      <select name="kelas_id" id="kelas_id" class="form-select" required>
        <?php foreach ($kelas as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $k['id'] == $plotkelas['kelas_id'] ? 'selected' : '' ?>>
            <?= esc($k['nama_kelas']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Tapel -->
    <div class="mb-3">
      <label for="tapel_id" class="form-label">Tahun Pelajaran</label>
      <select name="tapel_id" id="tapel_id" class="form-select" required>
        <?php foreach ($tapel as $t): ?>
          <option value="<?= $t['id'] ?>" <?= $t['id'] == $plotkelas['tapel_id'] ? 'selected' : '' ?>>
            <?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Shift -->
    <div class="mb-3">
      <label for="shift" class="form-label">Shift</label>
      <select name="shift" id="shift" class="form-select">
        <option value="" <?= $plotkelas['shift'] === null ? 'selected' : '' ?>>Fullday</option>
        <option value="pagi" <?= $plotkelas['shift'] === 'pagi' ? 'selected' : '' ?>>Pagi</option>
        <option value="siang" <?= $plotkelas['shift'] === 'siang' ? 'selected' : '' ?>>Siang</option>
      </select>
    </div>

    <!-- Jam Masuk -->
    <div class="mb-3">
      <label for="jam_masuk" class="form-label">Jam Masuk</label>
      <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" 
             value="<?= esc($plotkelas['jam_masuk']) ?>" required>
    </div>

    <!-- Jam Pulang -->
    <div class="mb-3">
      <label for="jam_pulang" class="form-label">Jam Pulang</label>
      <input type="time" name="jam_pulang" id="jam_pulang" class="form-control" 
             value="<?= esc($plotkelas['jam_pulang']) ?>" required>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select name="status" id="status" class="form-select">
        <option value="aktif" <?= $plotkelas['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $plotkelas['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="<?= base_url('admin/plotkelas') ?>" class="btn btn-secondary">Batal</a>
  </form>

</div>
<?= $this->endSection() ?>
