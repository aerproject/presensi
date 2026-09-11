<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-1">Manajemen User</h3>
            <div class="text-muted">Kelola akun pengguna aplikasi presensi.</div>
        </div>
        <a href="<?= base_url('/admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </a>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="get" action="<?= base_url('/admin/users') ?>" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="q" class="form-control"
                           placeholder="Cari username, email, atau role..."
                           value="<?= esc($q) ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= $role === $value ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th style="width:230px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada user ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $i => $row): ?>
                            <?php
                                $roleLabel = $roles[$row['role']] ?? ucfirst((string) $row['role']);
                                $isSelf = (int) session()->get('user_id') === (int) $row['id'];
                                $badge = match ($row['role']) {
                                    'admin' => 'danger',
                                    'operator' => 'warning',
                                    'walikelas' => 'info',
                                    'ortu' => 'success',
                                    default => 'secondary',
                                };
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= esc($row['username']) ?></strong>
                                    <?php if ($isSelf): ?><span class="badge text-bg-success ms-1">Anda</span><?php endif; ?>
                                </td>
                                <td><?= esc($row['email'] ?: '-') ?></td>
                                <td><span class="badge text-bg-<?= $badge ?>"><?= esc($roleLabel) ?></span></td>
                                <td><?= esc($row['created_at'] ?: '-') ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="<?= base_url('/admin/users/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="<?= base_url('/admin/users/password/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-key"></i> Password
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
