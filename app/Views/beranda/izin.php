<?php
// C:\wamp\www\absensiku\app\Views\beranda\izin.php
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h2 class="mb-4 mt-5 text-center">📋 Data Siswa Izin</h2>

<!-- Filter jumlah data -->
<form method="get" class="row g-2 mb-3">
    <div class="col-12 col-md-3">
        <select name="perPage" class="form-select" onchange="this.form.submit()">
            <option value="10" <?= ($perPage == 10 ? 'selected' : '') ?>>10</option>
            <option value="25" <?= ($perPage == 25 ? 'selected' : '') ?>>25</option>
            <option value="50" <?= ($perPage == 50 ? 'selected' : '') ?>>50</option>
            <option value="100" <?= ($perPage == 100 ? 'selected' : '') ?>>100</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <input type="text" name="nama" class="form-control" placeholder="Cari nama siswa..." value="<?= esc($nama ?? '') ?>">
    </div>
    <div class="col-12 col-md-3">
        <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
    </div>
</form>

<!-- Tabel Data Izin -->
<div class="table-responsive">
    <table class="table table-sm table-bordered table-striped shadow-sm align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($izinList)): ?>
                <?php foreach ($izinList as $index => $izin): ?>
                    <tr>
                        <td class="text-center"><?= $offset + $index + 1 ?></td>
                        <td><?= esc($izin['nama_siswa'] ?? '-') ?></td>
                        <td><?= esc($izin['nama_kelas'] ?? '-') ?></td>
                        <td><?= esc($izin['nama_jurusan'] ?? '-') ?></td>
                        <td><?= esc($izin['isi_pesan'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data izin siswa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-3 d-flex justify-content-center">
    <?= $pager->links('default', 'bootstrap') ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Tambahan CSS untuk mobile */
    @media (max-width: 576px) {
        table.table th, table.table td {
            font-size: 0.85rem;
            padding: 6px;
        }
    }
</style>
<?= $this->endSection() ?>
