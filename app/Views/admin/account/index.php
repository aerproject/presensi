<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<style>
    .account-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px 15px 40px;
    }

    .account-title {
        margin-bottom: 20px;
    }

    .account-title h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 4px;
        color: #172033;
    }

    .account-title p {
        margin: 0;
        color: #667085;
        font-size: .95rem;
    }

    .account-card {
        background: #fff;
        border: 1px solid #e9edf3;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        overflow: hidden;
        margin-bottom: 18px;
    }

    .account-card-header {
        padding: 17px 20px;
        border-bottom: 1px solid #e9edf3;
        background: #fff;
    }

    .account-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #172033;
        margin: 0;
    }

    .account-card-title i {
        color: #0d6efd;
        font-size: 1.3rem;
    }

    .account-card-body {
        padding: 22px 20px;
    }

    /* =========================
       INFORMASI AKUN
       ========================= */

    .account-profile-icon {
        width: 82px;
        height: 82px;
        margin: 2px auto 22px;
        border-radius: 50%;
        background: #eaf2ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
    }

    .account-info-list {
        margin: 0;
        border-top: 1px solid #e9edf3;
    }

    .account-info-row {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 20px;
        padding: 14px 14px;
        border-bottom: 1px solid #e9edf3;
    }

    .account-info-row:last-child {
        border-bottom: 0;
    }

    .account-info-label {
        font-weight: 700;
        color: #172033;
    }

    .account-info-value {
        color: #344054;
        word-break: break-word;
    }

    /* =========================
       FORM PASSWORD
       ========================= */

    .password-info {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #eaf2ff;
        color: #1456a0;
        border-radius: 8px;
        padding: 13px 15px;
        margin-bottom: 22px;
        font-size: .9rem;
        line-height: 1.5;
    }

    .password-info i {
        font-size: 1.1rem;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .password-field {
        position: relative;
    }

    .password-field .form-control {
        padding-right: 46px;
        min-height: 44px;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #667085;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
    }

    .password-toggle:hover {
        color: #0d6efd;
    }

    .form-label {
        font-weight: 500;
        color: #172033;
        margin-bottom: 7px;
    }

    .required {
        color: #dc3545;
    }

    .password-submit {
        width: 100%;
        min-height: 44px;
        font-weight: 500;
    }

    /* =========================
       ALERT
       ========================= */

    .account-alert {
        border-radius: 8px;
        margin-bottom: 18px;
    }

    /* =========================
       RESPONSIVE
       ========================= */

    @media (max-width: 576px) {

        .account-page {
            padding: 18px 10px 30px;
        }

        .account-title h3 {
            font-size: 1.5rem;
        }

        .account-card-header {
            padding: 15px;
        }

        .account-card-body {
            padding: 18px 15px;
        }

        .account-info-row {
            grid-template-columns: 1fr;
            gap: 4px;
            padding: 12px 8px;
        }

        .account-profile-icon {
            width: 72px;
            height: 72px;
            font-size: 2.4rem;
        }
    }
</style>


<div class="account-page">

    <!-- =====================================================
         JUDUL HALAMAN
         ===================================================== -->

    <div class="account-title">

        <h3>
            Akun Saya
        </h3>

        <p>
            Kelola password akun yang sedang digunakan.
        </p>

    </div>


    <!-- =====================================================
         FLASH MESSAGE
         ===================================================== -->

    <?php if ($message = session()->getFlashdata('success')): ?>

        <div
            class="alert alert-success alert-dismissible fade show account-alert"
            role="alert">

            <i class="bi bi-check-circle me-1"></i>

            <?= esc($message) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup">
            </button>

        </div>

    <?php endif; ?>


    <?php if ($message = session()->getFlashdata('error')): ?>

        <div
            class="alert alert-danger alert-dismissible fade show account-alert"
            role="alert">

            <i class="bi bi-exclamation-circle me-1"></i>

            <?= esc($message) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup">
            </button>

        </div>

    <?php endif; ?>


    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <?php if ($errors): ?>

        <div class="alert alert-danger account-alert">

            <div class="fw-semibold mb-1">
                Password belum dapat diubah:
            </div>

            <ul class="mb-0 ps-3">

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?= esc($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         CARD 1 - INFORMASI AKUN
         ===================================================== -->

    <div class="account-card">

        <div class="account-card-header">

            <h5 class="account-card-title">

                <i class="bi bi-person-circle"></i>

                Informasi Akun

            </h5>

        </div>


        <div class="account-card-body">

            <div class="account-profile-icon">

                <i class="bi bi-person"></i>

            </div>


            <div class="account-info-list">

                <div class="account-info-row">

                    <div class="account-info-label">
                        Username
                    </div>

                    <div class="account-info-value">
                        <?= esc($user['username'] ?? '-') ?>
                    </div>

                </div>


                <div class="account-info-row">

                    <div class="account-info-label">
                        Email
                    </div>

                    <div class="account-info-value">
                        <?= esc($user['email'] ?? '-') ?>
                    </div>

                </div>


                <div class="account-info-row">

                    <div class="account-info-label">
                        Role
                    </div>

                    <div class="account-info-value">
                        <?= esc($user['role'] ?? '-') ?>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         CARD 2 - UBAH PASSWORD
         ===================================================== -->

    <div class="account-card">

        <div class="account-card-header">

            <h5 class="account-card-title">

                <i class="bi bi-key-fill"></i>

                Ubah Password

            </h5>

        </div>


        <div class="account-card-body">

            <div class="password-info">

                <i class="bi bi-info-circle-fill"></i>

                <div>
                    Gunakan password yang kuat dan jangan gunakan
                    password yang mudah ditebak.
                </div>

            </div>


            <form
                method="post"
                action="<?= site_url('admin/account/password') ?>">

                <?= csrf_field() ?>


                <!-- PASSWORD LAMA -->

                <div class="mb-3">

                    <label
                        for="current_password"
                        class="form-label">

                        Password Lama
                        <span class="required">*</span>

                    </label>

                    <div class="password-field">

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password lama">

                        <button
                            type="button"
                            class="password-toggle"
                            data-target="current_password"
                            aria-label="Tampilkan password">

                            <i class="bi bi-eye-slash"></i>

                        </button>

                    </div>

                </div>


                <!-- PASSWORD BARU -->

                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label">

                        Password Baru
                        <span class="required">*</span>

                    </label>

                    <div class="password-field">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            minlength="6"
                            required
                            autocomplete="new-password"
                            placeholder="Masukkan password baru">

                        <button
                            type="button"
                            class="password-toggle"
                            data-target="password"
                            aria-label="Tampilkan password">

                            <i class="bi bi-eye-slash"></i>

                        </button>

                    </div>

                    <div class="form-text">
                        Minimal 6 karakter.
                    </div>

                </div>


                <!-- KONFIRMASI PASSWORD -->

                <div class="mb-4">

                    <label
                        for="password_confirm"
                        class="form-label">

                        Konfirmasi Password Baru
                        <span class="required">*</span>

                    </label>

                    <div class="password-field">

                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-control"
                            minlength="6"
                            required
                            autocomplete="new-password"
                            placeholder="Masukkan konfirmasi password baru">

                        <button
                            type="button"
                            class="password-toggle"
                            data-target="password_confirm"
                            aria-label="Tampilkan password">

                            <i class="bi bi-eye-slash"></i>

                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary password-submit">

                    <i class="bi bi-shield-check me-1"></i>

                    Ubah Password

                </button>

            </form>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.password-toggle').forEach(function (button) {

        button.addEventListener('click', function () {

            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (!input) {
                return;
            }

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');

                this.setAttribute(
                    'aria-label',
                    'Sembunyikan password'
                );

            } else {

                input.type = 'password';

                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');

                this.setAttribute(
                    'aria-label',
                    'Tampilkan password'
                );

            }

        });

    });

});
</script>


<?= $this->endSection() ?>