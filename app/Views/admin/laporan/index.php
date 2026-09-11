<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">

    <h3 class="mb-3"><i class="bi bi-clipboard-check me-2"></i>Laporan Kehadiran Siswa</h3>

    <?php
        $selectedKelas = $selectedKelas ?? '';
        $selectedBulan = $selectedBulan ?? date('m');
        $selectedTahun = $selectedTahun ?? date('Y');
    ?>

    <!-- 🔍 Filter -->
    <form method="get" class="row g-3 mb-4">

        <div class="col-md-3">
            <label class="form-label">Kelas</label>
            <select name="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $selectedKelas == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): 
                    $value = str_pad($m, 2, '0', STR_PAD_LEFT);
                ?>
                    <option value="<?= $value ?>" <?= $selectedBulan == $value ? 'selected' : '' ?>>
                        <?= date('F', mktime(0,0,0,$m,1)) ?>
                    </option>
                <?php endfor ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $selectedTahun == $y ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor ?>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-primary mt-4">
                🔍 Tampilkan Data
            </button>
        </div>

        <?php if (!empty($selectedKelas)): ?>
            <div class="col-md-2 d-grid">
                <a href="<?= base_url('/admin/laporan/exportExcel?kelas_id=' . $selectedKelas . '&bulan=' . $selectedBulan . '&tahun=' . $selectedTahun) ?>"
                   class="btn btn-success mt-4">
                    📥 Export Excel
                </a>
            </div>
        <?php endif; ?>

    </form>

    <!-- 📋 Tabel -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Siswa</th>
                    <th width="10%">Hadir</th>
                    <th width="10%">Izin</th>
                    <th width="10%">Sakit</th>
                    <th width="10%">Alpha</th>
                </tr>
            </thead>
            <tbody>

            <?php if (!empty($rekap)): ?>

                <?php 
                    $totalHadir = 0;
                    $totalIzin  = 0;
                    $totalSakit = 0;
                    $totalAlpha = 0;
                ?>

                <?php foreach ($rekap as $index => $r): 
                    $totalHadir += $r['hadir'];
                    $totalIzin  += $r['izin'];
                    $totalSakit += $r['sakit'];
                    $totalAlpha += $r['alpha'];
                ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= esc($r['nama_siswa']) ?></td>
                        <td class="text-center"><?= $r['hadir'] ?></td>
                        <td class="text-center"><?= $r['izin'] ?></td>
                        <td class="text-center"><?= $r['sakit'] ?></td>
                        <td class="text-center"><?= $r['alpha'] ?></td>
                    </tr>
                <?php endforeach ?>

                <!-- TOTAL -->
                <tr class="table-secondary fw-bold">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-center"><?= $totalHadir ?></td>
                    <td class="text-center"><?= $totalIzin ?></td>
                    <td class="text-center"><?= $totalSakit ?></td>
                    <td class="text-center"><?= $totalAlpha ?></td>
                </tr>

            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">
                        Tidak ada data kehadiran untuk periode ini.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>
<?= $this->endSection() ?>