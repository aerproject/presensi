<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Tambah User</h3><div class="text-muted">Buat akun pengguna baru.</div></div>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($message) ?></div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <form method="post" action="<?= base_url('/admin/users/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" maxlength="50" required value="<?= esc(old('username')) ?>">
                    <div class="form-text">3–50 karakter: huruf, angka, titik, underscore, atau tanda hubung.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Pilih role</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= old('role') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan User</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
