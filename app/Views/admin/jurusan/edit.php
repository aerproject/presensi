<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

	<div class="container mt-4">
	  <h4 class="mb-4"><i class="bi bi-diagram-3 me-2"></i>Edit Data Jurusan</h4>
	  <?php if (session()->get('errors')): ?>
		  <div class="alert alert-danger">
			<ul class="mb-0">
			  <?php foreach (session()->get('errors') as $error): ?>
				<li><?= esc($error) ?></li>
			  <?php endforeach ?>
			</ul>
		  </div>
		<?php endif; ?>


	  <form action="<?= base_url('/admin/jurusan/update/') ?><?= $jurusan['id'] ?>" method="post" class="needs-validation" novalidate>
		
		<div class="mb-3">
		  <label for="nama_jurusan" class="form-label">Nama Jurusan</label>
		  <input type="text" name="nama_jurusan" id="nama_jurusan" class="form-control" value="<?= esc($jurusan['nama_jurusan']) ?>" required>
		  <div class="invalid-feedback">Nama jurusan wajib diisi.</div>
		</div>

		<div class="mb-3">
		  <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
		  <input type="text" name="kode_jurusan" id="kode_jurusan" class="form-control" value="<?= esc($jurusan['kode_jurusan']) ?>" required>
		  <?php if (isset($errors['kode_jurusan'])): ?>
			<div class="text-danger small"><?= $errors['kode_jurusan'] ?></div>
		  <?php endif; ?>
		</div>

		<button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update</button>
		<a href="<?= base_url('/admin/jurusan') ?>" class="btn btn-secondary ms-2"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
		
	  </form>
	</div>

    <script src="<?= base_url('js/admin-jurusan-edit.js') ?>"></script>
</div>
<?= $this->endSection() ?>