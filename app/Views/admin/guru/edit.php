<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-person-badge me-2"></i>
                Edit Guru
            </h3>
            <div class="text-muted">
                Perbarui data guru, akun login, dan tugas wali kelas.
            </div>
        </div>

        <a href="<?= base_url('admin/guru') ?>"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>


    <?php if (session()->getFlashdata('errors')): ?>

        <div class="alert alert-danger">

            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Periksa data berikut:
            </div>

            <ul class="mb-0">

                <?php foreach (
                    session()->getFlashdata('errors')
                    as $error
                ): ?>

                    <li><?= esc($error) ?></li>

                <?php endforeach ?>

            </ul>

        </div>

    <?php endif; ?>


    <form
        action="<?= base_url('admin/guru/update/' . $guru['id']) ?>"
        method="post"
    >

        <?= csrf_field() ?>


        <!-- =====================================================
             DATA GURU
             ===================================================== -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>
                    Data Guru
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8 mb-3">

                        <label
                            for="nama_guru"
                            class="form-label"
                        >
                            Nama Guru <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nama_guru"
                            name="nama_guru"
                            value="<?= old(
                                'nama_guru',
                                $guru['nama_guru']
                            ) ?>"
                            maxlength="100"
                            required
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label
                            for="nip"
                            class="form-label"
                        >
                            NIP
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nip"
                            name="nip"
                            value="<?= old(
                                'nip',
                                $guru['nip']
                            ) ?>"
                            maxlength="30"
                        >

                        <div class="form-text">
                            Opsional, maksimal 30 karakter.
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             AKUN LOGIN
             ===================================================== -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-person-lock me-2"></i>
                    Akun Login
                </h5>

            </div>

            <div class="card-body">

                <?php if (!empty($user)): ?>

                    <div class="alert alert-success">

                        <div class="fw-bold mb-1">
                            <i class="bi bi-check-circle me-1"></i>
                            Guru sudah memiliki akun login
                        </div>

                        <div>
                            Username:
                            <strong><?= esc($user['username']) ?></strong>
                        </div>

                        <div>
                            Role:
                            <strong><?= esc($user['role']) ?></strong>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label
                                for="edit_username"
                                class="form-label"
                            >
                                Username
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_username"
                                name="username"
                                value="<?= old(
                                    'username',
                                    $user['username']
                                ) ?>"
                                maxlength="50"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label
                                for="edit_email"
                                class="form-label"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="edit_email"
                                name="email"
                                value="<?= old(
                                    'email',
                                    $user['email']
                                ) ?>"
                                maxlength="100"
                            >

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label
                                for="edit_password"
                                class="form-label"
                            >
                                Password Baru
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="edit_password"
                                name="password"
                                minlength="6"
                            >

                            <div class="form-text">
                                Kosongkan jika password tidak diubah.
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label
                                for="edit_password_confirm"
                                class="form-label"
                            >
                                Konfirmasi Password Baru
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="edit_password_confirm"
                                name="password_confirm"
                                minlength="6"
                            >

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="buat_akun"
                        value="1"
                    >

                <?php else: ?>

                    <div class="form-check form-switch mb-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="1"
                            id="buat_akun"
                            name="buat_akun"
                            <?= old('buat_akun') === '1'
                                ? 'checked'
                                : '' ?>
                        >

                        <label
                            class="form-check-label"
                            for="buat_akun"
                        >
                            Buat akun login
                        </label>

                    </div>

                    <div
                        id="akun_fields"
                        style="<?= old('buat_akun') === '1'
                            ? ''
                            : 'display:none;' ?>"
                    >

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label
                                    for="new_username"
                                    class="form-label"
                                >
                                    Username
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="new_username"
                                    name="username"
                                    value="<?= old('username') ?>"
                                    maxlength="50"
                                >

                            </div>

                            <div class="col-md-4 mb-3">

                                <label
                                    for="new_email"
                                    class="form-label"
                                >
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="new_email"
                                    name="email"
                                    value="<?= old('email') ?>"
                                    maxlength="100"
                                >

                            </div>

                            <div class="col-md-4 mb-3">

                                <label
                                    for="new_password"
                                    class="form-label"
                                >
                                    Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password"
                                    name="password"
                                    minlength="6"
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="new_password_confirm"
                                class="form-label"
                            >
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="new_password_confirm"
                                name="password_confirm"
                                minlength="6"
                            >

                        </div>

                    </div>

                <?php endif ?>

            </div>

        </div>


        <!-- =====================================================
             TUGAS TAMBAHAN
             ===================================================== -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-person-check me-2"></i>
                    Tugas Tambahan
                </h5>

            </div>

            <div class="card-body">


                <?php
                    $waliChecked =
                        !empty($waliKelas) ||
                        old('jadikan_walikelas') === '1';
                ?>


                <div class="form-check form-switch mb-3">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="1"
                        id="jadikan_walikelas"
                        name="jadikan_walikelas"
                        <?= $waliChecked
                            ? 'checked'
                            : '' ?>
                    >

                    <label
                        class="form-check-label fw-semibold"
                        for="jadikan_walikelas"
                    >
                        Jadikan Wali Kelas
                    </label>

                </div>


                <div class="form-text mb-3">

                    Guru wali kelas wajib memiliki akun login
                    dengan role <strong>walikelas</strong>.

                </div>


                <div
                    id="walikelas_fields"
                    style="<?= $waliChecked
                        ? ''
                        : 'display:none;' ?>"
                >

                    <?php if ($tapelAktif): ?>

                        <div class="alert alert-info">

                            <i class="bi bi-calendar-check me-1"></i>

                            Tahun Pelajaran Aktif:
                            <strong>
                                <?= esc(
                                    $tapelAktif['tahun_pelajaran']
                                ) ?>
                            </strong>

                            —
                            
                            <strong>
                                <?= esc(
                                    $tapelAktif['semester']
                                ) ?>
                            </strong>

                        </div>


                        <div class="mb-3">

                            <label
                                for="kelas_id"
                                class="form-label"
                            >
                                Kelas
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="kelas_id"
                                id="kelas_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih Kelas --
                                </option>

                                <?php foreach (
                                    $kelasAktif
                                    as $k
                                ): ?>

                                    <option
                                        value="<?= esc(
                                            $k['kelas_id']
                                        ) ?>"
                                        <?= old(
                                            'kelas_id',
                                            $waliKelas['kelas_id']
                                                ?? ''
                                        ) == $k['kelas_id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= esc(
                                            $k['nama_kelas']
                                        ) ?>
                                    </option>

                                <?php endforeach ?>

                            </select>


                            <?php if (
                                empty($kelasAktif)
                            ): ?>

                                <div class="form-text text-danger">
                                    Tidak ada kelas dengan siswa aktif
                                    pada tahun pelajaran ini.
                                </div>

                            <?php else: ?>

                                <div class="form-text">
                                    Daftar kelas diambil dari
                                    <strong>
                                        siswa_akademik aktif
                                    </strong>.
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


        <!-- =====================================================
             BUTTON
             ===================================================== -->

        <div class="d-flex gap-2 mb-5">

            <button
                type="submit"
                class="btn btn-warning"
            >
                <i class="bi bi-save me-1"></i>
                Simpan Perubahan
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

    const buatAkun =
        document.getElementById('buat_akun');

    const akunFields =
        document.getElementById('akun_fields');

    const jadikanWalikelas =
        document.getElementById('jadikan_walikelas');

    const walikelasFields =
        document.getElementById('walikelas_fields');

    /*
    |--------------------------------------------------------------------------
    | UPDATE AKUN
    |--------------------------------------------------------------------------
    | Hanya form akun BARU yang menggunakan checkbox buat_akun.
    | Jika guru sudah memiliki akun, field akun existing selalu tampil.
    |--------------------------------------------------------------------------
    */

    function updateAkun() {

        if (!buatAkun || !akunFields) {
            return;
        }

        /*
        | Pastikan hanya checkbox yang diproses.
        | Pada akun existing, buat_akun adalah hidden input.
        */
        if (buatAkun.type !== 'checkbox') {
            return;
        }

        const aktif =
            buatAkun.checked ||
            (
                jadikanWalikelas &&
                jadikanWalikelas.checked
            );

        akunFields.style.display =
            aktif ? '' : 'none';

        const username =
            document.getElementById('new_username');

        const email =
            document.getElementById('new_email');

        const password =
            document.getElementById('new_password');

        const passwordConfirm =
            document.getElementById('new_password_confirm');

        if (username) {
            username.required = aktif;
        }

        if (email) {
            email.required = aktif;
        }

        if (password) {
            password.required = aktif;
        }

        if (passwordConfirm) {
            passwordConfirm.required = aktif;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE WALI KELAS
    |--------------------------------------------------------------------------
    */

    function updateWalikelas() {

        if (!jadikanWalikelas || !walikelasFields) {
            return;
        }

        const aktif =
            jadikanWalikelas.checked;

        walikelasFields.style.display =
            aktif ? '' : 'none';

        const kelas =
            document.getElementById('kelas_id');

        if (kelas) {
            kelas.required = aktif;
        }

        /*
        | Jika dijadikan wali kelas,
        | akun login wajib tersedia.
        |
        | Jika belum memiliki akun:
        | aktifkan checkbox Buat akun.
        |
        | Jika sudah memiliki akun:
        | tidak perlu melakukan apa-apa karena
        | hidden buat_akun sudah bernilai 1.
        */
        if (
            aktif &&
            buatAkun &&
            buatAkun.type === 'checkbox'
        ) {
            buatAkun.checked = true;
            updateAkun();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVENT
    |--------------------------------------------------------------------------
    */

    if (
        buatAkun &&
        buatAkun.type === 'checkbox'
    ) {
        buatAkun.addEventListener(
            'change',
            updateAkun
        );
    }

    if (jadikanWalikelas) {
        jadikanWalikelas.addEventListener(
            'change',
            updateWalikelas
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateWalikelas();
    updateAkun();

});

</script>

<?= $this->endSection() ?>
