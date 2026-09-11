<?= $this->extend('layouts/walas') ?>
<?= $this->section('content') ?>

<h4 class="mb-4">Data Siswa Kelas Binaan</h4>

<?php if ($kelas): ?>
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title border-bottom pb-2 mb-3">Informasi Kelas</h5>
    <p><strong>Nama Kelas:</strong> <?= esc($kelas['nama_kelas']) ?></p>
    <p><strong>Jumlah Siswa:</strong> <?= count($siswa) ?> orang</p>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title mb-0">Daftar Siswa</h5>
      <!-- Dropdown perPage -->
      <form method="get" class="d-inline">
        <label for="perPage" class="me-2">Tampilkan</label>
        <select name="perPage" id="perPage" onchange="this.form.submit()" class="form-select d-inline-block w-auto">
          <option value="10" <?= $perPage==10?'selected':'' ?>>10</option>
          <option value="25" <?= $perPage==25?'selected':'' ?>>25</option>
          <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
          <option value="100" <?= $perPage==100?'selected':'' ?>>100</option>
        </select>
        <span>data per halaman</span>
      </form>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>NIS</th>
          <th>Nama Siswa</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($siswa)): ?>
          <?php foreach ($siswa as $row): ?>
          <tr>
            <td><?= esc($row['nis']) ?></td>
            <td><?= esc($row['nama_siswa']) ?></td>
            <td><?= ((int)($row['status_akademik_id'] ?? 0) === 1) ? 'Aktif' : 'Tidak Aktif' ?></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="text-center">Belum ada data siswa</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">
      <?= $pager->links('siswa', 'bootstrap') ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
