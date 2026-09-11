<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>Pilih Siswa untuk Presensi</h3>

        <a href="<?= base_url('admin/kehadiran') ?>"
           class="btn btn-secondary">

            Kembali ke Log Kehadiran

        </a>

    </div>


    <!-- FILTER -->
    <div class="card mb-3">

        <div class="card-body">

            <form action="" method="get" class="row g-3 align-items-end">

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

                    <select name="perPage"
                            class="form-select"
                            onchange="this.form.submit()">

                        <?php $selectedPerPage = service('request')->getGet('perPage') ?? 10; ?>

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

                    <button type="submit"
                            class="btn btn-primary w-100">

                        Cari

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


    <!-- TABLE -->
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark text-center">

                    <tr>

                        <th width="5%">No</th>

                        <th width="12%">NIS</th>

                        <th>Nama Siswa</th>

                        <th width="15%">Kelas</th>

                        <th width="20%">Tahun Pelajaran</th>

                        <th width="15%">Aksi</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if (!empty($plotSiswa)): ?>

                        <?php
                        $no = 1 + ($perPage * ($pager->getCurrentPage() - 1));
                        ?>

                        <?php foreach ($plotSiswa as $p): ?>

                            <tr>

                                <td><?= $no++ ?></td>

                                <td><?= esc($p['nis']) ?></td>

                                <td><?= esc($p['nama_siswa']) ?></td>

                                <td><?= esc($p['nama_kelas']) ?></td>

                                <!-- SAFE CHECK -->
                                <td>

                                    <?php if (isset($p['tahun_pelajaran'])): ?>

                                        <?= esc($p['tahun_pelajaran']) ?>

                                        <?php if (isset($p['semester'])): ?>
                                            (<?= esc($p['semester']) ?>)
                                        <?php endif; ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <a href="<?= base_url('admin/kehadiran/presensi/' . $p['id']) ?>"
                                       class="btn btn-success btn-sm">

                                        Input Presensi

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted">

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

        </div>

    </div>

</div>

<?= $this->endSection() ?>