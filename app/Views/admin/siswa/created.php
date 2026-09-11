<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <div class="mb-3">
        <h4 class="mb-1">Tambah Siswa</h4>
        <div class="text-muted small">
            Tambahkan data siswa dan akun orang tua/wali.
        </div>
    </div>

    <?php
    $errors = session()->getFlashdata('errors') ?? [];
    ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Periksa kembali data berikut:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>


    <form
        action="<?= base_url('/admin/siswa/store') ?>"
        method="post">

        <?= csrf_field() ?>


        <!-- =====================================================
             DATA SISWA
        ====================================================== -->

        <div class="card shadow-sm mb-3">

            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-vcard me-2"></i>
                    Data Siswa
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <!-- NIS -->
                    <div class="col-md-6">

                        <label for="nis" class="form-label">
                            NIS <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nis"
                            id="nis"
                            class="form-control"
                            value="<?= esc(old('nis')) ?>"
                            required>

                        <?php if (!empty($errors['nis'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['nis']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Nama Siswa -->
                    <div class="col-md-6">

                        <label for="nama_siswa" class="form-label">
                            Nama Siswa <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_siswa"
                            id="nama_siswa"
                            class="form-control"
                            value="<?= esc(old('nama_siswa')) ?>"
                            required>

                        <?php if (!empty($errors['nama_siswa'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['nama_siswa']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Password Siswa -->
                    <div class="col-md-6">

                        <label for="password" class="form-label">
                            Password Siswa <span class="text-danger">*</span>
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            minlength="6"
                            required>

                        <div class="form-text">
                            Minimal 6 karakter.
                        </div>

                        <?php if (!empty($errors['password'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['password']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Email Siswa -->
                    <div class="col-md-6">

                        <label for="email" class="form-label">
                            Email Siswa <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="<?= esc(old('email')) ?>"
                            required>

                        <?php if (!empty($errors['email'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['email']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- No HP Siswa -->
                    <div class="col-md-6">

                        <label for="wa_siswa" class="form-label">
                            No HP Siswa <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="wa_siswa"
                            id="wa_siswa"
                            class="form-control"
                            value="<?= esc(old('wa_siswa')) ?>"
                            required>

                        <?php if (!empty($errors['wa_siswa'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['wa_siswa']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Tahun Masuk -->
                    <div class="col-md-6">

                        <label for="tahun_masuk" class="form-label">
                            Tahun Masuk
                        </label>

                        <input
                            type="number"
                            name="tahun_masuk"
                            id="tahun_masuk"
                            class="form-control"
                            min="2000"
                            max="<?= date('Y') ?>"
                            value="<?= esc(old('tahun_masuk')) ?>"
                            placeholder="Contoh: <?= date('Y') ?>">

                        <?php if (!empty($errors['tahun_masuk'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['tahun_masuk']) ?>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </div>


        <!-- =====================================================
             DATA ORANG TUA / WALI
        ====================================================== -->

        <div class="card shadow-sm mb-3">

            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Data Orang Tua / Wali
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <!-- Nama Orang Tua -->
                    <div class="col-md-4">

                        <label for="nama_ortu" class="form-label">
                            Nama Orang Tua / Wali
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_ortu"
                            id="nama_ortu"
                            class="form-control"
                            value="<?= esc(old('nama_ortu')) ?>"
                            required>

                        <?php if (!empty($errors['nama_ortu'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['nama_ortu']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- No HP Orang Tua -->
                    <div class="col-md-4">

                        <label for="wa_ortu" class="form-label">
                            No HP Orang Tua / WhatsApp
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="wa_ortu"
                            id="wa_ortu"
                            class="form-control"
                            value="<?= esc(old('wa_ortu')) ?>"
                            required>

                        <?php if (!empty($errors['wa_ortu'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['wa_ortu']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Email Orang Tua -->
                    <div class="col-md-4">

                        <label for="email_ortu" class="form-label">
                            Email Orang Tua
                        </label>

                        <input
                            type="email"
                            name="email_ortu"
                            id="email_ortu"
                            class="form-control"
                            value="<?= esc(old('email_ortu')) ?>"
                            placeholder="Boleh dikosongkan">

                        <div class="form-text">
                            Jika kosong, email akan dibuat otomatis.
                        </div>

                        <?php if (!empty($errors['email_ortu'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['email_ortu']) ?>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>


                <div class="alert alert-info mt-3 mb-0">

                    <i class="bi bi-info-circle me-2"></i>

                    Username akun orang tua akan dibuat otomatis berdasarkan NIS:
                    <strong>NIS + ortu</strong>.

                    <br>

                    Contoh:
                    <strong>99902ortu</strong>

                </div>

            </div>
        </div>


        <!-- =====================================================
             BUTTON
        ====================================================== -->

        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-success">

                <i class="bi bi-save me-1"></i>
                Simpan

            </button>

            <a
                href="<?= base_url('/admin/siswa') ?>"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali

            </a>

        </div>

    </form>

</div>

<?= $this->endSection() ?>
