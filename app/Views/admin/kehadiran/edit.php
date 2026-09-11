<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>
            <i class="bi bi-pencil-square me-2"></i>
            Edit Kehadiran
        </h3>

        <a href="<?= base_url('admin/kehadiran') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

    </div>


    <div class="card">

        <div class="card-header bg-warning text-dark">

            <strong>Form Edit Kehadiran Siswa</strong>

        </div>

        <div class="card-body">

            <form action="<?= base_url('admin/kehadiran/update/' . $kehadiran['id']) ?>" method="post">

                <?= csrf_field() ?>

                <!-- TANGGAL -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Tanggal</label>

                    <input type="date"
                           class="form-control"
                           name="tanggal"
                           value="<?= $kehadiran['tanggal'] ?>"
                           readonly>

                </div>


                <!-- NAMA SISWA -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Nama Siswa</label>

                    <input type="text"
                           class="form-control bg-light"
                           value="<?= esc($kehadiran['nama_siswa']) ?>"
                           readonly>

                </div>


                <!-- KELAS -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Kelas</label>

                    <input type="text"
                           class="form-control bg-light"
                           value="<?= esc($kehadiran['nama_kelas']) ?>"
                           readonly>

                </div>


                <!-- JAM MASUK -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Jam Masuk</label>

                    <input type="time"
                           name="jam_masuk"
                           class="form-control"
                           value="<?= $kehadiran['jam_masuk'] ?>">

                </div>


                <!-- JAM PULANG -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Jam Pulang</label>

                    <input type="time"
                           name="jam_pulang"
                           class="form-control"
                           value="<?= $kehadiran['jam_pulang'] ?>">

                </div>


                <!-- STATUS -->
                <div class="mb-3">

                    <label class="form-label fw-bold">Status Kehadiran</label>

                    <select name="status" class="form-select">

                        <option value="hadir"
                            <?= $kehadiran['status'] == 'hadir' ? 'selected' : '' ?>>
                            Hadir
                        </option>

                        <option value="izin"
                            <?= $kehadiran['status'] == 'izin' ? 'selected' : '' ?>>
                            Izin
                        </option>

                        <option value="sakit"
                            <?= $kehadiran['status'] == 'sakit' ? 'selected' : '' ?>>
                            Sakit
                        </option>

                        <option value="alpha"
                            <?= $kehadiran['status'] == 'alpha' ? 'selected' : '' ?>>
                            Alpha
                        </option>

                    </select>

                </div>


                <!-- KETERANGAN -->
                <div class="mb-4">

                    <label class="form-label fw-bold">Keterangan</label>

                    <textarea name="keterangan"
                              class="form-control"
                              rows="3"><?= old('keterangan', $kehadiran['keterangan'] ?? '') ?></textarea>

                </div>


                <!-- BUTTON -->
                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-save"></i>
                    Simpan Perubahan

                </button>

                <a href="<?= base_url('admin/kehadiran') ?>"
                   class="btn btn-secondary">

                    Batal

                </a>

            </form>

        </div>

    </div>

</div>

<?= $this->endSection() ?>