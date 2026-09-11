<?= $this->extend('layouts/walas') ?>
<?= $this->section('content') ?>

<h4 class="mb-4">Notifikasi Absensi</h4>

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
      <h5 class="card-title mb-0">Daftar Notifikasi</h5>
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
          <th>Waktu Kirim</th>
          <th>NIS</th>
          <th>Nama Siswa</th>
          <th>Jenis Pesan</th>
          <th>Isi Pesan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($messages)): ?>
          <?php foreach ($messages as $row): ?>
          <tr>
            <td><?= esc($row['waktu_kirim']) ?></td>
            <td><?= esc($row['nis']) ?></td>
            <td><?= esc($row['nama_siswa']) ?></td>
            <td><?= esc($row['jenis_pesan']) ?></td>
            <td><?= esc($row['isi_pesan']) ?></td>
            <td>
              <?php if ($row['status'] === 'sent'): ?>
                <span class="badge bg-success">Terkirim</span>
              <?php elseif ($row['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php else: ?>
                <span class="badge bg-danger">Gagal</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Belum ada notifikasi absensi</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">
      <?= $pager->links('messages', 'bootstrap') ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
