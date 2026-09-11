<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

	 <h4 class="mb-3">📋 Daftar Siswa</h4>
	
	<a href="<?= base_url('/admin/siswa/create') ?>" class="btn btn-primary mb-3">Tambah Data Siswa</a>
	<?php if (session()->getFlashdata('success')): ?>
	  <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
	<?php endif; ?>
	
		  <!-- 🔍 Filter dan Pencarian -->
<div class="card mb-3">
		<div class="card-body">
			<form action="" method="get" class="row g-3 align-items-end">
				<div class="col-md-3">
					<label class="form-label small">Cari Nama/NIS</label>
					<input type="text" name="keyword" class="form-control" placeholder="Nama atau NIS..." value="<?= service('request')->getGet('keyword') ?>">
				</div>

				<div class="col-md-3">
					<label class="form-label small">Kelas</label>
					<select name="kelas_id" class="form-select">
						<option value="">-- Semua Kelas --</option>
						<?php foreach($kelasList as $k): ?>
							<option value="<?= $k['id'] ?>" <?= service('request')->getGet('kelas_id') == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-md-2">
					<label class="form-label small">Tampilkan</label>
					<select name="perPage" class="form-select" onchange="this.form.submit()">
						<?php $selectedPerPage = service('request')->getGet('perPage') ?? 10; ?>
						<option value="10" <?= $selectedPerPage == 10 ? 'selected' : '' ?>>10 Data</option>
						<option value="25" <?= $selectedPerPage == 25 ? 'selected' : '' ?>>25 Data</option>
						<option value="50" <?= $selectedPerPage == 50 ? 'selected' : '' ?>>50 Data</option>
						<option value="100" <?= $selectedPerPage == 100 ? 'selected' : '' ?>>100 Data</option>
					</select>
				</div>

				<div class="col-md-2">
					<button type="submit" class="btn btn-primary w-100">
						<i class="bi bi-search"></i> Cari
					</button>
				</div>

				<div class="col-md-2">
					<a href="<?= current_url() ?>" class="btn btn-outline-secondary w-100">Reset</a>
				</div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-bordered table-hover align-middle">

    <thead class="table-dark text-center">
        <tr>
            <th style="width: 5%;">NO</th>
            <th style="width: 15%;">NIS</th>
            <th>NAMA</th>
            <th style="width: 12%;">TAHUN</th>
            <th>ORANGTUA</th>
            <th style="width: 25%;">AKSI</th>
        </tr>
    </thead>

    <tbody>

        <?php if (!empty($siswa)): ?>

            <?php foreach ($siswa as $index => $s): ?>

                <tr>

                    <td class="text-center">
                        <?= (($pager->getCurrentPage() - 1) * $perPage) + $index + 1 ?>
                    </td>

                    <td>
                        <?= esc($s['nis'] ?? '-') ?>
                    </td>

                    <td>
                        <?= esc($s['nama_siswa'] ?? '-') ?>
                    </td>

                    <td class="text-center">
                        <?= esc($s['tahun_masuk'] ?? '-') ?>
                    </td>

                    <td>
                        <?= esc($s['nama_ortu'] ?? 'Belum Terdaftar') ?>
                    </td>

                    <td class="text-center">

                        <a
                            href="<?= base_url('/admin/siswa/edit/' . $s['id']) ?>"
                            class="btn btn-sm btn-warning me-1"
                            title="Edit Siswa">

                            <i class="bi bi-pencil-square"></i>
                            Edit

                        </a>

                        <a
                            href="<?= base_url('/admin/siswa/delete/' . $s['id']) ?>"
                            class="btn btn-sm btn-danger me-1"
                            onclick="return confirm('Hapus Siswa ini?')"
                            title="Hapus Siswa">

                            <i class="bi bi-trash"></i>
                            Hapus

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6" class="text-center text-muted py-4">

                    <i class="bi bi-people fs-3 d-block mb-2"></i>

                    Tidak ada data siswa.

                </td>
            </tr>

        <?php endif; ?>

    </tbody>

</table>
	    <div class="mt-4 d-flex justify-content-center">
		    <?= $pager->links('default', 'bootstrap') ?>
	    </div>
	</div>
	

</div>
<?= $this->endSection() ?>