<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

	<h4>Data Wali Kelas</h4>
	<a href="<?= base_url('/admin/walikelas/create') ?>" class="btn btn-primary mb-3">Tambah Wali Kelas</a>
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
	<div class="table-responsive">
	  <table class="table table-bordered table-hover align-middle">
		<thead class="table-dark text-center">
		  <tr>
			<th width="5%">NO</th>
			<th>Nama Wali Kelas</th>
			<th width="27%">No HP</th>
			<th width="25%">Aksi</th>
		  </tr>
		</thead>
		<tbody>
		  <?php foreach ($walikelas as $index => $w): ?>
		  <tr>
			<td class="text-center"><?= $index + 1 ?></td>
			<td><?= esc($w['nama_walas']) ?></td>
			<td class="text-center"><?= esc($w['nohp_walas']) ?></td>
			<td class="text-center">
			  <a href="<?= base_url('/admin/walikelas/edit/') ?><?= $w['id'] ?>" class="btn btn-sm btn-warning me-1">	
				<i class="bi bi-pencil-square"></i> Edit
			  </a>
			  <a href="<?= base_url('/admin/walikelas/delete/') ?><?= $w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus Wali Kelas ini?')">
				<i class="bi bi-trash"></i> Hapus
			  </a>
			</td>
		  </tr>
		  <?php endforeach ?>
		</tbody>
	  </table>
	</div>






</div>
<?= $this->endSection() ?>