<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3 class="mb-3 fw-bold"><?= $title ?></h3>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded shadow-sm">

    <div>
        <a href="<?= base_url('admin/siswa-akademik/create') ?>"
           class="btn btn-primary">

            <i class="bi bi-person-plus-fill me-1"></i>
            Plot Akademik Siswa
        </a>
    </div>

    <div class="d-flex align-items-center gap-2">

        <span class="text-secondary small fw-semibold">
            Tahun Pelajaran Aktif :
        </span>

        <span class="badge bg-success px-3 py-2 fs-6">

            <i class="bi bi-calendar-check me-2"></i>

            <?= esc($tapel_aktif['tahun_pelajaran'] ?? '-') ?>

            |

            <?= esc($tapel_aktif['semester'] ?? '-') ?>

        </span>

    </div>

</div>


<!-- INFO -->
<div id="accordion" class="mb-3">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-info text-white">

            <button class="btn btn-link text-white text-decoration-none fw-semibold"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseInfo">

                <i class="bi bi-info-circle me-2"></i>
                Informasi Data Akademik

            </button>

        </div>

        <div id="collapseInfo" class="collapse show">

            <div class="card-body small text-muted">

                Data akademik siswa digunakan sebagai acuan
                absensi QR, izin, laporan kehadiran,
                validasi shift siswa, dan rekap persentase kehadiran.

            </div>

        </div>

    </div>

</div>


<!-- FILTER -->
<div class="card mb-4 border-0 shadow-sm bg-light">

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-8">

                <form method="get" class="d-flex">

                    <input type="hidden"
                           name="perPage"
                           value="<?= $perPage ?>">

                    <div class="input-group">

                        <input type="text"
                               name="keyword"
                               class="form-control"
                               placeholder="Cari nama siswa / NIS..."
                               value="<?= esc($keyword ?? '') ?>">

                        <button class="btn btn-primary">

                            <i class="bi bi-search me-1"></i>
                            Cari

                        </button>

                    </div>

                </form>

            </div>

            <div class="col-md-4">

                <form method="get"
                      class="d-flex justify-content-md-end">

                    <input type="hidden"
                           name="keyword"
                           value="<?= esc($keyword ?? '') ?>">

                    <label class="me-2 mt-2 small">
                        Tampilkan
                    </label>

                    <select name="perPage"
                            onchange="this.form.submit()"
                            class="form-select w-auto">

                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>

                    </select>

                </form>

            </div>

        </div>

    </div>

</div>


<!-- TABLE -->
<div class="table-responsive shadow-sm rounded">

    <table class="table table-bordered table-striped bg-white align-middle">

        <thead class="table-dark text-nowrap">

        <tr>

            <th width="50">No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Jam Belajar</th>
            <th>Jam Sekolah</th>
            <th>Status</th>
            <th width="140">Aksi</th>

        </tr>

        </thead>

        <tbody>

        <?php if (!empty($siswa)): ?>

            <?php
            $no = 1 + (($pager->getCurrentPage() - 1) * $perPage);
            foreach ($siswa as $row):
            ?>

            <tr>

                <td><?= $no++ ?></td>

                <td>
                    <code><?= esc($row['nis']) ?></code>
                </td>

                <td class="fw-semibold">
                    <?= esc($row['nama_siswa']) ?>
                </td>

                <td>

                    <span class="badge bg-secondary">

                        <?= esc($row['nama_kelas']) ?>

                    </span>

                </td>

                <td>
                    <?= esc($row['nama_jurusan']) ?>
                </td>


                <!-- SHIFT dari jam_belajar -->
                <td>

                    <span class="badge bg-info text-dark">

                        <?= ucfirst($row['shift']) ?>

                    </span>

                </td>


                <!-- JAM -->
                <td>

                    <?= date('H:i', strtotime($row['jam_masuk'])) ?>

                    -

                    <?= date('H:i', strtotime($row['jam_pulang'])) ?>

                </td>


                <td class="text-center">

                    <?php if ($row['status_akademik_id'] == 1): ?>

                        <span class="badge bg-success">
                            Aktif
                        </span>

                    <?php else: ?>

                        <span class="badge bg-danger">
                            Non Aktif
                        </span>

                    <?php endif; ?>

                </td>


                <td>

                    <a href="<?= base_url('admin/siswa-akademik/edit/' . $row['id']) ?>"
                       class="btn btn-warning btn-sm">

                        <i class="bi bi-pencil-square"></i>

                    </a>


                    <a href="<?= base_url('admin/siswa-akademik/delete/' . $row['id']) ?>"
                       onclick="return confirm('Hapus data?')"
                       class="btn btn-danger btn-sm">

                        <i class="bi bi-trash"></i>

                    </a>

                </td>

            </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="9"
                    class="text-center py-4 text-muted">

                    Tidak ada data siswa

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>


<!-- PAGINATION -->
<div class="mt-4 d-flex justify-content-center">

    <?= $pager->links('default', 'bootstrap') ?>

</div>

<?= $this->endSection() ?>