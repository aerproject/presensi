<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>
            <i class="bi bi-pencil-square me-2"></i>
            Konfirmasi Surat Izin Siswa
        </h4>

        <a href="<?= base_url('admin/izin') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Form Konfirmasi Surat Izin</strong>
        </div>

        <div class="card-body">

            <form action="<?= base_url('admin/izin/update/' . $izin['id']) ?>" method="post">
                <?= csrf_field() ?>

                <!-- hidden field -->
                <input type="hidden" name="siswa_id" value="<?= esc($izin['siswa_id']) ?>">
                <input type="hidden" name="siswa_akademik_id" value="<?= esc($izin['siswa_akademik_id']) ?>">

                <!-- nama siswa -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Siswa</label>
                    <input
                        type="text"
                        class="form-control bg-light"
                        value="<?= esc($izin['nama_siswa']) ?>"
                        readonly>
                </div>

                <!-- kelas -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelas</label>
                    <input
                        type="text"
                        class="form-control bg-light"
                        value="<?= esc($izin['nama_kelas'] ?? '-') ?>"
                        readonly>
                </div>

                <!-- jurusan -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Jurusan</label>
                    <input
                        type="text"
                        class="form-control bg-light"
                        value="<?= esc($izin['nama_jurusan'] ?? '-') ?>"
                        readonly>
                </div>

                <!-- tanggal ketidakhadiran -->
                <div class="mb-3">
                    <label for="tanggal_absensi" class="form-label fw-bold">
                        Tanggal Ketidakhadiran
                    </label>
                    <input
                        type="date"
                        name="tanggal_absensi"
                        id="tanggal_absensi"
                        class="form-control"
                        value="<?= esc($izin['tanggal_absensi'] ?? '') ?>"
                        required>
                    <div class="form-text text-muted">
                        Tanggal siswa sebenarnya tidak hadir.
                    </div>
                </div>

                <!-- jenis izin -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Surat</label>
                    <input
                        type="text"
                        class="form-control bg-light"
                        value="<?= ucfirst($izin['jenis_izin']) ?>"
                        readonly>
                </div>

                <!-- isi pesan -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea
                        class="form-control bg-light"
                        rows="3"
                        readonly><?= esc($izin['isi_pesan']) ?></textarea>
                </div>

                <!-- file upload -->
                <div class="mb-3">
                    <label class="form-label fw-bold">File Surat</label>

                    <div>
                        <?php if (!empty($izin['upload_surat'])): ?>
                            <a
                                href="<?= base_url('uploads/surat_izin/' . $izin['upload_surat']) ?>"
                                target="_blank"
                                class="btn btn-outline-primary btn-sm">

                                <i class="bi bi-paperclip me-1"></i>
                                Lihat File
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada file</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- status -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Status Persetujuan</label>

                    <select name="status_izin" class="form-select" required>
                        <option value="diajukan"
                            <?= $izin['status_izin'] == 'diajukan' ? 'selected' : '' ?>>
                            Diajukan
                        </option>

                        <option value="disetujui"
                            <?= $izin['status_izin'] == 'disetujui' ? 'selected' : '' ?>>
                            Disetujui
                        </option>

                        <option value="ditangguhkan"
                            <?= $izin['status_izin'] == 'ditangguhkan' ? 'selected' : '' ?>>
                            Ditangguhkan
                        </option>

                        <option value="ditolak"
                            <?= $izin['status_izin'] == 'ditolak' ? 'selected' : '' ?>>
                            Ditolak
                        </option>
                    </select>
                </div>

                <!-- button -->
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>

                    <a href="<?= base_url('admin/izin') ?>" class="btn btn-secondary">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>