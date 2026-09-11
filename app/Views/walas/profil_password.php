<?= $this->extend('layouts/walas') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <div class="mb-4">
        <h4 class="mb-1">Ganti Password</h4>
        <div class="text-muted">
            Ubah password akun Wali Kelas
        </div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="post"
                  action="<?= site_url('walikelas/profil/password/update') ?>">

                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">
                        Password Lama
                    </label>

                    <input type="password"
                           name="current_password"
                           class="form-control"
                           required
                           autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Password Baru
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control"
                           minlength="6"
                           required
                           autocomplete="new-password">

                    <div class="form-text">
                        Minimal 6 karakter.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        Konfirmasi Password Baru
                    </label>

                    <input type="password"
                           name="password_confirm"
                           class="form-control"
                           minlength="6"
                           required
                           autocomplete="new-password">
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= site_url('walikelas/profil') ?>"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Password
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
