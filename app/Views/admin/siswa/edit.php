<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <!-- =======================================================
         HEADER
    ======================================================== -->

    <div class="mb-3">
        <h4 class="mb-1">Edit Siswa</h4>
        <div class="text-muted small">
            Perbarui data siswa dan data orang tua/wali.
        </div>
    </div>


    <!-- =======================================================
         FLASH MESSAGE
    ======================================================== -->

    <?php
    $errors = session()->getFlashdata('errors') ?? [];
    $success = session()->getFlashdata('success');
    $error = session()->getFlashdata('error');
    ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= esc($success) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>


    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= esc($error) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>


    <?php if ($errors): ?>
        <div class="alert alert-danger alert-dismissible fade show">

            <strong>Periksa kembali data berikut:</strong>

            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $message): ?>
                    <li><?= esc($message) ?></li>
                <?php endforeach; ?>
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>
    <?php endif; ?>


    <!-- =======================================================
         FORM
    ======================================================== -->

    <form
        action="<?= base_url('admin/siswa/update/' . $siswa['id']) ?>"
        method="post">

        <?= csrf_field() ?>


        <!-- ===================================================
             DATA SISWA
        ==================================================== -->

        <div class="card shadow-sm border-0 mb-3">

            <div class="card-header bg-white border-bottom">

                <h5 class="mb-0">
                    <i class="bi bi-person-vcard me-2"></i>
                    Data Siswa
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <!-- NIS - READ ONLY -->

                    <div class="col-md-6">

                        <label
                            for="nis"
                            class="form-label">

                            NIS
                        </label>

                        <input
                            type="text"
                            id="nis"
                            class="form-control bg-light"
                            value="<?= esc($siswa['nis'] ?? '') ?>"
                            readonly>

                        <div class="form-text">
                            NIS tidak dapat diubah.
                        </div>

                    </div>


                    <!-- NAMA SISWA -->

                    <div class="col-md-6">

                        <label
                            for="nama_siswa"
                            class="form-label">

                            Nama Siswa
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_siswa"
                            id="nama_siswa"
                            class="form-control"
                            value="<?= esc(old('nama_siswa', $siswa['nama_siswa'] ?? '')) ?>"
                            required>

                        <?php if (!empty($errors['nama_siswa'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['nama_siswa']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- TAHUN MASUK -->

                    <div class="col-md-4">

                        <label
                            for="tahun_masuk"
                            class="form-label">

                            Tahun Masuk

                        </label>

                        <input
                            type="number"
                            name="tahun_masuk"
                            id="tahun_masuk"
                            class="form-control"
                            min="2000"
                            max="<?= date('Y') ?>"
                            value="<?= esc(old('tahun_masuk', $siswa['tahun_masuk'] ?? '')) ?>"
                            placeholder="Contoh: <?= date('Y') ?>">

                        <?php if (!empty($errors['tahun_masuk'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['tahun_masuk']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- NO HP SISWA -->

                    <div class="col-md-4">

                        <label
                            for="wa_siswa"
                            class="form-label">

                            No HP Siswa
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="wa_siswa"
                            id="wa_siswa"
                            class="form-control"
                            value="<?= esc(old('wa_siswa', $siswa['wa_siswa'] ?? '')) ?>"
                            required>

                        <?php if (!empty($errors['wa_siswa'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['wa_siswa']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- KELAS -->

                    <div class="card shadow-sm border-0 mb-3">

            <div class="card-header bg-white border-bottom">

                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Data Orang Tua / Wali
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <!-- USERNAME - READ ONLY -->

                    <div class="col-md-4">

                        <label for="username_ortu" class="form-label">
                            Username Orang Tua
                        </label>

                        <input
                            type="text"
                            id="username_ortu"
                            class="form-control"
                            value="<?= esc($username_ortu ?? '-') ?>"
                            readonly>

                        <div class="form-text">
                            Username akun orang tua tidak dapat diubah.
                        </div>

                    </div>


                    <!-- NAMA ORANG TUA -->

                    <div class="col-md-4">

                        <label
                            for="nama_ortu"
                            class="form-label">

                            Nama Orang Tua / Wali
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_ortu"
                            id="nama_ortu"
                            class="form-control"
                            value="<?= esc(old('nama_ortu', $ortu['nama_ortu'] ?? '')) ?>"
                            required>

                        <?php if (!empty($errors['nama_ortu'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['nama_ortu']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- NO HP ORANG TUA -->

                    <div class="col-md-4">

                        <label
                            for="wa_ortu"
                            class="form-label">

                            No HP Orang Tua / WhatsApp

                        </label>

                        <input
                            type="text"
                            name="wa_ortu"
                            id="wa_ortu"
                            class="form-control"
                            value="<?= esc(old('wa_ortu', $ortu['wa_ortu'] ?? '')) ?>">

                        <?php if (!empty($errors['wa_ortu'])): ?>
                            <div class="text-danger small mt-1">
                                <?= esc($errors['wa_ortu']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                </div>

            </div>

        </div>


        <!-- ===================================================
             ACTION
        ==================================================== -->

        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">

                <i class="bi bi-save me-1"></i>
                Simpan Perubahan

            </button>

            <a
                href="<?= base_url('admin/siswa') ?>"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali

            </a>

        </div>


    </form>

</div>

<?= $this->endSection() ?>
