<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
<h3 class="mb-3"><i class="bi bi-collection me-2"></i><?= esc($title) ?></h3>

<!-- Notifikasi -->
<?php foreach (['success','error','info'] as $type): ?>
    <?php if (session()->getFlashdata($type)): ?>
        <div class="alert alert-<?= $type ?>"><?= session()->getFlashdata($type) ?></div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- 🔍 Filter -->
<div class="row g-3 mb-4 align-items-end">

    <!-- Generate QR Code (kiri) -->
    <div class="col-md-4">
    <form method="post" action="<?= site_url('admin/qrcode/generate') ?>" class="row g-2 align-items-end">
        <!-- Dropdown kelas lebih kecil -->
        <div class="col-md-8">
            <select name="kelas_id" id="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelasList as $kelas): ?>
                    <option value="<?= $kelas['id'] ?>"><?= esc($kelas['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Tombol generate di samping -->
        <div class="col-md-4 d-grid">
            <button type="submit" class="btn btn-success">⚙️ Generate</button>
        </div>
    </form>
</div>


    <!-- Filter + Export (kanan) -->
    <div class="col-md-8">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="kelas_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $selectedKelas == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="perPage" class="form-select">
                    <?php foreach ([10,25,50,100] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $perPage == $opt ? 'selected' : '' ?>>
                            <?= $opt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary">🔍 Tampilkan</button>
            </div>

            <?php if (!empty($selectedKelas)): ?>
                <div class="col-md-2 d-grid">
                    <a href="<?= base_url('/admin/qrcode/print?kelas_id=' . $selectedKelas) ?>"
                       class="btn btn-danger" target="_blank">
                        📄 Export / Print
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

</div>



<hr>

<!-- Daftar QR Code -->
<h2>Daftar QR Code</h2>
<?php if (!empty($qrCodes)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width:50px;">No</th>
                <th>NIS</th>
                <th>Nama Kelas</th>
                <th>File</th>
                <th>Tanggal Generate</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $no = 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage(); 
        ?>
        <?php foreach ($qrCodes as $qr): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($qr['nis']) ?></td>
                <td><?= esc($qr['nama_kelas']) ?></td>
                <td>
                    <?php if (file_exists($qr['file_path'])): ?>
                        <img src="<?= base_url(str_replace(FCPATH, '', $qr['file_path'])) ?>" width="40">
                    <?php else: ?>
                        <span class="text-danger">File hilang</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($qr['created_at']) ?></td>
                <td>
                    <a href="<?= site_url('admin/qrcode/download/'.$qr['kelas_id']) ?>" 
                       class="btn btn-sm btn-success">Download ZIP</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ✅ Pagination -->
    <div class="mt-4 d-flex justify-content-center">
		    <?= $pager->links('default', 'bootstrap') ?>
	    </div>

<?php else: ?>
    <p>Belum ada QR Code yang digenerate.</p>
<?php endif; ?>
</div>
<?= $this->endSection() ?>
