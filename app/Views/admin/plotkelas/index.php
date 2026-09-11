<?= $this->extend('layouts/admin') ?>
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

<!-- Form Filter -->
<form method="get" class="row mb-3">
  <div class="col-md-3">
    <select name="tapel_id" class="form-select" onchange="this.form.submit()">
      <option value="">-- Semua Tahun Pelajaran --</option>
      <?php foreach ($semesterList as $s): ?>
        <option value="<?= esc($s['id']) ?>" <?= $tapel_id==$s['id']?'selected':'' ?>>
          <?= esc($s['tahun_pelajaran']) ?> (<?= esc($s['semester']) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <select name="kelas_id" class="form-select" onchange="this.form.submit()">
      <option value="">-- Semua Kelas --</option>
      <?php foreach ($kelasList as $k): ?>
        <option value="<?= esc($k['id']) ?>" <?= $kelas_id==$k['id']?'selected':'' ?>>
          <?= esc($k['nama_kelas']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4">
    <input type="text" name="keyword" value="<?= esc($keyword) ?>" 
           placeholder="Cari siswa..." class="form-control">
  </div>
  <div class="col-md-2">
    <input type="hidden" name="perPage" value="<?= esc($perPage) ?>">
    <button type="submit" class="btn btn-primary w-100">Filter</button>
  </div>
</form>


  <!-- Tombol tambah + jumlah data per halaman -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('admin/plotkelas/create') ?>" class="btn btn-primary w-25">Tambah Ploat Data</a>

        <!-- Form PerPage -->
        <form method="get" class="d-flex align-items-center">
            <input type="hidden" name="tapel_id" value="<?= esc($tapel_id) ?>">
            <input type="hidden" name="kelas_id" value="<?= esc($kelas_id) ?>">
            <input type="hidden" name="keyword" value="<?= esc($keyword) ?>">

            <label for="perPage" class="me-2 mb-0">Tampilkan</label>
            <select name="perPage" id="perPage" onchange="this.form.submit()" class="form-select w-auto me-2">
                <option value="10"  <?= $perPage==10?'selected':'' ?>>10</option>
                <option value="25"  <?= $perPage==25?'selected':'' ?>>25</option>
                <option value="50"  <?= $perPage==50?'selected':'' ?>>50</option>
                <option value="100" <?= $perPage==100?'selected':'' ?>>100</option>
            </select>
            <span>data per halaman</span>
    </form>
  </div>

    <!-- Collapse Info dengan Accordion -->
  <div id="accordion" class="mb-3">
    <div class="card">
      <div class="card-header bg-info" id="headingInfo">
      <h5 class="mb-0">
        <button class="btn btn-link text-white text-decoration-none" 
                data-bs-toggle="collapse" 
                data-bs-target="#collapseInfo" 
                aria-expanded="true" 
                aria-controls="collapseInfo">
          <i class="bi bi-info-circle me-2"></i><strong> Informasi Plot Data Siswa</strong>
        </button>
      </h5>
    </div>

      <div id="collapseInfo" class="collapse show" aria-labelledby="headingInfo" data-bs-parent="#accordion">
        <div class="card-body">
            <p><strong>Plot Data Siswa</strong> digunakan sebagai acuan aplikasi presensi ini bekerja, terkait dengan proses absensi, proses izin, report/laporan, kenaikaikan kelas, dan kelulusan. <strong>Jadi</strong> silahkan tambahkan data plot data siswa diawal tahun pelajaran atau jika terdapat siswa baru atau pindahan!. </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel Data -->
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Siswa</th>
        <th>Kelas</th>
        <th>Tahun Pelajaran</th>
        <th>Shift</th>
        <th>Jam Masuk</th>
        <th>Jam Pulang</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($plotkelas)): ?>
        <?php foreach ($plotkelas as $row): ?>
          <tr>
            <td><?= esc($row['id']) ?></td>
            <td><?= esc($row['nama_siswa']) ?></td>
            <td><?= esc($row['nama_kelas']) ?></td>
            <td><?= esc($row['tahun_pelajaran']) ?> (<?= esc($row['semester']) ?>)</td>
            <td><?= $row['shift'] ? esc(ucfirst($row['shift'])) : 'Fullday' ?></td>
            <td><?= esc($row['jam_masuk']) ?></td>
            <td><?= esc($row['jam_pulang']) ?></td>
            <td>
              <span class="badge bg-<?= $row['status'] === 'aktif' ? 'success' : 'secondary' ?>">
                <?= esc($row['status']) ?>
              </span>
            </td>
            <td>
              <a href="<?= base_url('admin/plotkelas/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
              <form action="<?= base_url('admin/plotkelas/delete/'.$row['id']) ?>" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="9" class="text-center">Belum ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="mt-4 d-flex justify-content-center">
    <?= $pager->links('default', 'bootstrap') ?>
  </div>
</div>

<?= $this->endSection() ?>
