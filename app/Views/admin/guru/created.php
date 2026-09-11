<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <h3 class="mb-3">
        <i class="bi bi-person-badge me-2"></i>
        Tambah Guru
    </h3>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Periksa data berikut:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/guru/store') ?>" method="post">
        <?= csrf_field() ?>

        <!-- ================================================= -->
        <!-- DATA GURU -->
        <!-- ================================================= -->

        <div class="card mb-4">
            <div class="card-header">
                <strong>
                    <i class="bi bi-person me-2"></i>
                    Data Guru
                </strong>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label for="nama_guru" class="form-label">
                        Nama Guru <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nama_guru"
                        name="nama_guru"
                        value="<?= old('nama_guru') ?>"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="nip" class="form-label">
                        NIP
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nip"
                        name="nip"
                        value="<?= old('nip') ?>"
                        maxlength="30"
                    >

                    <small class="text-muted">
                        Opsional, maksimal 30 karakter.
                    </small>
                </div>

            </div>
        </div>


        <!-- ================================================= -->
        <!-- AKUN LOGIN -->
        <!-- ================================================= -->

        <div class="card mb-4">
            <div class="card-header">
                <strong>
                    <i class="bi bi-person-lock me-2"></i>
                    Akun Login
                </strong>
            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="buat_akun"
                        name="buat_akun"
                        value="1"
                        <?= old('buat_akun') === '1' ? 'checked' : '' ?>
                    >

                    <label class="form-check-label" for="buat_akun">
                        <strong>Buat akun login</strong>
                    </label>

                    <div class="form-text">
                        Akun login hanya diperlukan jika guru memiliki akses aplikasi.
                    </div>
                </div>


                <div id="akunLoginFields"
                     style="<?= old('buat_akun') === '1' ? '' : 'display:none;' ?>">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Jika guru dijadikan wali kelas, akun login akan otomatis dibuat
                        dengan role <strong>walikelas</strong>.
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                Username <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                value="<?= old('username') ?>"
                                maxlength="50"
                                autocomplete="off"
                            >

                            <small class="text-muted">
                                3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= old('email') ?>"
                                maxlength="100"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                minlength="6"
                                autocomplete="new-password"
                            >

                            <small class="text-muted">
                                Minimal 6 karakter.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password_confirm"
                                name="password_confirm"
                                minlength="6"
                                autocomplete="new-password"
                            >
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- ================================================= -->
        <!-- TUGAS TAMBAHAN -->
        <!-- ================================================= -->

        <div class="card mb-4">

            <div class="card-header">
                <strong>
                    <i class="bi bi-person-workspace me-2"></i>
                    Tugas Tambahan
                </strong>
            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="jadikan_walikelas"
                        name="jadikan_walikelas"
                        value="1"
                        <?= old('jadikan_walikelas') === '1' ? 'checked' : '' ?>
                    >

                    <label
                        class="form-check-label"
                        for="jadikan_walikelas"
                    >
                        <strong>Jadikan Wali Kelas</strong>
                    </label>

                    <div class="form-text">
                        Guru akan mendapatkan akun dengan role
                        <strong>walikelas</strong> dan langsung ditugaskan
                        ke kelas yang dipilih.
                    </div>

                </div>


                <div id="walikelasFields"
                     style="<?= old('jadikan_walikelas') === '1' ? '' : 'display:none;' ?>">

                    <?php if (!empty($tapelAktif)): ?>

                        <div class="alert alert-success">

                            <i class="bi bi-calendar-check me-1"></i>

                            <strong>Tahun Pelajaran Aktif:</strong>

                            <?= esc($tapelAktif['tahun_pelajaran']) ?>

                            —

                            <?= esc($tapelAktif['semester']) ?>

                        </div>


                        <input
                            type="hidden"
                            name="tapel_id"
                            value="<?= esc($tapelAktif['id']) ?>"
                        >


                        <div class="mb-3">

                            <label for="kelas_id" class="form-label">
                                Kelas <span class="text-danger">*</span>
                            </label>

                            <select
                                name="kelas_id"
                                id="kelas_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih Kelas --
                                </option>

                                <?php foreach ($kelasAktif as $k): ?>

                                    <option
                                        value="<?= esc($k['kelas_id']) ?>"
                                        <?= old('kelas_id') == $k['kelas_id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($k['nama_kelas']) ?>
                                    </option>

                                <?php endforeach ?>

                            </select>

                            <?php if (empty($kelasAktif)): ?>

                                <div class="form-text text-danger">
                                    Tidak ada kelas dengan siswa aktif
                                    pada tahun pelajaran ini.
                                </div>

                            <?php else: ?>

                                <div class="form-text">
                                    Daftar kelas diambil dari
                                    <strong>siswa_akademik aktif</strong>.
                                </div>

                            <?php endif ?>

                        </div>


                    <?php else: ?>

                        <div class="alert alert-warning">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            Belum ada tahun pelajaran aktif.
                            Guru tidak dapat ditetapkan sebagai wali kelas
                            sebelum tahun pelajaran diaktifkan.

                        </div>

                    <?php endif ?>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- BUTTON -->
        <!-- ================================================= -->

        <div class="mb-4">

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-save me-1"></i>
                Simpan
            </button>

            <a
                href="<?= base_url('admin/guru') ?>"
                class="btn btn-secondary"
            >
                Batal
            </a>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const buatAkun = document.getElementById('buat_akun');
    const jadikanWalikelas = document.getElementById('jadikan_walikelas');

    const akunFields = document.getElementById('akunLoginFields');
    const walikelasFields = document.getElementById('walikelasFields');

    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');

    function updateAkunFields() {

        const aktif =
            buatAkun.checked ||
            jadikanWalikelas.checked;

        akunFields.style.display = aktif ? '' : 'none';

        username.required = aktif;
        email.required = aktif;
        password.required = aktif;
        passwordConfirm.required = aktif;
    }

    function updateWalikelasFields() {

        const aktif = jadikanWalikelas.checked;

        walikelasFields.style.display = aktif ? '' : 'none';

        if (aktif) {

            /*
             * Wali kelas selalu membutuhkan akun login.
             */
            buatAkun.checked = true;

        }

        updateAkunFields();
    }

    buatAkun.addEventListener('change', function () {

        /*
         * Jangan mematikan akun jika masih wali kelas.
         */
        if (!this.checked && jadikanWalikelas.checked) {
            this.checked = true;
        }

        updateAkunFields();
    });

    jadikanWalikelas.addEventListener(
        'change',
        updateWalikelasFields
    );

    updateWalikelasFields();

});
</script>

<?= $this->endSection() ?>
