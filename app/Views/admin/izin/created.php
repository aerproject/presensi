<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
            <i class="bi bi-file-earmark-plus fs-4"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">Input Surat Izin Manual</h4>
            <p class="text-muted small mb-0">Formulir pencatatan izin dan sakit siswa oleh administrator</p>
        </div>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading fs-6 fw-bold"><i class="bi bi-exclaimation-triangle-fill me-2"></i> Periksa Kembali Isian Anda:</h5>
            <ul class="mb-0 ps-3 small">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="post" action="<?= base_url('admin/izin/store') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <input type="hidden" name="siswa_akademik_id" id="siswa_akademik_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="mb-3">
                            <label for="siswa_id" class="form-label fw-semibold">Cari Siswa</label>
                            <select name="siswa_id" id="siswa_id" class="form-select" required data-cari-url="<?= base_url('admin/izin/cariSiswa') ?>"></select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-semibold">
                                Tanggal Ketidakhadiran
                            </label>
                            <input
                                type="date"
                                name="tanggal"
                                id="tanggal"
                                class="form-control"
                                value="<?= old('tanggal') ?>"
                                required>
                            <div class="form-text text-muted small">
                                Masukkan tanggal siswa sebenarnya tidak hadir.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Detail Informasi Siswa</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row small text-dark">
                                    <div class="col-4 text-secondary">Kelas</div>
                                    <div class="col-8 fw-bold mb-2">: <span id="infoKelas">-</span></div>
                                    
                                    <div class="col-4 text-secondary">Jurusan</div>
                                    <div class="col-8 fw-bold">: <span id="infoJurusan">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="mb-3">
                            <label for="jenis" class="form-label fw-semibold">Jenis Surat</label>
                            <select name="jenis" id="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                <option value="sakit">Surat Sakit (S)</option>
                                <option value="izin">Surat Izin (I)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="upload_surat" class="form-label fw-semibold">Upload Berkas / Surat Dokumen</label>
                            <input type="file" name="upload_surat" id="upload_surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text text-muted small">Format: PDF, JPG, JPEG, PNG (Maks. 2MB)</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="isi_pesan" class="form-label fw-semibold">Keterangan Alasan</label>
                    <textarea name="isi_pesan" id="isi_pesan" class="form-control" rows="4" placeholder="Tulis alasan detail ketidakhadiran siswa di sini..." required></textarea>
                </div>

                <hr class="text-mutedmy-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/izin') ?>" class="btn btn-light px-4 border">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('js/admin-izin-created.js') ?>"></script>

<?= $this->endSection() ?>