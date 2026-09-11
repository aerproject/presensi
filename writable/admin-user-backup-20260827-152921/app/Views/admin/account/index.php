<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Akun Saya</h3><div class="text-muted">Kelola password akun yang sedang digunakan.</div></div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100"><div class="card-body">
                <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Informasi Akun</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Username</dt><dd class="col-sm-8"><?= esc($user['username']) ?></dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= esc($user['email'] ?: '-') ?></dd>
                    <dt class="col-sm-4">Role</dt><dd class="col-sm-8"><?= esc($user['role']) ?></dd>
                </dl>
            </div></div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0"><div class="card-body">
                <h5 class="mb-3"><i class="bi bi-key me-2"></i>Ubah Password</h5>
                <form method="post" action="<?= base_url('/admin/account/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-shield-check me-1"></i> Ubah Password</button>
                </form>
            </div></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
