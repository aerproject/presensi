<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3">
        <h3 class="mb-1">Reset Password User</h3>
        <div class="text-muted">Atur password baru untuk akun <strong><?= esc($user['username']) ?></strong>.</div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6"><div class="small text-muted">Username</div><strong><?= esc($user['username']) ?></strong></div>
            <div class="col-md-6"><div class="small text-muted">Role</div><strong><?= esc($user['role']) ?></strong></div>
        </div>

        <form method="post" action="<?= base_url('/admin/users/password/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
            </div>
            <div class="alert alert-warning mt-4">
                <i class="bi bi-shield-lock me-1"></i> Password disimpan menggunakan hashing password aplikasi. Password lama tidak dapat ditampilkan.
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-warning"><i class="bi bi-key me-1"></i> Simpan Password</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
