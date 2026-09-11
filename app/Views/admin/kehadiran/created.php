<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>
            <i class="bi bi-calendar-check"></i>
            Input Presensi Manual
        </h3>

        <a href="<?= base_url('admin/kehadiran/siswa') ?>"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>
            Kembali

        </a>

    </div>


    <!-- CARD -->
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                Form Presensi Siswa

            </h5>

        </div>


        <div class="card-body">

            <!-- DATA SISWA -->
            <div class="row mb-4">

                <div class="col-md-6">

                    <label class="form-label fw-bold">

                        Nama Siswa

                    </label>

                    <input type="text"
                           class="form-control bg-light"
                           value="<?= esc($siswa['nama_siswa']) ?>"
                           readonly>

                </div>


                <div class="col-md-6">

                    <label class="form-label fw-bold">

                        Kelas

                    </label>

                    <input type="text"
                           class="form-control bg-light"
                           value="<?= esc($siswa['nama_kelas']) ?>"
                           readonly>

                </div>

            </div>

            <hr>


            <!-- FORM -->
            <form action="<?= base_url('admin/kehadiran/prestore') ?>"
                  method="post">

                <?= csrf_field() ?>

                <!-- hidden id siswa akademik -->
                <input type="hidden"
                       name="siswa_akademik_id"
                       value="<?= $siswa['id'] ?>">


                <!-- tanggal + jam -->
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label fw-bold">

                            Tanggal

                        </label>

                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>"
                               required>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label fw-bold">

                            Waktu / Jam

                        </label>

                        <input type="time"
                               name="jam_input"
                               class="form-control"
                               value="<?= date('H:i') ?>"
                               required>

                    </div>

                </div>


                <!-- jenis presensi -->
                <div class="mb-3">

                    <label class="form-label fw-bold d-block">

                        Jenis Presensi

                    </label>

                    <div class="form-check form-check-inline">

                        <input class="form-check-input"
                               type="radio"
                               name="jenis_presensi"
                               id="presensi_masuk"
                               value="masuk"
                               checked>

                        <label class="form-check-label"
                               for="presensi_masuk">

                            <span class="badge bg-success">

                                Jam Masuk

                            </span>

                        </label>

                    </div>


                    <div class="form-check form-check-inline">

                        <input class="form-check-input"
                               type="radio"
                               name="jenis_presensi"
                               id="presensi_pulang"
                               value="pulang">

                        <label class="form-check-label"
                               for="presensi_pulang">

                            <span class="badge bg-danger">

                                Jam Pulang

                            </span>

                        </label>

                    </div>


                    <div class="form-text mt-2 text-muted">

                        Pilih <strong>Jam Pulang</strong>
                        jika ingin update kepulangan
                        pada tanggal yang sama.

                    </div>

                </div>


                <!-- status -->
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Status Kehadiran

                    </label>

                    <select name="status"
                            class="form-select"
                            required>

                        <option value="hadir" selected>
                            Hadir
                        </option>

                        <option value="izin">
                            Izin
                        </option>

                        <option value="sakit">
                            Sakit
                        </option>

                        <option value="alpha">
                            Alpha
                        </option>

                    </select>

                </div>


                <!-- keterangan -->
                <div class="mb-4">

                    <label class="form-label fw-bold">

                        Keterangan (Opsional)

                    </label>

                    <textarea name="keterangan"
                              class="form-control"
                              rows="3"
                              placeholder="Tambahkan keterangan jika diperlukan"></textarea>

                </div>


                <!-- tombol -->
                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save"></i>
                        Simpan Presensi

                    </button>


                    <a href="<?= base_url('admin/kehadiran/siswa') ?>"
                       class="btn btn-outline-secondary">

                        Batal

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?= $this->endSection() ?>