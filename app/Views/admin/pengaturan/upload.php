<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

  <h3 class="mb-3">
    <i class="bi bi-cloud-upload me-2"></i>
    Upload Data Siswa & Orang Tua
  </h3>

  <!-- Flash Message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <!-- INFO WARNING -->
  <div class="alert alert-warning">
    <strong>Perhatian:</strong>
    Upload dalam jumlah besar (10.000+ data) akan diproses bertahap (chunk 500 data).
    Jangan menutup halaman sampai proses selesai jika server lambat.
  </div>

  <!-- FORM UPLOAD -->
  <div class="card shadow-sm">
    <div class="card-body">

      <form action="<?= base_url('/admin/upload/process') ?>" method="post" enctype="multipart/form-data">

        <div class="mb-3">
          <label for="csv_file" class="form-label">
            Pilih File CSV
          </label>

          <input
            type="file"
            name="csv_file"
            id="csv_file"
            class="form-control"
            accept=".csv"
            required
          >

          <small class="text-muted">
            Format wajib CSV (delimiter <b>;</b>) dengan header:
          </small>

          <div class="mt-2">
            <code>
              nis;password;email;nama_siswa;wa_siswa;kelas_id;jurusan_id;nama_ortu;wa_ortu;email_ortu
            </code>
          </div>
        </div>

        <button type="submit" class="btn btn-success">
          <i class="bi bi-upload me-1"></i>
          Upload Data
        </button>

        <a href="<?= base_url('/admin') ?>" class="btn btn-secondary">
          Kembali
        </a>

        <!-- TEMPLATE DOWNLOAD -->
        <a href="<?= base_url('/admin/upload/template') ?>" class="btn btn-info ms-2">
          <i class="bi bi-download me-1"></i>
          Download Template
        </a>

      </form>
    </div>
  </div>

  <!-- TEMPLATE EXAMPLE -->
  <div class="card mt-4">
    <div class="card-header bg-dark text-white">
      Contoh Data CSV
    </div>

    <div class="card-body">
<pre class="mb-0">
nis;password;email;nama_siswa;wa_siswa;kelas_id;jurusan_id;nama_ortu;wa_ortu;email_ortu
12345;rahasia;siswa@example.com;Budi;628123456789;1;2;Pak Santoso;628987654321;ortu@example.com
12346;rahasia2;siswa2@example.com;Siti;628111222333;1;3;Bu Aminah;628444555666;ortu2@example.com
</pre>
    </div>
  </div>

</div>

<?= $this->endSection() ?>