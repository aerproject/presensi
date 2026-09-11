<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container-fluid mt-4">
  <h2 class="mb-3">Ploat Data Semester</h2>

  <!-- Notifikasi -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form action="<?= base_url('admin/plotkelas/semester-proses') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Tapel Lama -->
    <div class="mb-3">
      <label for="old_tapel_id" class="form-label">Tapel Lama</label>
      <select name="old_tapel_id" id="old_tapel_id" class="form-select" required>
        <option value="">-- Pilih Tapel Lama --</option>
        <?php foreach ($tapel as $t): ?>
          <option value="<?= $t['id'] ?>"><?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)</option>
        <?php endforeach ?>
      </select>
    </div>

    <!-- Tapel Baru --><?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
  <h2 class="mb-3">Plot Data Siswa</h2>

  <!-- Notifikasi -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Kolom Kiri -->
    <div class="col-md-5">
      <h5>Pilih Semester (Sumber)</h5>
      <form method="get" class="mb-3">
        <select name="semester_sumber" class="form-select" onchange="this.form.submit()">
            <option value="">-- Pilih Semester --</option>
            <?php foreach ($semesterList as $s): ?>
            <option value="<?= esc($s['id']) ?>" <?= $semester_sumber==$s['id']?'selected':'' ?>>
                <?= esc($s['tahun_pelajaran']) ?> (<?= esc($s['semester']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </form>


      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Pilih</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dataSumber)): ?>
            <?php foreach ($dataSumber as $row): ?>
              <tr>
                <td><input type="checkbox" name="sumber[]" value="<?= esc($row['id']) ?>"></td>
                <td><?= esc($row['nama_siswa']) ?></td>
                <td><?= esc($row['nama_kelas']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Kolom Tengah -->
    <div class="col-md-2 d-flex align-items-center justify-content-center">
      <form action="<?= base_url('admin/plotkelas/save') ?>" method="post">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">Simpan ➡️</button>
      </form>
    </div>

    <!-- Kolom Kanan -->
    <div class="col-md-5">
      <h5>Pilih Semester (Tujuan)</h5>
      <form method="get" class="mb-3">
        <select name="semester_sumber" class="form-select" onchange="this.form.submit()">
            <option value="">-- Pilih Semester --</option>
            <?php foreach ($semesterList as $s): ?>
            <option value="<?= esc($s['id']) ?>" <?= $semester_sumber==$s['id']?'selected':'' ?>>
                <?= esc($s['tahun_pelajaran']) ?> (<?= esc($s['semester']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </form>

      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Pilih</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dataTujuan)): ?>
            <?php foreach ($dataTujuan as $row): ?>
              <tr>
                <td><input type="checkbox" name="tujuan[]" value="<?= esc($row['id']) ?>"></td>
                <td><?= esc($row['nama_siswa']) ?></td>
                <td><?= esc($row['nama_kelas']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

    <div class="mb-3">
      <label for="new_tapel_id" class="form-label">Tapel Baru</label>
      <select name="new_tapel_id" id="new_tapel_id" class="form-select" required>
        <option value="">-- Pilih Tapel Baru --</option>
        <?php foreach ($tapel as $t): ?>
          <option value="<?= $t['id'] ?>"><?= esc($t['tahun_pelajaran']) ?> (<?= esc($t['semester']) ?>)</option>
        <?php endforeach ?>
      </select>
    </div>

    <!-- Kelas (opsional) -->
    <div class="mb-3">
      <label for="kelas_id" class="form-label">Kelas (opsional)</label>
      <select name="kelas_id" id="kelas_id" class="form-select">
        <option value="">-- Semua Kelas --</option>
        <?php foreach ($kelas as $k): ?>
          <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Salin Data</button>
    <a href="<?= base_url('admin/plotkelas') ?>" class="btn btn-secondary">Kembali</a>
  </form>

</div>
<?= $this->endSection() ?>
