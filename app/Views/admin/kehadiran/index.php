<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <h3 class="mb-3">
        <i class="bi bi-clipboard-check me-2"></i>
        Data Kehadiran Siswa
    </h3>


    <!-- ========================= -->
    <!-- FILTER -->
    <!-- ========================= -->
    <div class="card mb-3">

        <div class="card-body">

            <form method="get" class="row g-3 align-items-end">

                <!-- SEARCH -->
                <div class="col-md-3">

                    <label class="form-label small">

                        Cari Nama / NIS

                    </label>

                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Nama atau NIS..."
                           value="<?= esc(service('request')->getGet('keyword')) ?>">

                </div>


                <!-- FILTER KELAS -->
                <div class="col-md-3">

                    <label class="form-label small">

                        Kelas

                    </label>

                    <select name="kelas_id" class="form-select">

                        <option value="">

                            -- Semua Kelas --

                        </option>

                        <?php foreach ($kelasList as $k): ?>

                            <option value="<?= $k['id'] ?>"
                                <?= service('request')->getGet('kelas_id') == $k['id'] ? 'selected' : '' ?>>

                                <?= esc($k['nama_kelas']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- PER PAGE -->
                <div class="col-md-2">

                    <label class="form-label small">

                        Tampilkan

                    </label>

                    <?php $selectedPerPage = service('request')->getGet('perPage') ?? 10; ?>

                    <select name="perPage"
                            class="form-select"
                            onchange="this.form.submit()">

                        <option value="10" <?= $selectedPerPage == 10 ? 'selected' : '' ?>>
                            10 Data
                        </option>

                        <option value="25" <?= $selectedPerPage == 25 ? 'selected' : '' ?>>
                            25 Data
                        </option>

                        <option value="50" <?= $selectedPerPage == 50 ? 'selected' : '' ?>>
                            50 Data
                        </option>

                        <option value="100" <?= $selectedPerPage == 100 ? 'selected' : '' ?>>
                            100 Data
                        </option>

                    </select>

                </div>


                <!-- BUTTON -->
                <div class="col-md-2">

                    <button type="submit" class="btn btn-primary w-100">

                        <i class="bi bi-search"></i> Cari

                    </button>

                </div>


                <!-- RESET -->
                <div class="col-md-2">

                    <a href="<?= current_url() ?>"
                       class="btn btn-outline-secondary w-100">

                        Reset

                    </a>

                </div>

            </form>

        </div>

    </div>


    <!-- ========================= -->
    <!-- FLASH MESSAGE -->
    <!-- ========================= -->
    <?php if (session()->getFlashdata('success')): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?= session()->getFlashdata('success') ?>

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    <?php endif; ?>


    <?php if (session()->getFlashdata('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?= session()->getFlashdata('error') ?>

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    <?php endif; ?>



    <!-- ========================= -->
    <!-- TABLE -->
    <!-- ========================= -->
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark text-center">

                    <tr>

                        <th width="5%">NO</th>

                        <th width="10%">TANGGAL</th>

                        <th width="10%">NIS</th>

                        <th width="22%">NAMA</th>

                        <th width="8%">KELAS</th>

                        <th width="9%">JAM MASUK</th>

                        <th width="9%">JAM PULANG</th>

                        <th width="7%">STATUS</th>

                        <th width="10%">KET</th>

                        <th width="15%">AKSI</th>

                    </tr>

                    </thead>


                    <tbody>

                    <?php if (empty($kehadiran)): ?>

                        <tr>

                            <td colspan="10"
                                class="text-center text-muted">

                                Tidak ada data kehadiran

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php
                        $no = 1 + (($pager->getCurrentPage() - 1) * $perPage);

                        foreach ($kehadiran as $k):
                        ?>

                            <tr>

                                <td class="text-center">

                                    <?= $no++ ?>

                                </td>

                                <td class="text-center">

                                    <?= date('d M Y', strtotime($k['tanggal'])) ?>

                                </td>

                                <td>

                                    <?= esc($k['nis'] ?? '-') ?>

                                </td>

                                <td>

                                    <?= esc($k['nama_siswa'] ?? '-') ?>

                                </td>

                                <td>

                                    <?= esc($k['nama_kelas'] ?? '-') ?>

                                </td>

                                <td class="text-center">

                                    <?= $k['jam_masuk'] ?: '-' ?>

                                </td>

                                <td class="text-center">

                                    <?= $k['jam_pulang'] ?: '-' ?>

                                </td>

                                <td class="text-center">

                                    <?php

                                    $status = strtolower($k['status']);

                                    $badge = 'secondary';

                                    if ($status == 'hadir') {
                                        $badge = 'success';
                                    } elseif ($status == 'izin') {
                                        $badge = 'warning';
                                    } elseif ($status == 'sakit') {
                                        $badge = 'info';
                                    } elseif ($status == 'alpha') {
                                        $badge = 'danger';
                                    }

                                    ?>

                                    <span class="badge bg-<?= $badge ?>">

                                        <?= ucfirst($k['status']) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= esc($k['keterangan'] ?? '-') ?>

                                </td>

                                <td class="text-center">

                                    <a href="<?= base_url('admin/kehadiran/edit/' . $k['id']) ?>"
                                       class="btn btn-sm btn-warning mb-1">

                                        <i class="bi bi-pencil-square"></i>
                                        Edit

                                    </a>



                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- PAGINATION -->
            <div class="mt-3 d-flex justify-content-center">

                <?= $pager->links('default', 'bootstrap') ?>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>