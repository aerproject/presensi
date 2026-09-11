<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <h3 class="mb-3">
        <i class="bi bi-file-earmark-text me-2"></i>
        Data Pengajuan Izin Siswa
    </h3>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">

            <form method="get" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small">Cari Nama Siswa</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Nama siswa..."
                        value="<?= esc(service('request')->getGet('nama')) ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">Tampilkan</label>
                    <select name="perPage" class="form-select" onchange="this.form.submit()">
                        <?php $selected = service('request')->getGet('perPage') ?? 10; ?>

                        <option value="10" <?= $selected == 10 ? 'selected' : '' ?>>10 Data</option>
                        <option value="25" <?= $selected == 25 ? 'selected' : '' ?>>25 Data</option>
                        <option value="50" <?= $selected == 50 ? 'selected' : '' ?>>50 Data</option>
                        <option value="100" <?= $selected == 100 ? 'selected' : '' ?>>100 Data</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="<?= base_url('admin/izin') ?>" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>

                <div class="col-md-3 text-end">
                    <a href="<?= base_url('admin/izin/create') ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Izin
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">NO</th>
                    <th width="10%">TANGGAL</th>
                    <th width="20%">SISWA</th>
                    <th width="10%">KELAS</th>
                    <th width="15%">JURUSAN</th>
                    <th width="8%">JENIS</th>
                    <th width="15%">KETERANGAN</th>
                    <th width="7%">FILE</th>
                    <th width="10%">STATUS</th>
                    <th width="10%">AKSI</th>
                </tr>
            </thead>

            <tbody>

            <?php if (empty($izinList)) : ?>

                <tr>
                    <td colspan="10" class="text-center text-muted">
                        Tidak ada data izin
                    </td>
                </tr>

            <?php else : ?>

                <?php
                    $no = 1 + ($perPage * ($pager->getCurrentPage() - 1));
                ?>

                <?php foreach ($izinList as $izin) : ?>

                    <tr>

                        <td class="text-center"><?= $no++ ?></td>

                        <td class="text-center">
                            <?= !empty($izin['created_at']) ? date('d-m-Y', strtotime($izin['created_at'])) : '-' ?>
                        </td>

                        <td><?= esc($izin['nama_siswa'] ?? '-') ?></td>

                        <td><?= esc($izin['nama_kelas'] ?? '-') ?></td>

                        <td><?= esc($izin['nama_jurusan'] ?? '-') ?></td>

                        <td class="text-center">
                            <?= ucfirst($izin['jenis_izin']) ?>
                        </td>

                        <td>
                            <?= esc($izin['isi_pesan']) ?>
                        </td>

                        <td class="text-center">

                            <?php if (!empty($izin['upload_surat'])) : ?>

                                <a
                                    href="<?= base_url('uploads/surat_izin/' . $izin['upload_surat']) ?>"
                                    target="_blank"
                                    class="btn btn-sm btn-info">

                                    <i class="bi bi-paperclip"></i>

                                </a>

                            <?php else : ?>

                                -

                            <?php endif; ?>

                        </td>

                        <td class="text-center">

                            <?php
                                $badge = 'secondary';

                                if ($izin['status_izin'] == 'disetujui') {
                                    $badge = 'success';
                                } elseif ($izin['status_izin'] == 'diajukan') {
                                    $badge = 'warning';
                                } elseif ($izin['status_izin'] == 'ditolak') {
                                    $badge = 'danger';
                                }
                            ?>

                            <span class="badge bg-<?= $badge ?>">
                                <?= ucfirst($izin['status_izin']) ?>
                            </span>

                        </td>

                        <td class="text-center">

                            <a
                                href="<?= base_url('admin/izin/edit/' . $izin['id']) ?>"
                                class="btn btn-sm btn-warning mb-1">

                                <i class="bi bi-pencil-square"></i>

                            </a>

                            <a
                                href="<?= base_url('admin/izin/delete/' . $izin['id']) ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Hapus data izin ini ?')">

                                <i class="bi bi-trash"></i>

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="mt-3">
        <?= $pager->links('default', 'bootstrap') ?>
    </div>

</div>

<?= $this->endSection() ?>