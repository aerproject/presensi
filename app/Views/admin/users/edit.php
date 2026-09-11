<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="mb-3"><h3 class="mb-1">Edit User</h3><div class="text-muted">Perbarui identitas dan role akun.</div></div>

    <?php if ($message = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($message) ?></div><?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0"><div class="card-body">
        <form method="post" action="<?= base_url('/admin/users/update/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" maxlength="50" required value="<?= esc(old('username', $user['username'])) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email', $user['email'])) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= old('role', $user['role']) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="alert alert-light border mt-4 mb-0">
                <i class="bi bi-info-circle me-1"></i> Untuk mengganti password gunakan tombol <strong>Password</strong> dari halaman Manajemen User.
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>
