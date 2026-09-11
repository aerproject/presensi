<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

  <h3 class="mb-3"><i class="bi bi-collection me-2"></i> Mapping Kelas</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>



  <div class="d-flex justify-content-between align-items-center mb-3">
    <!-- Tombol tambah di kiri -->
    <a href="<?= base_url('admin/mapping/create') ?>" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Tambah Mapping
    </a>

    <!-- Pilihan jumlah data per halaman di kanan -->
    <form method="get" class="d-flex align-items-center">
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
          <i class="bi bi-info-circle me-2"></i> <strong>Informasi Tabel Mapping Kelas</strong>
        </button>
      </h5>
    </div>

      <div id="collapseInfo" class="collapse show" aria-labelledby="headingInfo" data-bs-parent="#accordion">
        <div class="card-body">
          <ul class="mb-0">
            <li><strong>Kelas Lama</strong> menunjukkan kelas asal siswa sebelum pindah kelas atau naik kelas.</li>
            <li><strong>Kelas Baru</strong> menunjukkan kelas tujuan siswa setelah mapping. Jika kosong, berarti siswa sudah <em>Lulus</em>.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>


  <!-- Tabel -->
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Kelas Lama</th>
        <th>Kelas Baru</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php usort($mapping, function($a, $b) { return $a['id'] <=> $b['id']; }); foreach ($mapping as $m): ?>
        <tr>
          <td><?= esc($m['id']) ?></td>
          <td><?= esc($m['kelas_lama']) ?></td>
          <td><?= $m['kelas_baru'] ? esc($m['kelas_baru']) : 'Lulus' ?></td>
          <td>
            <a href="<?= base_url('admin/mapping/edit/'.$m['id']) ?>" class="btn btn-sm btn-warning">
              <i class="bi bi-pencil-square"></i> Edit
            </a>
            <form action="<?= base_url('admin/mapping/delete/'.$m['id']) ?>" method="post" style="display:inline;">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mapping ini?')">
                <i class="bi bi-trash"></i> Hapus
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="mt-4 d-flex justify-content-center">
    <?= $pager->links('default', 'bootstrap') ?>
  </div>
</div>

<?= $this->endSection() ?>
