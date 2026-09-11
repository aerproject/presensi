<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3><?= $title ?></h3>

<!-- ========================= -->
<!-- NOTIFIKASI -->
<!-- ========================= -->
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


<!-- ========================= -->
<!-- INFO TAPEL -->
<!-- ========================= -->
<div class="card mb-3">
    <div class="card-body bg-info text-white d-flex justify-content-between align-items-center">

        <span>
            <strong>Tahun Pelajaran Aktif</strong>
        </span>

        <span>
            <?= $tapelAktif['tahun_pelajaran'] ?> - <?= $tapelAktif['semester'] ?>
        </span>

    </div>
</div>


<!-- ========================= -->
<!-- INFO PROMOTE -->
<!-- ========================= -->
<div class="accordion mb-3" id="accordionPromote">

    <div class="card">

        <div class="card-header bg-warning">

            <button class="btn btn-link text-dark text-decoration-none"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseInfo">

                <strong>Informasi Promosi Kenaikan Kelas</strong>

            </button>

        </div>

        <div id="collapseInfo" class="collapse">

            <div class="card-body">

                <p>Menu ini digunakan untuk proses:</p>

                <ul>
                    <li>Naik kelas siswa</li>
                    <li>Promosi massal per kelas</li>
                    <li>Meluluskan siswa kelas akhir</li>
                    <li>Menyimpan riwayat akademik siswa</li>
                </ul>

                <p>
                    Proses promote ideal dilakukan saat pergantian tahun pelajaran.
                </p>

            </div>

        </div>

    </div>

</div>


<!-- ========================= -->
<!-- PROMOTE MASSAL -->
<!-- ========================= -->
<div class="mb-3">

    <h5>Promosi Massal Per Kelas</h5>

    <?php
    $kelasSudahTampil = [];

    foreach ($siswaAktif as $row):

        if (!in_array($row['kelas_id'], $kelasSudahTampil)):

            $kelasSudahTampil[] = $row['kelas_id'];
    ?>

        <a href="<?= base_url('admin/promote/kelas/' . $row['kelas_id']) ?>"
           class="btn btn-danger btn-sm mb-1"
           onclick="return confirm('Promosikan seluruh siswa kelas <?= $row['nama_kelas'] ?> ?')">

            Promote <?= $row['nama_kelas'] ?>

        </a>

    <?php endif; endforeach; ?>

</div>


<!-- ========================= -->
<!-- TOOLBAR -->
<!-- ========================= -->
<div class="card mb-3">

    <div class="card-body">

        <div class="row">

            <!-- SEARCH -->
            <div class="col-md-8">

                <form method="get" class="d-flex">

                    <input type="hidden" name="perPage" value="<?= $perPage ?>">

                    <input type="text"
                           name="keyword"
                           class="form-control me-2"
                           placeholder="Cari nama siswa / NIS..."
                           value="<?= esc($keyword ?? '') ?>">

                    <button class="btn btn-primary">
                        Cari
                    </button>

                </form>

            </div>


            <!-- PER PAGE -->
            <div class="col-md-4">

                <form method="get" class="d-flex justify-content-end align-items-center">

                    <input type="hidden" name="keyword" value="<?= esc($keyword ?? '') ?>">

                    <label class="me-2 mb-0">Tampilkan</label>

                    <select name="perPage"
                            onchange="this.form.submit()"
                            class="form-select w-auto">

                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>
                            10
                        </option>

                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>
                            25
                        </option>

                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>
                            50
                        </option>

                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>
                            100
                        </option>

                    </select>

                </form>

            </div>

        </div>

    </div>

</div>


<!-- ========================= -->
<!-- TABEL -->
<!-- ========================= -->
<div class="card">

    <div class="card-header bg-primary text-white">

        Daftar Siswa Aktif Tahun Pelajaran Ini

    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>

            <tr>

                <th width="60">No</th>

                <th>NIS</th>

                <th>Nama Siswa</th>

                <th>Kelas</th>

                <th>Status</th>

                <th width="220">Aksi</th>

            </tr>

            </thead>

            <tbody>

            <?php if (!empty($siswaAktif)): ?>

                <?php
                $no = 1 + (($pager->getCurrentPage() - 1) * $perPage);

                foreach ($siswaAktif as $row):
                ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td><?= $row['nis'] ?></td>

                        <td><?= $row['nama_siswa'] ?></td>

                        <td><?= $row['nama_kelas'] ?></td>

                        <td>
                            <span class="badge bg-success">
                                Aktif
                            </span>
                        </td>

                        <td>

                            <a href="<?= base_url('admin/promote/naik/' . $row['siswa_id']) ?>"
                               class="btn btn-success btn-sm">

                                Naik Kelas

                            </a>

                            <a href="<?= base_url('admin/promote/lulus/' . $row['siswa_id']) ?>"
                               class="btn btn-warning btn-sm"
                               onclick="return confirm('Luluskan siswa ini?')">

                                Luluskan

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6" class="text-center">

                        Tidak ada data siswa

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>


        <!-- PAGINATION -->

        <div class="mt-4 d-flex justify-content-center">

            <?= $pager->links('default', 'bootstrap') ?>

        </div>

    </div>

</div>

<?= $this->endSection() ?>